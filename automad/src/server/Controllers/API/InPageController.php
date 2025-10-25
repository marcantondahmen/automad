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
use Automad\Core\Automad;
use Automad\Core\Request;
use Automad\Core\Text;
use Automad\Models\Page;
use Automad\System\Fields;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The inPage controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class InPageController {
	/**
	 * Handle in-page field editing requests.
	 *
	 * @return Response the response object
	 */
	public static function edit(): Response {
		$Response = new Response();
		$Automad = Automad::fromCache();
		$context = Request::post('context');
		$init = Request::post('init');
		$field = Request::post('field');

		$Page = $Automad->getPage($context);

		if (!$Page) {
			return $Response->setError(Text::get('pageNotFoundError'))->setCode(404);
		}

		$ThemeCollection = new ThemeCollection();
		$Theme = $ThemeCollection->getThemeByKey($Page->get(Fields::THEME));
		$templateFields = array_merge(
			Fields::inCurrentTemplate($Page, $Theme),
			array_values(Fields::$reserved)
		);

		if (!in_array($field, $templateFields)) {
			return $Response->setError(Text::get('invalidFieldError'));
		}

		if ($init) {
			return $Response->setData(array('value'=> $Page->data[$field] ?? ''));
		}

		if (filemtime($Page->getFile()) > Request::post('dataFetchTime')) {
			return $Response->setError(Text::get('preventDataOverwritingError'))->setCode(403);
		}

		$Page->updateField($field, Request::post('value'));

		return $Response->setData(array('saved' => true));
	}

	/**
	 * Publish a page.
	 *
	 * @return Response
	 */
	public static function publish(): Response {
		$Response = new Response();
		$url = Request::post('url');
		$Page = Page::fromCache($url);

		if (!$Page) {
			return $Response;
		}

		$newPagePath = $Page->publish();

		if (!empty($newPagePath)) {
			$Page = Page::findByPath($newPagePath);

			if ($Page) {
				return $Response->setRedirect(AM_BASE_INDEX . $Page->origUrl);
			}
		}

		return $Response;
	}
}
