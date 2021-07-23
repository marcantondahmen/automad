<?php
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\UI\Models;

use Automad\Core\Str;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Search model.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Search {


	/**
	 *	The Automad object.
	 */

	private $Automad;


	/**
	 *	The search value.
	 */

	private $searchValue;


	/**
	 *	Initialize a new search model for a search value, optionally used as a regular expression.
	 *
	 *	@param string $searchValue
	 *	@param boolean $isRegex
	 */

	public function __construct($searchValue, $isRegex) {

		$this->Automad = UICache::get();

		if ($isRegex == false) {
			$this->searchValue = preg_quote($searchValue, '/');
		} else {
			$this->searchValue = $searchValue;
		}

	}


	/**
	 *	Perform a search in all data arrays and return an associative 
	 *	array with a file path as key and its matches as value.
	 *
	 *	@return array an associative array of file => matches pairs
	 */

	public function searchPerFile() {

		$resultsPerFile = array();
		
		if (!trim($this->searchValue)) {
			return $resultsPerFile;
		}

		$sharedData = $this->Automad->Shared->data;

		if ($resultsPerDataField = $this->searchData($sharedData)) {
			$path = Str::stripStart(AM_FILE_SHARED_DATA, AM_BASE_DIR);
			$resultsPerFile[$path] = $resultsPerDataField;
		}

		foreach ($this->Automad->getCollection() as $Page) {
			if ($resultsPerDataField = $this->searchData($Page->data)) {
				$path = AM_DIR_PAGES . $Page->path . $Page->get(':template') . '.' . AM_FILE_EXT_DATA;
				$resultsPerFile[$path] = $resultsPerDataField;
			}
		}

		return $resultsPerFile;

	}


	/**
	 *	Perform a search in a single data array and return an associative 
	 *	array with a variable field as key and its matches as value.
	 *
	 *	@return array an associative array of field => matches pairs
	 */

	private function searchData($data) {

		$resultsPerDataField = array();

		foreach ($data as $key => $value) {

			if (strpos($key, '+') === 0) {

				try {
					$data = json_decode($value);
					$match = $this->searchBlocksRecursively($key, $data->blocks);
				} catch (Exception $error) {
					$match = false;
				}
				
			} else {
				$match = $this->searchTextField($key, $value);
			}

			if (!empty($match)) {
				$resultsPerDataField[$key] = $match;
			}

		}

		return $resultsPerDataField;

	}


	/**
	 *	Perform a search in a single data field and return a
	 *	`SearchDataFieldResults` object for a given search value.
	 *
	 *	@return object a `SearchDataFieldResults` object
	 */

	private function searchTextField($key, $value) {

		if (preg_match('/(:|hidden|private|date|checkbox|tags|color)/', $key)) {
			return false;
		}

		$fieldMatches = array();

		preg_match_all(
			'/(.{0,20})(' . $this->searchValue . ')(.{0,20})/is',
			$value,
			$matches,
			PREG_SET_ORDER
		);

		if (!empty($matches[0])) {

			$parts = array();

			foreach ($matches as $match) {
				$parts[] = preg_replace('/\s+/', ' ', trim("{$match[1]}<mark>{$match[2]}</mark>{$match[3]}"));
			}

			$context = join(' ... ', $parts);

			foreach ($matches as $match) {
				$fieldMatches[] = $match[2];
			}

		}

		if ($fieldMatches) {
			return new SearchDataFieldResults(
				$key, 
				$this->searchValue, 
				$fieldMatches, 
				$context
			);
		}

		return false;

	}


	/**
	 *	Perform a search in a block field recursively and return a
	 *	`SearchDataFieldResults` object for a given search value.
	 *
	 *	@return object a `SearchDataFieldResults` object
	 */

	private function searchBlocksRecursively($key, $blocks) {

		$results = array();

		foreach ($blocks as $block) {

			if ($block->type == 'section') {

				if ($res = $this->searchBlocksRecursively($key, $block->data->content->blocks)) {
					$results[] = $res;
				}

			} else {

				foreach ($block->data as $value) {

					if (is_string($value)) {
						if ($res = $this->searchTextField($key, $value)) {
							$results[] = $res;
						}
					}
					
				}

			}

		}

		$matches = array();
		$contextArray = array();

		foreach ($results as $result) {
			$matches = array_merge($matches, $result->matches);
			$contextArray[] = $result->context;
		}

		if (!empty($matches)) {
			return new SearchDataFieldResults(
				$key,
				$this->searchValue,
				$matches,
				join(' ... ', $contextArray)
			);
		}

		return false;
	
	}


}