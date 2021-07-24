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
use Automad\UI\Models\Search\FieldResults;
use Automad\UI\Models\Search\FileResults;
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
	 *	The search regex flags.
	 */

	private $regexFlags;


	/**
	 *	Initialize a new search model for a search value, optionally used as a regular expression.
	 *
	 *	@param string $searchValue
	 *	@param boolean $isRegex
	 *	@param boolean $isCaseSensitive
	 */

	public function __construct($searchValue, $isRegex, $isCaseSensitive) {

		$this->Automad = UICache::get();
		$this->searchValue = $searchValue;
		$this->regexFlags = 'is';

		if ($isRegex == false) {
			$this->searchValue = preg_quote($searchValue, '/');
		} 

		if ($isCaseSensitive) {
			$this->regexFlags = 's';
		}

	}


	/**
	 *	Perform a search in all data arrays and return an array with `FileResults`.
	 *
	 *	@see \Automad\UI\Models\Search\FileResults
	 *	@return array an array of `FileResults`
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
	 *	Perform a search in a single data array and return an
	 *	array of `\Automad\UI\Models\Search\FieldResults`.
	 *
	 *	@see \Automad\UI\Models\Search\FieldResults
	 *	@param array $data
	 *	@return array an array of `FieldResults` objects
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
	 *	Perform a search in a single data field and return a
	 *	`FieldResults` object for a given search value.
	 *
	 *	@param string $key
	 *	@param string $value
	 *	@return \Automad\UI\Models\Search\FieldResults the field results
	 */

	private function searchTextField($key, $value) {

		if (preg_match('/(:|hidden|private|date|checkbox|tags|color)/', $key)) {
			return false;
		}

		$fieldMatches = array();

		preg_match_all(
			'/(.{0,20})(' . $this->searchValue . ')(.{0,20})/' . $this->regexFlags,
			strip_tags($value),
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
			return new FieldResults(
				$key, 
				$fieldMatches, 
				$context
			);
		}

		return false;

	}


	/**
	 *	Perform a search in a block field recursively and return a
	 *	`FieldResults` object for a given search value.
	 *
	 *	@param string $key
	 *	@param object $blocks
	 *	@return \Automad\UI\Models\Search\FieldResults the field results
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
			return new FieldResults(
				$key,
				$matches,
				join(' ... ', $contextArray)
			);
		}

		return false;
	
	}


}