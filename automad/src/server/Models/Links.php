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

namespace Automad\Models;

use Automad\Core\Automad;
use Automad\Models\Search\FileFields;
use Automad\Models\Search\Replacement;
use Automad\Models\Search\Search;

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
	 * @param Automad $Automad
	 * @param string $old
	 * @param string $new
	 * @param string|null $dataFilePath
	 * @return bool true on success
	 */
	public static function update(Automad $Automad, string $old, string $new, ?string $dataFilePath = null) {
		$searchValue = '(?<=^|"|\(|\s)' . preg_quote($old) . '(?="|/|,|\?|#|\s|$)';
		$replaceValue = $new;

		$Search = new Search($Automad, $searchValue, true, false);
		$fileResultsArray = $Search->searchPerFile();
		$fileFieldsArray = array();

		foreach ($fileResultsArray as $FileResults) {
			if ($dataFilePath === $FileResults->path || empty($dataFilePath)) {
				$fields = array();

				foreach ($FileResults->fieldResultsArray as $FieldResults) {
					$fields[] = $FieldResults->field;
				}

				$fileFieldsArray[] = new FileFields($FileResults->path, $fields);
			}
		}

		$Replacement = new Replacement($searchValue, $replaceValue, true, false);
		$Replacement->replaceInFiles($fileFieldsArray);

		return true;
	}
}
