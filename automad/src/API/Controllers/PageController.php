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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\API\Controllers;

use Automad\API\Models\PageModel;
use Automad\API\Response;
use Automad\API\Utils\APICache;
use Automad\API\Utils\Keys;
use Automad\Core\Parse;
use Automad\Core\Request;
use Automad\Core\Selection;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The App controller handles all requests related to page data.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageController {
	/**
	 * Get a breadcrumb trail for a requested page.
	 *
	 * @return Response the response data
	 */
	public static function breadcrumbs() {
		$Automad = APICache::get();
		$Response = new Response();
		$url = Request::post('url');

		if ($url && ($Page = $Automad->getPage($url))) {
			$Selection = new Selection($Automad->getCollection());
			$Selection->filterBreadcrumbs($url);

			$breadcrumbs = array();

			foreach ($Selection->getSelection(false) as $Page) {
				$breadcrumbs[] = array(
					'url' => $Page->origUrl,
					'title' => $Page->get(AM_KEY_TITLE)
				);
			}

			$Response->setData($breadcrumbs);
		}

		return $Response;
	}

	/**
	 * Send form when there is no posted data in the request or save data if there is.
	 *
	 * @return Response the response object
	 */
	public static function data() {
		$Automad = APICache::get();
		$Response = new Response();
		$url = Request::post('url');

		if ($url && ($Page = $Automad->getPage($url))) {
			// If the posted form contains any "data", save the form's data to the page file.
			if ($data = Request::post('data')) {
				// Save page and replace $Response with the returned $Response object (error or redirect).
			} else {
				// If only the URL got submitted, just get the form ready.
				$ThemeCollection = new ThemeCollection();
				$Theme = $ThemeCollection->getThemeByKey($Page->get(AM_KEY_THEME));
				$keys = Keys::inCurrentTemplate($Page, $Theme);
				$data = Parse::dataFile(PageModel::getPageFilePath($Page));

				$unusedKeys = array_values(array_diff(array_keys($data), $keys, Keys::$reserved));

				$Response->setData(
					array(
						'url' => $Page->origUrl,
						'prefix' => PageModel::extractPrefixFromPath($Page->path),
						'slug' => PageModel::extractSlugFromPath($Page->path),
						'template' => $Page->getTemplate(),
						'pageData' => $data,
						'shared' => $Automad->Shared->data,
						'keys' => array(
							'reserved' => array(
								'AM_KEY_DATE' => AM_KEY_DATE,
								'AM_KEY_HIDDEN' => AM_KEY_HIDDEN,
								'AM_KEY_PRIVATE' => AM_KEY_PRIVATE,
								'AM_KEY_TAGS' => AM_KEY_TAGS,
								'AM_KEY_THEME' => AM_KEY_THEME,
								'AM_KEY_TITLE' => AM_KEY_TITLE,
								'AM_KEY_SITENAME' => AM_KEY_SITENAME,
								'AM_KEY_URL' => AM_KEY_URL
							),
							'settings' => Keys::filterSettingKeys($keys),
							'text' => Keys::filterTextKeys($keys),
							'colors' => Keys::filterColorKeys($keys)
						),
						'keysUnused' => array(
							'settings' => Keys::filterSettingKeys($unusedKeys),
							'text' => Keys::filterTextKeys($unusedKeys),
							'colors' => Keys::filterColorKeys($unusedKeys)
						)
					)
				);
			}
		}

		return $Response;
	}
}
