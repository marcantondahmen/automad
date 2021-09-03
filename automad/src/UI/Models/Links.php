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

namespace Automad\UI\Models;

use Automad\UI\Models\Search\FileKeys;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The links model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Links {
	/**
	 * Update all links to a page or file after renaming or moving content.
	 *
	 * @param \Automad\Core\Automad $Automad
	 * @param string $old
	 * @param string $new
	 * @param string $dataFilePath
	 * @return boolean true on success
	 */
	public static function update($Automad, $old, $new, $dataFilePath = false) {
		$searchValue = '(?<=^|"|\(|\s)' . preg_quote($old) . '(?="|/|,|\?|#|\s|$)';
		$replaceValue = $new;

		$Search = new Search($Automad, $searchValue, true, false);
		$fileResultsArray = $Search->searchPerFile();
		$fileKeysArray = array();

		foreach ($fileResultsArray as $FileResults) {
			if ($dataFilePath === $FileResults->path || empty($dataFilePath)) {
				$keys = array();

				foreach ($FileResults->fieldResultsArray as $FieldResults) {
					$keys[] = $FieldResults->key;
				}

				$fileKeysArray[] = new FileKeys($FileResults->path, $keys);
			}
		}

		$Replacement = new Replacement($searchValue, $replaceValue, true, false);
		$Replacement->replaceInFiles($fileKeysArray);

		return true;
	}
}
