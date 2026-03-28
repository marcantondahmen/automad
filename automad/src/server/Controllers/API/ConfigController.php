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
use Automad\Core\Config;
use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\Core\Text;
use Automad\System\ConfigFile;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Config controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class ConfigController {
	/**
	 * Update a single configuration item.
	 *
	 * @return Response the response object
	 */
	public static function update(): Response {
		$Response = new Response();
		$ConfigFile = new ConfigFile();

		switch (Request::post('type')) {
			case 'cache':
				$ConfigFile->set('AM_CACHE_ENABLED', !empty(Request::post('cacheEnabled')));
				$ConfigFile->set('AM_CACHE_MONITOR_DELAY', intval(Request::post('cacheMonitorDelay')));
				$ConfigFile->set('AM_CACHE_LIFETIME', intval(Request::post('cacheLifetime')));

				break;

			case 'debug':
				$ConfigFile->set('AM_DEBUG_ENABLED', !empty(Request::post('debugEnabled')));
				$ConfigFile->set('AM_DEBUG_BROWSER', !empty(Request::post('debugBrowser')));

				break;

			case 'feed':
				$ConfigFile->set('AM_FEED_ENABLED', !empty(Request::post('feedEnabled')));
				$ConfigFile->set('AM_FEED_FIELDS', '');

				if ($fields = Request::post('feedFields')) {
					$ConfigFile->set('AM_FEED_FIELDS', join(', ', array_unique(json_decode($fields))));
				}

				break;

			case 'i18n':
				$ConfigFile->set('AM_I18N_ENABLED', !empty(Request::post('i18nEnabled')));

				break;

			case 'translation':
				$ConfigFile->set('AM_FILE_UI_TRANSLATION', Request::post('translation'));
				$Response->setReload(true);

				break;

			case 'sessionCookieSalt':
				$ConfigFile->set('AM_SESSION_COOKIE_SALT', substr(str_shuffle(MD5(microtime())), 0, 10));
				$Response->setReload(true);

				break;
		}

		if ($ConfigFile->write()) {
			Debug::log('Updated config file');
			Cache::clear();
		} else {
			$Response->setError(Text::get('permissionsDeniedError'));
		}

		return $Response;
	}
}
