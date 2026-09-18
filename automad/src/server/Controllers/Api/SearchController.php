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
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class SearchController {
	/**
	 * Perform a search and replace.
	 *
	 * @return Response the response object
	 */
	public static function searchReplace(): Response {
		$Response = new Response();
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();

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
				$isCaseSensitive,
				$Automad->ComponentCollection,
				false
			);

			foreach ($files as $path => $fieldsCsv) {
				$fileFieldsArray[] = new FileFields($path, Parse::csv($fieldsCsv));
			}

			$Replacement->replaceInFiles($fileFieldsArray);

			// Recreate Automad instance after changing conten on disk.
			$Automad = $Cache->getAutomad();
		}

		$Search = new Search(
			Request::post('searchValue'),
			$isRegex,
			$isCaseSensitive,
			$Automad->getPages(),
			$Automad->SearchIndexCache
		);

		$fileResultsArray = $Search->searchPerFile();
		Debug::log($fileResultsArray, 'Results per file');

		return $Response->setData($fileResultsArray);
	}
}
