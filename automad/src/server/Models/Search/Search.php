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

use Automad\Core\Str;
use Automad\Core\Value;
use Automad\Models\Page;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Search model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Search {
	/**
	 * The pages array to search in.
	 */
	private array $pagesToSearch;

	/**
	 * The search regex flags.
	 */
	private string $regexFlags;

	/**
	 * The search index instance.
	 */
	private SearchIndex $SearchIndex;

	/**
	 * The search value.
	 */
	private string $searchValue;

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
	 * @param array<string, Page> $pagesToSearch
	 * @param bool|null $stripTags
	 * @param SearchIndexCache $SearchIndexCache
	 */
	public function __construct(
		string $searchValue,
		bool $isRegex,
		bool $isCaseSensitive,
		array $pagesToSearch,
		SearchIndexCache $SearchIndexCache,
		?bool $stripTags = false
	) {
		$this->searchValue = preg_quote($searchValue, '/');
		$this->regexFlags = 'ims';
		$this->pagesToSearch = $pagesToSearch;
		$this->stripTags = $stripTags;
		$this->SearchIndex = $SearchIndexCache->getIndex();

		if ($isRegex) {
			$this->searchValue = str_replace('/', '\/', $searchValue);
		}

		if ($isCaseSensitive) {
			$this->regexFlags = 'ms';
		}
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

		if ($fieldResultsArray = $this->searchData($this->SearchIndex->sharedEntry->fields)) {
			$resultsPerFile[] = new FileResults($fieldResultsArray, null, null);
		}

		foreach ($this->pagesToSearch as $Page) {
			if ($SearchIndexEntry = $this->SearchIndex->getPageEntry($Page->path)) {
				if ($fieldResultsArray = $this->searchData($SearchIndexEntry->fields)) {
					$resultsPerFile[] = new FileResults($fieldResultsArray, $SearchIndexEntry->path, $SearchIndexEntry->origUrl);
				}
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
						trim(preg_replace($searchRegex, '<mark>$0</mark>', htmlspecialchars($ctx[0])) ?? '')
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
	 * @param array<string, string> $data
	 * @return array<FieldResults> an array of `FieldResults` resultss
	 */
	private function searchData(array $data): array {
		$fieldResults = array();

		foreach ($data as $field => $value) {
			$FieldResults = $this->searchTextField($field, $value);

			if (!empty($FieldResults)) {
				$fieldResults[] = $FieldResults;
			}
		}

		return $fieldResults;
	}
}
