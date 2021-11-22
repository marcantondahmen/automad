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

use Automad\Core\Cache;
use Automad\Core\Config;
use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\UI\Response;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\URLHashes;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Config controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ConfigController {
	/**
	 * Save the posted configuartion to the config.php file.
	 *
	 * @return Response the response object
	 */
	public static function save() {
		$Response = new Response();

		if ($json = Request::post('json')) {
			$config = json_decode($json, true);

			if (json_last_error() === JSON_ERROR_NONE) {
				// Make sure 'php' and other PHP extensions like 'php5' are removed
				// from the list of allowed file types.
				if (!empty($config['AM_ALLOWED_FILE_TYPES'])) {
					$config['AM_ALLOWED_FILE_TYPES'] = trim(preg_replace('/,?\s*php\w?/is', '', $config['AM_ALLOWED_FILE_TYPES']), ', ');
				}

				if (Config::write($config)) {
					Cache::clear();
					$Response->setReload(true);
				} else {
					$Response->setError(Text::get('error_permission') . '<br>' . Config::$file);
				}
			} else {
				$Response->setError(Text::get('error_json'));
			}
		} else {
			$config = Config::read();
			$json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

			$html = <<< HTML
				<div class="uk-overflow-container">
					<textarea 
					class="uk-form-controls uk-width-1-1"
					name="json"
					>$json</textarea>
				</div>
			HTML;

			$Response->setHtml($html);
		}

		return $Response;
	}

	/**
	 * Update a single configuration item.
	 *
	 * @return Response the response object
	 */
	public static function update() {
		$Response = new Response();

		// Get config from json file, if exsiting.
		$config = Config::read();
		ksort($config);

		if ($type = Request::post('type')) {
			// Cache
			if ($type == 'cache') {
				$cache = Request::post('cache');

				if (isset($cache['enabled'])) {
					$config['AM_CACHE_ENABLED'] = true;
				} else {
					$config['AM_CACHE_ENABLED'] = false;
				}

				$config['AM_CACHE_MONITOR_DELAY'] = intval($cache['monitor-delay']);
				$config['AM_CACHE_LIFETIME'] = intval($cache['lifetime']);
			}

			// Feed
			if ($type == 'feed') {
				if (Request::post('feed')) {
					$config['AM_FEED_ENABLED'] = true;
				} else {
					$config['AM_FEED_ENABLED'] = false;
				}

				if ($fields = Request::post('fields')) {
					$config['AM_FEED_FIELDS'] = join(', ', $fields);
				} else {
					$config['AM_FEED_FIELDS'] = '';
				}
			}

			// Language
			if ($type == 'language') {
				$language = Request::post('language');
				$config['AM_FILE_GUI_TRANSLATION'] = $language;
				$Response->setRedirect('#' . URLHashes::get()->system->language);
				$Response->setReload(true);
			}

			// Headless
			if ($type == 'headless') {
				if (isset($_POST['headless'])) {
					$config['AM_HEADLESS_ENABLED'] = true;
				} else {
					$config['AM_HEADLESS_ENABLED'] = false;
				}

				// Reload page to update the dashboard.
				$Response->setReload(true);
			}

			// Debugging
			if ($type == 'debug') {
				if (isset($_POST['debug'])) {
					$config['AM_DEBUG_ENABLED'] = true;
				} else {
					$config['AM_DEBUG_ENABLED'] = false;
				}
			}
		}

		if (Config::write($config)) {
			Debug::log($config, 'Updated config file');
			$Response->setSuccess(Text::get('success_config_update'));
			Cache::clear();
		} else {
			$Response->setError(Text::get('error_permission') . '<br>' . Config::$file);
		}

		return $Response;
	}
}
