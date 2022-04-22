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

namespace Automad\Admin\Controllers;

use Automad\Admin\API\Response;
use Automad\Admin\Models\ReplacementModel;
use Automad\Admin\Models\Search\FileFieldsModel;
use Automad\Admin\Models\SearchModel;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Parse;
use Automad\Core\Request;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Search controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SearchController {
	/**
	 * Perform a search and replace.
	 *
	 * @return Response the response object
	 */
	public static function searchReplace() {
		$Response = new Response();

		$isRegex = filter_var(Request::post('isRegex'), FILTER_VALIDATE_BOOL);
		$isCaseSensitive = filter_var(Request::post('isCaseSensitive'), FILTER_VALIDATE_BOOL);

		$files = json_decode(Request::post('files'));
		$replaceSelected = filter_var(Request::post('replaceSelected'), FILTER_VALIDATE_BOOL) && !empty($files);

		if ($replaceSelected) {
			$fileFieldsArray = array();
			$ReplacementModel = new ReplacementModel(
				Request::post('searchValue'),
				Request::post('replaceValue'),
				$isRegex,
				$isCaseSensitive
			);

			foreach ($files as $path => $fieldsCsv) {
				$fileFieldsArray[] = new FileFieldsModel($path, Parse::csv($fieldsCsv));
			}

			$ReplacementModel->replaceInFiles($fileFieldsArray);
		}

		$Cache = new Cache();
		$Search = new SearchModel(
			$Cache->getAutomad(),
			Request::post('searchValue'),
			$isRegex,
			$isCaseSensitive
		);

		$fileResultsArray = $Search->searchPerFile();
		Debug::log($fileResultsArray, 'Results per file');

		return $Response->setData($fileResultsArray);
	}
}
