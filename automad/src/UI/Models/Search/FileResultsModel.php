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

namespace Automad\UI\Models\Search;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A wrapper class for all results for a given data file.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileResultsModel {
	/**
	 * The array of `FieldResultsModel`.
	 *
	 * @see FieldResultsModel
	 */
	public $fieldResultsArray;

	/**
	 * The file path.
	 */
	public $path;

	/**
	 * The page URL or an empty string for shared data.
	 */
	public $url;

	/**
	 * Initialize a new field results instance.
	 *
	 * @see FieldResultsModel
	 * @param string $path
	 * @param array $fieldResultsArray
	 * @param string $url
	 */
	public function __construct(string $path, array $fieldResultsArray, string $url = '') {
		$this->path = $path;
		$this->fieldResultsArray = $fieldResultsArray;
		$this->url = (string) $url;
	}
}
