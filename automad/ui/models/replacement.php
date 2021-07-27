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

use Automad\Core\Debug;
use Automad\Core\Parse;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Replacement model.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Replacement {


	/**
	 *	The search value.
	 */

	private $searchValue;


	/**
	 *	The replace value.
	 */

	private $replaceValue;


	/**
	 *	The search regex flags.
	 */

	private $regexFlags;


	/**
	 *	Initialize a new replacer model.
	 *
	 *	@param string $searchValue
	 *	@param string $replaceValue
	 *	@param boolean $isRegex
	 *	@param boolean $isCaseSensitive
	 */

	public function __construct($searchValue, $replaceValue, $isRegex, $isCaseSensitive) {

		$this->searchValue = preg_quote($searchValue, '/');
		$this->regexFlags = 'ims';
		
		if ($isRegex) {
			$this->searchValue = str_replace('/', '\/', $searchValue);
		}

		if ($isCaseSensitive) {
			$this->regexFlags = 'ms';
		}

		$this->replaceValue = $replaceValue;

	}


	/**
	 *	Replace matches with a given string in a given list of files.
	 *
	 *	@see \Automad\UI\Models\Search\FileKeys
	 *	@param array $fileKeysArray
	 *	@return boolean true on success
	 */

	public function replaceInFiles($fileKeysArray) {

		if (!$this->replaceValue || empty($fileKeysArray)) {
			Debug::log('No files or replacement string');
			return false;
		}

		foreach ($fileKeysArray as $FileKeys) {

			$file = AM_BASE_DIR . $FileKeys->path;
			$data = Parse::textFile($file);
			$data = $this->replaceInData($data, $FileKeys->keys);

			FileSystem::writeData($data, $file);
			
		}

		UICache::rebuild();

	}


	/**
	 *	Replace matches in data for a given list of keys.
	 *
	 *	@param array $data
	 *	@param array $keys
	 *	@return array the processed data array
	 */

	private function replaceInData($data, $keys) {

		foreach ($keys as $key) {

			if (strpos($key, '+') === 0) {

				$fieldData = json_decode($data[$key]);
				$fieldData->blocks = $this->replaceInBlocksRecursively(
					$fieldData->blocks, 
					$this->replaceValue
				);
				$data[$key] = json_encode($fieldData, JSON_PRETTY_PRINT);

				Debug::log($fieldData->blocks, 'Blocks');

			} else {

				$data[$key] = preg_replace(
					'/' . $this->searchValue . '/' . $this->regexFlags,
					$this->replaceValue,
					$data[$key]
				);

			}

		}
		
		return $data;

	}


	/**
	 *	Replace matches in block data recursively.
	 *
	 *	@param object $blocks
	 *	@return object the processed block data
	 */

	private function replaceInBlocksRecursively($blocks) {

		foreach ($blocks as $block) {

			if ($block->type == 'section') {

				$block->data->content->blocks = $this->replaceInBlocksRecursively($block->data->content->blocks);

			} else {

				foreach ($block->data as $key => $value) {

					$block->data->{$key} = $this->replaceInValueRecursively($value);

				}

			}

		}

		return $blocks;

	}


	/**
	 *	Replace searched string in a value that is either a string or an multidimensional array of strings.
	 *
	 *	@param mixed $value
	 *	@return mixed $value
	 */

	private function replaceInValueRecursively($value) {

		if (is_array($value)) {

			$array = array();

			foreach ($value as $item) {
				$array[] = $this->replaceInValueRecursively($item);
			}

			$value = $array;
			
		}

		if (is_string($value)) {

			$value = preg_replace(
				'/' . $this->searchValue . '/' . $this->regexFlags,
				$this->replaceValue,
				$value
			);

		}

		return $value;

	}


}