<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\GUI\Controllers;

use Automad\Core\Cache;
use Automad\Core\Config as CoreConfig;
use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\GUI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Config controller.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Config {


	/**
	 *	Save the posted configuartion to the config.php file.
	 *
	 *	@return array the $output array
	 */

	public static function save() {

		$output = array();

		if ($json = Request::post('json')) {

			$config = json_decode($json, true);

			if (json_last_error() === JSON_ERROR_NONE) {

				// Make sure 'php' and other PHP extensions like 'php5' are removed 
				// from the list of allowed file types.
				if (!empty($config['AM_ALLOWED_FILE_TYPES'])) {
					$config['AM_ALLOWED_FILE_TYPES'] = trim(preg_replace('/,?\s*php\w?/is', '', $config['AM_ALLOWED_FILE_TYPES']), ', ');
				}

				if (CoreConfig::write($config)) {
					Cache::clear();
					$output['reload'] = true;
				} else {
					$output['error'] = Text::get('error_permission') . '<br>' . AM_CONFIG;
				}

			} else {

				$output['error'] = Text::get('error_json');
				
			}

		} else {

			$config = CoreConfig::read();
			$json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

			$output['html'] = <<< HTML
					<div class="uk-overflow-container">
						<textarea 
						class="uk-form-controls uk-width-1-1"
						name="json"
						>$json</textarea>
					</div>
HTML;

		}

		return $output;

	}


	/**
	 *	Update a single configuration item.
	 *
	 *	@return array the $output array
	 */

	public static function update() {

		$output = array();

		// Get config from json file, if exsiting.
		$config = CoreConfig::read();
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

			// Language
			if ($type == 'language') {

				$language = Request::post('language');
				$config['AM_FILE_GUI_TRANSLATION'] = $language;
				$output['redirect'] = '#3';
				$output['reload'] = true;
			}

			// Headless
			if ($type == 'headless') {

				if (isset($_POST['headless'])) {
					$config['AM_HEADLESS_ENABLED'] = true;
				} else {
					$config['AM_HEADLESS_ENABLED'] = false;
				}

				// Reload page to update the dashboard.
				$output['reload'] = true;
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

		if (CoreConfig::write($config)) {

			Debug::log($config, 'Updated config file');
			$output['success'] = Text::get('success_config_update');
			Cache::clear();
		} else {

			$output['error'] = Text::get('error_permission') . '<br>' . AM_CONFIG;
		}

		return $output;

	}


}