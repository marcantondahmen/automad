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

namespace Automad\Models\Search;

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Parse;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Replacement model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Replacement {
	/**
	 * The search regex flags.
	 */
	private $regexFlags;

	/**
	 * The replace value.
	 */
	private $replaceValue;

	/**
	 * The search value.
	 */
	private $searchValue;

	/**
	 * Initialize a new replacement model.
	 *
	 * @param string $searchValue
	 * @param string $replaceValue
	 * @param bool $isRegex
	 * @param bool $isCaseSensitive
	 */
	public function __construct(string $searchValue, string $replaceValue, bool $isRegex, bool $isCaseSensitive) {
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
	 * Replace matches with a given string in a given list of files.
	 *
	 * @param array $fileFieldsArray
	 * @return bool true on success
	 */
	public function replaceInFiles(array $fileFieldsArray) {
		if (!$this->replaceValue || empty($fileFieldsArray)) {
			Debug::log('No files or replacement string');

			return false;
		}

		foreach ($fileFieldsArray as $FileFields) {
			$file = AM_BASE_DIR . $FileFields->path;
			$data = Parse::dataFile($file);
			$data = $this->replaceInData($data, $FileFields->fields);

			FileSystem::writeData($data, $file);
		}

		Cache::clear();
	}

	/**
	 * Replace matches in block data recursively.
	 *
	 * @param array $blocks
	 * @return array the processed blocks
	 */
	private function replaceInBlocksRecursively(array $blocks) {
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
	 * Replace matches in data for a given list of keys.
	 *
	 * @param array $data
	 * @param array $keys
	 * @return array the processed data array
	 */
	private function replaceInData(array $data, array $keys) {
		foreach ($keys as $key) {
			if (strpos($key, '+') === 0) {
				$fieldData = json_decode($data[$key]);
				$fieldData->blocks = $this->replaceInBlocksRecursively($fieldData->blocks);
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
	 * Replace searched string in a value that is either a string or an multidimensional array of strings.
	 *
	 * @param array|object|string $value
	 * @return mixed $value
	 */
	private function replaceInValueRecursively($value) {
		if (is_array($value) || is_object($value)) {
			$array = array();

			foreach ($value as $key => $item) {
				$array[$key] = $this->replaceInValueRecursively($item);
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
