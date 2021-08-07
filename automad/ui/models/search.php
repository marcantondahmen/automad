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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Models;

use Automad\Core\Str;
use Automad\UI\Models\Search\FieldResults;
use Automad\UI\Models\Search\FileResults;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Search model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Search {
	/**
	 * The Automad results.
	 */
	private $Automad;

	/**
	 * The search regex flags.
	 */
	private $regexFlags;

	/**
	 * The search value.
	 */
	private $searchValue;

	/**
	 * Initialize a new search model for a search value, optionally used as a regular expression.
	 *
	 * @param \Automad\Core\Automad $Automad
	 * @param string $searchValue
	 * @param boolean $isRegex
	 * @param boolean $isCaseSensitive
	 */
	public function __construct($Automad, $searchValue, $isRegex, $isCaseSensitive) {
		$this->Automad = $Automad;
		$this->searchValue = preg_quote($searchValue, '/');
		$this->regexFlags = 'ims';

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
	 * @see \Automad\UI\Models\Search\FileResults
	 * @return array an array of `FileResults`
	 */
	public function searchPerFile() {
		$resultsPerFile = array();

		if (!trim($this->searchValue)) {
			return $resultsPerFile;
		}

		$sharedData = $this->Automad->Shared->data;

		if ($fieldResultsArray = $this->searchData($sharedData)) {
			$path = Str::stripStart(AM_FILE_SHARED_DATA, AM_BASE_DIR);
			$resultsPerFile[] = new FileResults($path, $fieldResultsArray);
		}

		foreach ($this->Automad->getCollection() as $Page) {
			if ($fieldResultsArray = $this->searchData($Page->data)) {
				$path = AM_DIR_PAGES . $Page->path . $Page->get(':template') . '.' . AM_FILE_EXT_DATA;
				$resultsPerFile[] = new FileResults($path, $fieldResultsArray, $Page->origUrl);
			}
		}

		return $resultsPerFile;
	}

	/**
	 * Append an item to a given array only in case it is an results.
	 *
	 * @param array $resultsArray
	 * @param \Automad\UI\Models\Search\FieldResults $results
	 * @return array the results array
	 */
	private function appendFieldResults($resultsArray, $results) {
		if (is_a($results, '\Automad\UI\Models\Search\FieldResults')) {
			$resultsArray[] = $results;
		}

		return $resultsArray;
	}

	/**
	 * Check whether a property name represents a valid block property.
	 *
	 * @param string $property
	 * @return boolean true if the property name is in the whitelist
	 */
	private function isValidBlockProperty($property) {
		$validProperties = array(
			'text',
			'items',
			'content',
			'caption',
			'url',
			'globs',
			'primaryText',
			'primaryLink',
			'secondaryText',
			'secondaryLink',
			'code'
		);

		return in_array($property, $validProperties);
	}

	/**
	 * Merge an array of `FieldResults` into a single results.
	 *
	 * @param string $key
	 * @param array $results
	 * @return \Automad\UI\Models\Search\FieldResults a field results results
	 */
	private function mergeFieldResults($key, $results) {
		$matches = array();
		$contextArray = array();

		foreach ($results as $result) {
			if (is_a($result, '\Automad\UI\Models\Search\FieldResults')) {
				$matches = array_merge($matches, $result->matches);
				$contextArray[] = $result->context;
			}
		}

		if (!empty($matches)) {
			return new FieldResults(
				$key,
				$matches,
				join(' ... ', $contextArray)
			);
		}

		return false;
	}

	/**
	 * Search an array of values recursively.
	 *
	 * @param string $key
	 * @param array $array
	 * @return \Automad\UI\Models\Search\FieldResults a field results results
	 */
	private function searchArrayRecursively($key, $array) {
		$results = array();

		foreach ($array as $item) {
			if (is_array($item)) {
				$results = $this->appendFieldResults($results, $this->searchArrayRecursively($key, $item));
			}

			if (is_string($item)) {
				$results = $this->appendFieldResults($results, $this->searchTextField($key, $item));
			}
		}

		return $this->mergeFieldResults($key, $results);
	}

	/**
	 * Perform a search in a block field recursively and return a
	 * `FieldResults` results for a given search value.
	 *
	 * @param string $key
	 * @param results $blocks
	 * @return \Automad\UI\Models\Search\FieldResults a field results results
	 */
	private function searchBlocksRecursively($key, $blocks) {
		$results = array();

		foreach ($blocks as $block) {
			if ($block->type == 'section') {
				$results = $this->appendFieldResults(
					$results,
					$this->searchBlocksRecursively($key, $block->data->content->blocks)
				);
			} else {
				foreach ($block->data as $blockProperty => $value) {
					if ($this->isValidBlockProperty($blockProperty)) {
						if (is_array($value)) {
							$results = $this->appendFieldResults($results, $this->searchArrayRecursively($key, $value));
						}

						if (is_string($value)) {
							$results = $this->appendFieldResults($results, $this->searchTextField($key, $value));
						}
					}
				}
			}
		}

		return $this->mergeFieldResults($key, $results);
	}

	/**
	 * Perform a search in a single data array and return an
	 * array of `FieldResults`.
	 *
	 * @see \Automad\UI\Models\Search\FieldResults
	 * @param array $data
	 * @return array an array of `FieldResults` resultss
	 */
	private function searchData($data) {
		$fieldResultsArray = array();

		foreach ($data as $key => $value) {
			if (strpos($key, '+') === 0) {
				try {
					$data = json_decode($value);
					$FieldResults = $this->searchBlocksRecursively($key, $data->blocks);
				} catch (Exception $error) {
					$FieldResults = false;
				}
			} else {
				$FieldResults = $this->searchTextField($key, $value);
			}

			if (!empty($FieldResults)) {
				$fieldResultsArray[] = $FieldResults;
			}
		}

		return $fieldResultsArray;
	}

	/**
	 * Perform a search in a single data field and return a
	 * `FieldResults` results for a given search value.
	 *
	 * @param string $key
	 * @param string $value
	 * @return \Automad\UI\Models\Search\FieldResults the field results
	 */
	private function searchTextField($key, $value) {
		$ignoredKeys = array(
			AM_KEY_HIDDEN,
			AM_KEY_PRIVATE,
			AM_KEY_THEME,
			AM_KEY_URL,
			AM_KEY_TITLE
		);

		if (preg_match('/^(:|date|checkbox|tags|color)/', $key) || in_array($key, $ignoredKeys)) {
			return false;
		}

		$fieldMatches = array();

		$value = htmlspecialchars($value);

		preg_match_all(
			'/(?P<before>(?:^|\s).{0,50})(?P<match>' . $this->searchValue . ')(?P<after>.{0,50}(?:\s|$))/' . $this->regexFlags,
			$value,
			$matches,
			PREG_SET_ORDER
		);

		if (!empty($matches[0])) {
			$parts = array();

			foreach ($matches as $match) {
				$parts[] = preg_replace(
					'/\s+/',
					' ',
					trim("{$match['before']}<mark>{$match['match']}</mark>{$match['after']}")
				);
			}

			$context = join(' ... ', $parts);

			foreach ($matches as $match) {
				$fieldMatches[] = $match['match'];
			}
		}

		if ($fieldMatches) {
			return new FieldResults(
				$key,
				$fieldMatches,
				$context
			);
		}

		return false;
	}
}
