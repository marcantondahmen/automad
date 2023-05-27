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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
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
 * @copyright Copyright (c) 2021-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ConfigController {
	/**
	 * Save the posted configuartion to the config.php file.
	 *
	 * @return Response the response object
	 */
	public static function file(): Response {
		$Response = new Response();

		if ($json = Request::post('content')) {
			$config = json_decode($json, true);

			if (json_last_error() === JSON_ERROR_NONE) {
				// Make sure 'php' and other PHP extensions like 'php5' are removed
				// from the list of allowed file types.
				if (!empty($config['AM_ALLOWED_FILE_TYPES'])) {
					$config['AM_ALLOWED_FILE_TYPES'] = trim(
						preg_replace(
							'/,?\s*php\w?/is',
							'',
							$config['AM_ALLOWED_FILE_TYPES']
						),
						', '
					);
				}

				if (Config::write($config)) {
					Cache::clear();
					$Response->setReload(true);
				} else {
					$Response->setError(Text::get('permissionsDeniedError'));
				}
			} else {
				$Response->setError(Text::get('invalidJsonError'));
			}
		} else {
			$config = Config::read();
			$json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

			$Response->setData(array('content' => $json));
		}

		return $Response;
	}

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
				$config['AM_CACHE_ENABLED'] = !empty(Request::post('cacheEnabled'));
				$config['AM_CACHE_MONITOR_DELAY'] = intval(Request::post('cacheMonitorDelay'));
				$config['AM_CACHE_LIFETIME'] = intval(Request::post('cacheLifetime'));

				break;

			case 'debug':
				$config['AM_DEBUG_ENABLED'] = !empty(Request::post('debugEnabled'));

				break;

			case 'feed':
				$config['AM_FEED_ENABLED'] = !empty(Request::post('feedEnabled'));
				$config['AM_FEED_FIELDS'] = '';

				if ($fields = Request::post('feedFields')) {
					$config['AM_FEED_FIELDS'] = join(', ', array_unique(json_decode($fields)));
				}

				break;

			case 'translation':
				$config['AM_FILE_UI_TRANSLATION'] = Request::post('translation');
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
