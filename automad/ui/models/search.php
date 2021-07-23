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
	 *	Replace matches with a given string in a given list of files.
	 *
	 *	@param string $replace
	 *	@param array $files
	 *	@return boolean true on success
	 */

	public function replaceInFiles($replace, $files) {

		return false;

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
				$match = $this->searchBlockField($key, $value);
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

		$SearchDataFieldResults = new SearchDataFieldResults($this->searchValue, $key, $value);

		if ($SearchDataFieldResults->matches) {
			return $SearchDataFieldResults;
		}

		return false;

	}


	/**
	 *	Perform a search in a single block field and return a
	 *	`SearchDataFieldResults` object for a given search value.
	 *
	 *	@return object a `SearchDataFieldResults` object
	 */

	private function searchBlockField($key, $value) {

		$value = str_replace('\\', '', $value);
		$value = strip_tags($value);
		$value = preg_replace('/"time":\s*\d+,/s', ' ', $value);
		$value = preg_replace('/"version":\s*"[\w\.\-]+"/s', ' ', $value);
		$value = preg_replace('/\s+/s', ' ', $value);
		$value = preg_replace('/(\{|,)\s*"\w+"\:/ms', str_repeat(' ', 100), $value);
		$value = preg_replace('/[\[\]\{\}]+/s', ' ', $value);
		$value = trim(str_replace('"', '', $value));

		return $this->searchTextField($key, $value);

	}

	
}