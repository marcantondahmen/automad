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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Models\Search;

use Automad\Core\Blocks;
use Automad\Core\Str;
use Automad\Core\Value;
use Automad\Models\ComponentCollection;
use Automad\Models\Page;
use Automad\Models\Shared;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Search model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Search {
	const IGNORED = array(
		Fields::HIDDEN,
		Fields::PRIVATE,
		Fields::TEMPLATE,
		Fields::THEME,
		Fields::SYNTAX_THEME,
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

	/**
	 * The ComponentCollection instance.
	 */
	private ComponentCollection $ComponentCollection;

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
	 * Strip tags before performing search.
	 */
	private ?bool $stripTags;

	/**
	 * Initialize a new search model for a search value, optionally used as a regular expression.
	 *
	 * @param string $searchValue
	 * @param bool $isRegex
	 * @param bool $isCaseSensitive
	 * @param array<string, Page> $pages
	 * @param ComponentCollection $ComponentCollection
	 * @param Shared|null $Shared
	 * @param bool|null $stripTags
	 */
	public function __construct(
		string $searchValue,
		bool $isRegex,
		bool $isCaseSensitive,
		array $pages,
		ComponentCollection $ComponentCollection,
		?Shared $Shared,
		?bool $stripTags = false
	) {
		$this->searchValue = preg_quote($searchValue, '/');
		$this->regexFlags = 'ims';
		$this->pages = $pages;
		$this->Shared = $Shared;
		$this->ComponentCollection = $ComponentCollection;
		$this->stripTags = $stripTags;

		if ($isRegex) {
			$this->searchValue = str_replace('/', '\/', $searchValue);
		}

		if ($isCaseSensitive) {
			$this->regexFlags = 'ms';
		}
	}

	/**
	 * Append an item to a given array only in case it is an results.
	 *
	 * @param array<FieldResults> $resultsArray
	 * @param FieldResults|null $results
	 * @return array<FieldResults> the results array
	 */
	public function appendFieldResults(array $resultsArray, ?FieldResults $results): array {
		if (is_a($results, FieldResults::class)) {
			$resultsArray[] = $results;
		}

		return $resultsArray;
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
	 * Perform a search in a single data field and return a
	 * `FieldResults` results for a given search value.
	 *
	 * @param string $field
	 * @param string $content
	 * @return FieldResults|null the field results
	 */
	public function searchTextField(string $field, string $content): ?FieldResults {
		if (preg_match('/^(:|date|checkbox|tags|color)/', $field) || in_array($field, Search::IGNORED)) {
			return null;
		}

		if ($this->stripTags) {
			$content = Str::stripTags($content);
		}

		$contextRegex = '/(?:^|\s).{0,50}' . $this->searchValue . '.{0,50}(?:\s|$)/' . $this->regexFlags;
		$searchRegex = '/' . $this->searchValue . '/' . $this->regexFlags;

		preg_match_all(
			$contextRegex,
			$content,
			$contextMatches,
			PREG_SET_ORDER
		);

		if (!empty($contextMatches[0])) {
			$parts = array();
			$searchMatches = array();

			foreach ($contextMatches as $ctx) {
				$m = array();
				preg_match_all($searchRegex, $ctx[0], $m, PREG_SET_ORDER);

				if (isset($m[0]) && !empty($m[0][0])) {
					$searchMatches = array_merge($searchMatches, array_map(fn (array $item): string => $item[0], $m));
					$parts[] = preg_replace(
						'/\s+/',
						' ',
						trim(preg_replace($searchRegex, '<mark>$0</mark>', htmlspecialchars($ctx[0])))
					);
				}
			}

			$contextString = join(' ... ', $parts);

			return new FieldResults(
				$field,
				$searchMatches,
				$contextString
			);
		}

		return null;
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

		foreach ($data as $field => $raw) {
			$value = '';

			if (str_starts_with($field, '+')) {
				try {
					$value = Blocks::toString($raw['blocks'], $this->ComponentCollection);
				} catch (Exception $error) {
				}
			} else {
				$value = Value::asString($raw);
			}

			$FieldResults = $this->searchTextField($field, $value);

			if (!empty($FieldResults)) {
				$fieldResults[] = $FieldResults;
			}
		}

		return $fieldResults;
	}
}
