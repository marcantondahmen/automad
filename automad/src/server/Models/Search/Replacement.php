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

use Automad\Core\Blocks;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\PublicationState;
use Automad\Models\ComponentCollection;
use Automad\Stores\DataStore;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Replacement model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Replacement {
	/**
	 * The ComponentCollection instance.
	 */
	private ComponentCollection $ComponentCollection;

	/**
	 * If true also published content is searched.
	 */
	private bool $replaceInPublished;

	/**
	 * The replace value.
	 */
	private string $replaceValue;

	/**
	 * The search regex.
	 */
	private string $searchRegex;

	/**
	 * Initialize a new replacement model.
	 *
	 * @param string $searchValue
	 * @param string $replaceValue
	 * @param bool $isRegex
	 * @param bool $isCaseSensitive
	 * @param ComponentCollection $ComponentCollection
	 * @param bool $replaceInPublished
	 */
	public function __construct(
		string $searchValue,
		string $replaceValue,
		bool $isRegex,
		bool $isCaseSensitive,
		ComponentCollection $ComponentCollection,
		bool $replaceInPublished
	) {
		$this->searchRegex = Replacement::buildRegex($searchValue, $isRegex, $isCaseSensitive);
		$this->replaceValue = $replaceValue;
		$this->ComponentCollection = $ComponentCollection;
		$this->replaceInPublished = $replaceInPublished;
	}

	/**
	 * Build the search regex for replacements.
	 *
	 * @param string $searchValue
	 * @param bool $isRegex
	 * @param bool $isCaseSensitive
	 * @return string
	 */
	public static function buildRegex(string $searchValue, bool $isRegex, bool $isCaseSensitive): string {
		$searchValuePrepared = preg_quote($searchValue, '/');
		$regexFlags = 'ims';

		if ($isRegex) {
			$searchValuePrepared = str_replace('/', '\/', $searchValue);
		}

		if ($isCaseSensitive) {
			$regexFlags = 'ms';
		}

		return '/' . $searchValuePrepared . '/' . $regexFlags;
	}

	/**
	 * Replace a string.
	 *
	 * @param mixed $value
	 * @param string $searchRegex
	 * @param string $replaceValue
	 * @return string
	 */
	public static function replace(string $value, string $searchRegex, string $replaceValue): string {
		if (!is_string($value)) {
			return '';
		}

		return preg_replace(
			$searchRegex,
			$replaceValue,
			$value
		) ?? '';
	}

	/**
	 * Search and replace in selected fields in a data array.
	 *
	 * @param array<string, string> $data
	 * @param array $fields
	 * @param string $searchRegex
	 * @param string $replace
	 * @return array<string, string>
	 */
	public static function replaceInBlockFields(array $data, array $fields, string $searchRegex, string $replace): array {
		foreach ($fields as $field) {
			if (empty($data[$field])) {
				continue;
			}

			$data[$field] = Replacement::replace($data[$field], $searchRegex, $replace);
		}

		return $data;
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

			if ($this->replaceInPublished) {
				$published = $DataStore->getState(PublicationState::PUBLISHED) ?? array();
				$published = $this->replaceInData($published, $FileFields->fields);
				$DataStore->setState(PublicationState::PUBLISHED, $published);
			}

			$DataStore->save();
		}

		Cache::clear();

		return true;
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
				$data[$field]['blocks'] = Blocks::replace(
					$data[$field]['blocks'],
					$this->ComponentCollection,
					$this->searchRegex,
					$this->replaceValue,
					$this->replaceInPublished
				);
			} else {
				$data[$field] = Replacement::replace($data[$field] ?? '', $this->searchRegex, $this->replaceValue);
			}
		}

		return $data;
	}
}
