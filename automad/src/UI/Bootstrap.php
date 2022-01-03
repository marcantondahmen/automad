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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI;

use Automad\System\ThemeCollection;
use Automad\UI\Utils\Session;
use Automad\UI\Utils\SwitcherSections;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The bootstrap JS file containing all required settings for the UI.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2022 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Bootstrap {
	/**
	 * Render the Javascript file content and set caching headers accordingly
	 *
	 * @return string the Javascript file
	 */
	public static function file() {
		$js = self::compile();
		$etag = md5($js);
		$ifNoneMatch = trim(
			str_replace(
				array('"', '\''),
				'',
				getenv('HTTP_IF_NONE_MATCH')
			)
		);

		header('Content-Type: application/javascript; charset=utf-8');
		header('Cache-Control: max-age=86400');

		if ($ifNoneMatch == $etag) {
			header('HTTP/1.1 304 Not Modified');
			exit();
		}

		header('Etag: ' . $etag);
		http_response_code(200);

		return $js;
	}

	/**
	 * Compile the Javascript file including all bootstrap information.
	 *
	 * @return string the Javascript file content
	 */
	private static function compile() {
		$fn = function ($expression) {
			return $expression;
		};

		$Automad = UICache::get();

		$sections = '{}';
		$tags = '[]';
		$themes = '{}';

		if (Session::getUsername()) {
			$sections = json_encode(SwitcherSections::get());
			$tags = json_encode($Automad->getPagelist()->getTags());
			$ThemeCollection = new ThemeCollection();
			$themes = json_encode($ThemeCollection->getThemes(), JSON_UNESCAPED_SLASHES);
		}

		return <<< JS
			(() => {
				window.Automad = {
					version: '{$fn(AM_VERSION)}',
					baseURL: '{$fn(AM_BASE_INDEX)}',
					dashboardURL: '{$fn(AM_BASE_INDEX . AM_PAGE_DASHBOARD)}',
					sections: $sections,
					tags: $tags,
					text: {$fn(json_encode(Text::getObject()))},
					themes: $themes
				}
			})();
			JS;
	}
}
