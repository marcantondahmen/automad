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

use Automad\Core\Automad;
use Automad\UI\Models\Search\FileKeysModel;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The links model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class LinksModel {
	/**
	 * Update all links to a page or file after renaming or moving content.
	 *
	 * @param Automad $Automad
	 * @param string $old
	 * @param string $new
	 * @param string|null $dataFilePath
	 * @return bool true on success
	 */
	public static function update(Automad $Automad, string $old, string $new, ?string $dataFilePath = null) {
		$searchValue = '(?<=^|"|\(|\s)' . preg_quote($old) . '(?="|/|,|\?|#|\s|$)';
		$replaceValue = $new;

		$SearchModel = new SearchModel($Automad, $searchValue, true, false);
		$fileResultsArray = $SearchModel->searchPerFile();
		$fileKeysArray = array();

		foreach ($fileResultsArray as $FileResultsModel) {
			if ($dataFilePath === $FileResultsModel->path || empty($dataFilePath)) {
				$keys = array();

				foreach ($FileResultsModel->fieldResultsArray as $FieldResultsModel) {
					$keys[] = $FieldResultsModel->key;
				}

				$fileKeysArray[] = new FileKeysModel($FileResultsModel->path, $keys);
			}
		}

		$ReplacementModel = new ReplacementModel($searchValue, $replaceValue, true, false);
		$ReplacementModel->replaceInFiles($fileKeysArray);

		return true;
	}
}
