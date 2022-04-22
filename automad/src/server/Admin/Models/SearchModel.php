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

namespace Automad\Admin\Models;

use Automad\Admin\Models\Search\FieldResultsModel;
use Automad\Admin\Models\Search\FileResultsModel;
use Automad\Core\Automad;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Search model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SearchModel {
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
	 * @param Automad $Automad
	 * @param string $searchValue
	 * @param bool $isRegex
	 * @param bool $isCaseSensitive
	 */
	public function __construct(Automad $Automad, string $searchValue, bool $isRegex, bool $isCaseSensitive) {
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
	 * Perform a search in all data arrays and return an array with `FileResultsModel`.
	 *
	 * @see FileResultsModel
	 * @return array an array of `FileResultsModel`
	 */
	public function searchPerFile() {
		$resultsPerFile = array();

		if (!trim($this->searchValue)) {
			return $resultsPerFile;
		}

		$sharedData = $this->Automad->Shared->data;

		if ($fieldResultsArray = $this->searchData($sharedData)) {
			$path = Str::stripStart(AM_FILE_SHARED_DATA, AM_BASE_DIR);
			$resultsPerFile[] = new FileResultsModel($path, $fieldResultsArray);
		}

		foreach ($this->Automad->getCollection() as $Page) {
			if ($fieldResultsArray = $this->searchData($Page->data)) {
				$path = AM_DIR_PAGES . $Page->path . $Page->get(':template') . '.' . AM_FILE_EXT_DATA;
				$resultsPerFile[] = new FileResultsModel($path, $fieldResultsArray, $Page->origUrl);
			}
		}

		return $resultsPerFile;
	}

	/**
	 * Append an item to a given array only in case it is an results.
	 *
	 * @param array $resultsArray
	 * @param FieldResultsModel|null $results
	 * @return array the results array
	 */
	private function appendFieldResults(array $resultsArray, ?FieldResultsModel $results) {
		if (is_a($results, '\Automad\Admin\Models\Search\FieldResultsModel')) {
			$resultsArray[] = $results;
		}

		return $resultsArray;
	}

	/**
	 * Check whether a property name represents a valid block property.
	 *
	 * @param string $property
	 * @return bool true if the property name is in the whitelist
	 */
	private function isValidBlockProperty(string $property) {
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
	 * Merge an array of `FieldResultsModel` into a single results.
	 *
	 * @param string $field
	 * @param array $results
	 * @return FieldResultsModel|null a field results results
	 */
	private function mergeFieldResults(string $field, array $results) {
		$matches = array();
		$contextArray = array();

		foreach ($results as $result) {
			if (is_a($result, '\Automad\Admin\Models\Search\FieldResultsModel')) {
				$matches = array_merge($matches, $result->matches);
				$contextArray[] = $result->context;
			}
		}

		if (!empty($matches)) {
			return new FieldResultsModel(
				$field,
				$matches,
				join(' ... ', $contextArray)
			);
		}

		return null;
	}

	/**
	 * Search an array of values recursively.
	 *
	 * @param string $field
	 * @param array $array
	 * @return FieldResultsModel a field results results
	 */
	private function searchArrayRecursively(string $field, array $array) {
		$results = array();

		foreach ($array as $item) {
			if (is_array($item) || is_object($item)) {
				$results = $this->appendFieldResults($results, $this->searchArrayRecursively($field, (array) $item));
			}

			if (is_string($item)) {
				$results = $this->appendFieldResults($results, $this->searchTextField($field, $item));
			}
		}

		return $this->mergeFieldResults($field, $results);
	}

	/**
	 * Perform a search in a block field recursively and return a
	 * `FieldResultsModel` results for a given search value.
	 *
	 * @param string $field
	 * @param array $blocks
	 * @return FieldResultsModel|null a field results results
	 */
	private function searchBlocksRecursively(string $field, array $blocks) {
		$results = array();

		foreach ($blocks as $block) {
			if ($block->type == 'section') {
				$results = $this->appendFieldResults(
					$results,
					$this->searchBlocksRecursively($field, $block->data->content->blocks)
				);
			} else {
				foreach ($block->data as $blockProperty => $value) {
					if ($this->isValidBlockProperty($blockProperty)) {
						if (is_array($value) || is_object($value)) {
							$results = $this->appendFieldResults($results, $this->searchArrayRecursively($field, (array) $value));
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
	 * array of `FieldResultsModel`.
	 *
	 * @see FieldResultsModel
	 * @param array $data
	 * @return array an array of `FieldResultsModel` resultss
	 */
	private function searchData(array $data) {
		$fieldResults = array();

		foreach ($data as $field => $value) {
			if (strpos($field, '+') === 0) {
				try {
					$data = json_decode($value);
					$FieldResultsModel = $this->searchBlocksRecursively($field, $data->blocks);
				} catch (Exception $error) {
					$FieldResultsModel = false;
				}
			} else {
				$FieldResultsModel = $this->searchTextField($field, $value);
			}

			if (!empty($FieldResultsModel)) {
				$fieldResults[] = $FieldResultsModel;
			}
		}

		return $fieldResults;
	}

	/**
	 * Perform a search in a single data field and return a
	 * `FieldResultsModel` results for a given search value.
	 *
	 * @param string $field
	 * @param string $value
	 * @return FieldResultsModel the field results
	 */
	private function searchTextField(string $field, string $value) {
		$ignoredKeys = array(
			AM_KEY_HIDDEN,
			AM_KEY_PRIVATE,
			AM_KEY_THEME,
			AM_KEY_URL,
			AM_KEY_TITLE
		);

		if (preg_match('/^(:|date|checkbox|tags|color)/', $field) || in_array($field, $ignoredKeys)) {
			return false;
		}

		$fieldMatches = array();

		preg_match_all(
			'/(?P<before>(?:^|\s).{0,50})(?P<match>' . $this->searchValue . ')(?P<after>.{0,50}(?:\s|$))/' . $this->regexFlags,
			$value,
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
		}

		if ($fieldMatches) {
			return new FieldResultsModel(
				$field,
				$fieldMatches,
				$context
			);
		}

		return null;
	}
}
