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
 * Copyright (c) 2017-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\Models\Context;
use Automad\Models\Filelist;
use Automad\Models\Pagelist;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Runtime class handles all variables generated during runtime.
 * Those variables are created by Automad at runtime and independent from the Context and Shared objects.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2017-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Runtime {
	/**
	 * The Context object.
	 */
	private Context $Context;

	/**
	 * The runtime data array.
	 */
	private array $data = array();

	/**
	 * The Filelist object.
	 */
	private Filelist $Filelist;

	/**
	 * The Pagelist object.
	 */
	private Pagelist $Pagelist;

	/**
	 * The constructor.
	 *
	 * @param Filelist $Filelist
	 * @param Pagelist $Pagelist
	 * @param Context $Context
	 */
	public function __construct(Filelist $Filelist, Pagelist $Pagelist, Context $Context) {
		$this->Filelist = $Filelist;
		$this->Pagelist = $Pagelist;
		$this->Context = $Context;

		Debug::log('Created new instance');
	}

	/**
	 * Return the requested runtime variable.
	 *
	 * @param string $key
	 * @return int|string|null The value of $key or NULL if the requested variable doesn't exsist
	 */
	public function get(string $key): int|string|null {
		// Check whether $key is generated within a loop and therefore stored in $this->data or
		// if $key is related to the context, filelist or pagelist object.
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		} else {
			switch ($key) {
				case Fields::NOW:
					// The current date/time.
					return date(DATE_ATOM);

				case Fields::FILELIST_COUNT:
					// The filelist count represents the number of files within the last defined filelist.
					return count($this->Filelist->getFiles());

				case Fields::LANG:
					// The currently active language.
					$lang = AM_I18N_ENABLED ? I18n::get()->getLanguage() : $this->Context->get()->get(Fields::LANG_CUSTOM);

					return $lang ? $lang : 'en';

				case Fields::PAGELIST_COUNT:
					// The pagelist count represents the number of pages within the last defined pagelist, ignoring limit and pagination.
					return count($this->Pagelist->getPages(true));

				case Fields::PAGELIST_DISPLAY_COUNT:
					// The pagelist display count represents the number of pages that will be displayed.
					return count($this->Pagelist->getPages(false));

				case Fields::PAGINATION_COUNT:
					// The last page number of the pagination of the current pagelist.
					return $this->Pagelist->getPaginationCount();

				case Fields::CAPTION:
					// Get the caption for the currently used ":file".
					// In case ":file" is "image.jpg", the parsed caption file is "image.jpg.caption" and the returned value is stored in ":caption".
					if (isset($this->data[Fields::FILE])) {
						return FileUtils::caption(AM_BASE_DIR . $this->data[Fields::FILE]);
					} else {
						return '';
					}

					// no break
				default:
					// Return NULL in case the requested $key doesn't match any runtime variable
					// to distinguish false values from not existing keys.
					return null;
			}
		}
	}

	/**
	 * Set a runtime variable.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set(string $key, $value): void {
		$this->data[$key] = $value;
	}

	/**
	 * Get the $data array to be shelved and restored at a later point.
	 *
	 * @return array The runtime data array
	 */
	public function shelve(): array {
		Debug::log($this->data, 'Shelving');

		return $this->data;
	}

	/**
	 * Restore shelved $data array by replacing it with the passed $shelf array.
	 *
	 * @param array $shelf
	 */
	public function unshelve(array $shelf): void {
		$this->data = $shelf;
		Debug::log($this->data, 'Unshelving');
	}
}
