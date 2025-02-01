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

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Parse;
use Automad\Core\Request;
use Automad\Models\Search\FileFields;
use Automad\Models\Search\Replacement;
use Automad\Models\Search\Search;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Search controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SearchController {
	/**
	 * Perform a search and replace.
	 *
	 * @return Response the response object
	 */
	public static function searchReplace(): Response {
		$Response = new Response();

		$isRegex = filter_var(Request::post('isRegex'), FILTER_VALIDATE_BOOL);
		$isCaseSensitive = filter_var(Request::post('isCaseSensitive'), FILTER_VALIDATE_BOOL);

		$files = json_decode(Request::post('files'));
		$replaceSelected = filter_var(Request::post('replaceSelected'), FILTER_VALIDATE_BOOL) && !empty($files);

		if ($replaceSelected) {
			$fileFieldsArray = array();
			$Replacement = new Replacement(
				Request::post('searchValue'),
				Request::post('replaceValue'),
				$isRegex,
				$isCaseSensitive
			);

			foreach ($files as $path => $fieldsCsv) {
				$fileFieldsArray[] = new FileFields($path, Parse::csv($fieldsCsv));
			}

			$Replacement->replaceInFiles($fileFieldsArray);
		}

		$Cache = new Cache();
		$Automad = $Cache->getAutomad();
		$Search = new Search(
			Request::post('searchValue'),
			$isRegex,
			$isCaseSensitive,
			$Automad->getPages(),
			$Automad->Shared
		);

		$fileResultsArray = $Search->searchPerFile();
		Debug::log($fileResultsArray, 'Results per file');

		return $Response->setData($fileResultsArray);
	}
}
