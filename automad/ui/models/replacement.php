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
use Automad\Core\Str;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Replacer model.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Replacement {


	/**
	 *	The Automad object.
	 */

	private $Automad;


	/**
	 *	The search value.
	 */

	private $searchValue;


	/**
	 *	The replace value.
	 */

	private $replaceValue;


	/**
	 *	Initialize a new replacer model.
	 *
	 *	@param string $searchValue
	 *	@param string $replaceValue
	 *	@param boolean $isRegex
	 */

	public function __construct($searchValue, $replaceValue, $isRegex) {

		$this->Automad = UICache::get();

		if ($isRegex == false) {
			$this->searchValue = preg_quote($searchValue, '/');
		} else {
			$this->searchValue = $searchValue;
		}

		$this->replaceValue = $replaceValue;

	}


	/**
	 *	Replace matches with a given string in a given list of files.
	 *
	 *	@param array $fileKeys
	 *	@return boolean true on success
	 */

	public function replaceInFiles($fileKeys) {

		if (!$this->replaceValue || empty($fileKeys)) {
			Debug::log('No files or replacement string');
			return false;
		}

		foreach ($fileKeys as $file => $keysJson) {

			$file = AM_BASE_DIR . $file;
			$keys = json_decode($keysJson, true);
			$data = Parse::textFile($file);
			$data = $this->replaceInData($data, $keys);

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
					'/' . $this->searchValue . '/is',
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

					if (is_string($value)) {

						$block->data->{$key} = preg_replace(
							'/' . $this->searchValue . '/is',
							$this->replaceValue,
							$block->data->{$key}
						);

					}

				}

			}

		}

		return $blocks;

	}


}