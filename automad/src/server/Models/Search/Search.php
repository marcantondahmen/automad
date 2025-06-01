<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models\Search;

use Automad\Core\Str;
use Automad\Core\Value;
use Automad\Models\Page;
use Automad\Models\Shared;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Search model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Search {
	const VALID_BLOCK_PROPERTIES = array(
		'text',
		'items',
		'content',
		'caption',
		'url',
		'link',
		'files',
		'labelAddress',
		'labelSubject',
		'labelBody',
		'labelSend',
		'primaryText',
		'primaryLink',
		'secondaryText',
		'secondaryLink',
		'code',
		'snippet'
	);

	/**
	 * The pages array to search in.
	 */
	private array $pages;

	/**
	 * The search regex flags.
	 */
	private string $regexFlags;

	/**
	 * The search value.
	 */
	private string $searchValue;

	/**
	 * The optional Shared object.
	 */
	private ?Shared $Shared;

	/**
	 * Initialize a new search model for a search value, optionally used as a regular expression.
	 *
	 * @param string $searchValue
	 * @param bool $isRegex
	 * @param bool $isCaseSensitive
	 * @param array<string, Page> $pages
	 * @param Shared|null $Shared
	 */
	public function __construct(string $searchValue, bool $isRegex, bool $isCaseSensitive, array $pages, ?Shared $Shared) {
		$this->searchValue = preg_quote($searchValue, '/');
		$this->regexFlags = 'ims';
		$this->pages = $pages;
		$this->Shared = $Shared;

		if ($isRegex) {
			$this->searchValue = str_replace('/', '\/', $searchValue);
		}

		if ($isCaseSensitive) {
			$this->regexFlags = 'ms';
		}
	}

	/**
	 * Check whether a property name represents a valid block property.
	 *
	 * @param string $property
	 * @return bool true if the property name is in the whitelist
	 */
	public static function isValidBlockProperty(string $property): bool {
		return in_array($property, Search::VALID_BLOCK_PROPERTIES);
	}

	/**
	 * Perform a search in all data arrays and return an array with `FileResults`.
	 *
	 * @see FileResults
	 * @return array<FileResults> an array of `FileResults`
	 */
	public function searchPerFile(): array {
		$resultsPerFile = array();

		if (!trim($this->searchValue)) {
			return $resultsPerFile;
		}

		$sharedData = $this->Shared?->data ?? array();

		if ($fieldResultsArray = $this->searchData($sharedData)) {
			$resultsPerFile[] = new FileResults($fieldResultsArray, null, null);
		}

		foreach ($this->pages as $Page) {
			if ($fieldResultsArray = $this->searchData($Page->data)) {
				$resultsPerFile[] = new FileResults($fieldResultsArray, $Page->path, $Page->origUrl);
			}
		}

		return $resultsPerFile;
	}

	/**
	 * Append an item to a given array only in case it is an results.
	 *
	 * @param array<FieldResults> $resultsArray
	 * @param FieldResults|null $results
	 * @return array<FieldResults> the results array
	 */
	private function appendFieldResults(array $resultsArray, ?FieldResults $results): array {
		if (is_a($results, FieldResults::class)) {
			$resultsArray[] = $results;
		}

		return $resultsArray;
	}

	/**
	 * Merge an array of `FieldResults` into a single results.
	 *
	 * @param string $field
	 * @param array<FieldResults> $results
	 * @return FieldResults|null a field results results
	 */
	private function mergeFieldResults(string $field, array $results): ?FieldResults {
		$matches = array();
		$contextArray = array();

		foreach ($results as $result) {
			$matches = array_merge($matches, $result->matches);
			$contextArray[] = $result->context;
		}

		if (!empty($matches)) {
			return new FieldResults(
				$field,
				$matches,
				join(' ... ', $contextArray)
			);
		}

		return null;
	}

	/**
	 * Perform a search in a block field recursively and return a
	 * `FieldResults` results for a given search value.
	 *
	 * @param string $field
	 * @param array<array{data: array, type: string}>|null $blocks
	 * @return FieldResults|null a field results results
	 */
	private function searchBlocksRecursively(string $field, ?array $blocks): ?FieldResults {
		if (empty($blocks)) {
			return null;
		}

		$results = array();

		foreach ($blocks as $block) {
			if ($block['type'] == 'section' && isset($block['data']['content']['blocks'])) {
				$results = $this->appendFieldResults(
					$results,
					$this->searchBlocksRecursively($field, $block['data']['content']['blocks'])
				);
			} else {
				foreach ($block['data'] as $blockProperty => $value) {
					if (Search::isValidBlockProperty($blockProperty)) {
						if (is_array($value)) {
							$results = $this->appendFieldResults($results, $this->searchDataRecursively($field, $value));
						}

						if (is_string($value)) {
							$results = $this->appendFieldResults($results, $this->searchTextField($field, $value));
						}
					}
				}
			}
		}

		return $this->mergeFieldResults($field, $results);
	}

	/**
	 * Perform a search in a single data array and return an
	 * array of `FieldResults`.
	 *
	 * @see FieldResults
	 * @param array<string, array{blocks: mixed}|string> $data
	 * @return array<FieldResults> an array of `FieldResults` resultss
	 */
	private function searchData(array $data): array {
		$fieldResults = array();

		foreach ($data as $field => $value) {
			if (str_starts_with($field, '+')) {
				/** @var array{blocks: array} $value */
				try {
					$FieldResults = $this->searchBlocksRecursively($field, $value['blocks']);
				} catch (Exception $error) {
					$FieldResults = false;
				}
			} else {
				/** @var ?string $value */
				$FieldResults = $this->searchTextField($field, Value::asString($value));
			}

			if (!empty($FieldResults)) {
				$fieldResults[] = $FieldResults;
			}
		}

		return $fieldResults;
	}

	/**
	 * Search an array of values recursively.
	 *
	 * @param string $field
	 * @param array $array
	 * @return FieldResults|null a field results results
	 */
	private function searchDataRecursively(string $field, array $array): ?FieldResults {
		$results = array();

		foreach ($array as $item) {
			if (is_array($item)) {
				$results = $this->appendFieldResults($results, $this->searchDataRecursively($field, $item));
			}

			if (is_string($item)) {
				$results = $this->appendFieldResults($results, $this->searchTextField($field, $item));
			}
		}

		return $this->mergeFieldResults($field, $results);
	}

	/**
	 * Perform a search in a single data field and return a
	 * `FieldResults` results for a given search value.
	 *
	 * @param string $field
	 * @param string $value
	 * @return FieldResults|null the field results
	 */
	private function searchTextField(string $field, string $value): ?FieldResults {
		$ignoredKeys = array(
			Fields::HIDDEN,
			Fields::PRIVATE,
			Fields::TEMPLATE,
			Fields::THEME,
			Fields::URL,
			Fields::CUSTOM_CONSENT_ACCEPT,
			Fields::CUSTOM_CONSENT_COLOR_BACKGROUND,
			Fields::CUSTOM_CONSENT_COLOR_BORDER,
			Fields::CUSTOM_CONSENT_COLOR_TEXT,
			Fields::CUSTOM_CONSENT_DECLINE,
			Fields::CUSTOM_CONSENT_PLACEHOLDER_COLOR_BACKGROUND,
			Fields::CUSTOM_CONSENT_PLACEHOLDER_COLOR_TEXT,
			Fields::CUSTOM_CONSENT_PLACEHOLDER_TEXT,
			Fields::CUSTOM_CONSENT_REVOKE,
			Fields::CUSTOM_CONSENT_TEXT,
			Fields::CUSTOM_CONSENT_TOOLTIP,
			Fields::CUSTOM_CSS,
			Fields::CUSTOM_HTML_BODY_END,
			Fields::CUSTOM_HTML_HEAD,
			Fields::CUSTOM_JS_BODY_END,
			Fields::CUSTOM_JS_HEAD,
			Fields::CUSTOM_OPEN_GRAPH_IMAGE_COLOR_BACKGROUND,
			Fields::CUSTOM_OPEN_GRAPH_IMAGE_COLOR_TEXT,
		);

		if (preg_match('/^(:|date|checkbox|tags|color)/', $field) || in_array($field, $ignoredKeys)) {
			return null;
		}

		$fieldMatches = array();

		preg_match_all(
			'/(?P<before>(?:^|\s).{0,50})(?P<match>' . $this->searchValue . ')(?P<after>.{0,50}(?:\s|$))/' . $this->regexFlags,
			Str::stripTags($value),
			$matches,
			PREG_SET_ORDER
		);

		if (!empty($matches[0])) {
			$parts = array();

			foreach ($matches as $match) {
				$before = htmlspecialchars($match['before']);
				$marked = htmlspecialchars($match['match']);
				$after = htmlspecialchars($match['after']);

				$parts[] = preg_replace(
					'/\s+/',
					' ',
					trim("$before<mark>$marked</mark>$after")
				);
			}

			$context = join(' ... ', $parts);

			foreach ($matches as $match) {
				$fieldMatches[] = $match['match'];
			}

			return new FieldResults(
				$field,
				$fieldMatches,
				$context
			);
		}

		return null;
	}
}
