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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models\Search;

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\PublicationState;
use Automad\Stores\DataStore;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Replacement model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Replacement {
	/**
	 * The search regex flags.
	 */
	private string $regexFlags;

	/**
	 * The replace value.
	 */
	private string $replaceValue;

	/**
	 * The search value.
	 */
	private string $searchValue;

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
	 * @param array<FileFields> $fileFieldsArray
	 * @return bool true on success
	 */
	public function replaceInFiles(array $fileFieldsArray): bool {
		if (!$this->replaceValue || empty($fileFieldsArray)) {
			Debug::log('No files or replacement string');

			return false;
		}

		foreach ($fileFieldsArray as $FileFields) {
			$DataStore = new DataStore($FileFields->path);

			$draft = $DataStore->getState(PublicationState::DRAFT) ?? array();
			$draft = $this->replaceInData($draft, $FileFields->fields);
			$DataStore->setState(PublicationState::DRAFT, $draft);

			$published = $DataStore->getState(PublicationState::PUBLISHED) ?? array();
			$published = $this->replaceInData($published, $FileFields->fields);
			$DataStore->setState(PublicationState::PUBLISHED, $published);

			$DataStore->save();
		}

		Cache::clear();

		return true;
	}

	/**
	 * Replace matches in block data recursively.
	 *
	 * @param array<array{type: string, data: array}> $blocks
	 * @return array the processed blocks
	 */
	private function replaceInBlocksRecursively(array $blocks): array {
		foreach ($blocks as $index => $block) {
			if ($block['type'] == 'section' && isset($block['data']['content']['blocks'])) {
				$block['data']['content']['blocks'] = $this->replaceInBlocksRecursively($block['data']['content']['blocks']);
			} else {
				foreach ($block['data'] as $key => $value) {
					if (Search::isValidBlockProperty($key)) {
						$block['data'][$key] = $this->replaceInValueRecursively($value);
					}
				}
			}

			$blocks[$index] = $block;
		}

		return $blocks;
	}

	/**
	 * Replace matches in data for a given list of keys.
	 *
	 * @param array<string, mixed> $data
	 * @param array<string> $fields
	 * @return array the processed data array
	 */
	private function replaceInData(array $data, array $fields): array {
		foreach ($fields as $field) {
			if (str_starts_with($field, '+') && is_array($data[$field])) {
				$data[$field]['blocks'] = $this->replaceInBlocksRecursively($data[$field]['blocks']);

				Debug::log($data[$field]['blocks'], 'Blocks');
			} else {
				$data[$field] = $this->replaceString($data[$field]);
			}
		}

		return $data;
	}

	/**
	 * Replace searched string in a value that is either a string or an multidimensional array of strings.
	 *
	 * @param mixed $value
	 * @return mixed $value
	 */
	private function replaceInValueRecursively(mixed $value): mixed {
		if (is_array($value)) {
			$array = array();

			foreach ($value as $key => $item) {
				$array[$key] = $this->replaceInValueRecursively($item);
			}

			return $array;
		}

		return $this->replaceString($value);
	}

	/**
	 * Check whether a value is a string and then perfom a replace.
	 *
	 * @param mixed $value
	 * @return mixed
	 * @psalm-return ($value is string ? string : mixed)
	 */
	private function replaceString(mixed $value): mixed {
		if (is_string($value)) {
			return preg_replace(
				'/' . $this->searchValue . '/' . $this->regexFlags,
				$this->replaceValue,
				$value
			) ?? '';
		}

		return $value;
	}
}
