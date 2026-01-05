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
use Automad\Core\Config;
use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Config controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ConfigController {
	/**
	 * Update a single configuration item.
	 *
	 * @return Response the response object
	 */
	public static function update(): Response {
		$Response = new Response();

		$config = Config::read();
		ksort($config);

		switch (Request::post('type')) {
			case 'cache':
				if (AM_CLOUD_MODE_ENABLED) {
					$Response->setError(Text::get('permissionsDeniedError'));
				} else {
					$config['AM_CACHE_ENABLED'] = !empty(Request::post('cacheEnabled'));
					$config['AM_CACHE_MONITOR_DELAY'] = intval(Request::post('cacheMonitorDelay'));
					$config['AM_CACHE_LIFETIME'] = intval(Request::post('cacheLifetime'));
				}

				break;

			case 'debug':
				if (AM_CLOUD_MODE_ENABLED) {
					$Response->setError(Text::get('permissionsDeniedError'));
				} else {
					$config['AM_DEBUG_ENABLED'] = !empty(Request::post('debugEnabled'));
				}

				break;

			case 'feed':
				$config['AM_FEED_ENABLED'] = !empty(Request::post('feedEnabled'));
				$config['AM_FEED_FIELDS'] = '';

				if ($fields = Request::post('feedFields')) {
					$config['AM_FEED_FIELDS'] = join(', ', array_unique(json_decode($fields)));
				}

				break;

			case 'i18n':
				$config['AM_I18N_ENABLED'] = !empty(Request::post('i18nEnabled'));

				break;

			case 'translation':
				$config['AM_FILE_UI_TRANSLATION'] = Request::post('translation');
				$Response->setReload(true);

				break;

			case 'sessionCookieSalt':
				$config['AM_SESSION_COOKIE_SALT'] = substr(str_shuffle(MD5(microtime())), 0, 10);
				$Response->setReload(true);

				break;
		}

		if (Config::write($config)) {
			Debug::log($config, 'Updated config file');
			Cache::clear();
		} else {
			$Response->setError(Text::get('permissionsDeniedError'));
		}

		return $Response;
	}
}
