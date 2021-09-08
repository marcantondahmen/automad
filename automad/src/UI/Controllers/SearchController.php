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

namespace Automad\UI\Controllers;

use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\UI\Components\Alert\Alert;
use Automad\UI\Components\Layout\SearchResults;
use Automad\UI\Models\ReplacementModel;
use Automad\UI\Models\SearchModel;
use Automad\UI\Models\Search\FileKeysModel;
use Automad\UI\Response;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\UICache;

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
	public static function searchAndReplace() {
		$Response = new Response();

		if (Request::post('replaceSelected')) {
			$files = Request::post('files');

			if (!empty($files)) {
				$fileKeysArray = array();
				$ReplacementModel = new ReplacementModel(
					Request::post('searchValue'),
					Request::post('replaceValue'),
					Request::post('isRegex'),
					Request::post('isCaseSensitive')
				);

				foreach ($files as $path => $keysJson) {
					$fileKeysArray[] = new FileKeysModel($path, json_decode($keysJson, true));
				}

				$ReplacementModel->replaceInFiles($fileKeysArray);
			}
		}

		$Search = new SearchModel(
			UICache::get(),
			Request::post('searchValue'),
			Request::post('isRegex'),
			Request::post('isCaseSensitive')
		);

		$fileResultsArray = $Search->searchPerFile();

		Debug::log($fileResultsArray, 'Results per file');

		if (!$html = SearchResults::render($fileResultsArray)) {
			$html = Alert::render(Text::get('search_no_results'), 'uk-margin-top');
		}

		$Response->setHtml($html);

		return $Response;
	}
}
