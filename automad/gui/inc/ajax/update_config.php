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
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	Update or merge config/config.php with requested items.
 */


$output = array();


// Get config from json file, if exsiting.
$config = Core\Config::read();
ksort($config);


if ($type = Core\Request::post('type')) {
	
	// Cache
	if ($type == 'cache') {
		
		$cache = Core\Request::post('cache');
		
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

		$language = Core\Request::post('language');
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


if (Core\Config::write($config)) {

	Core\Debug::log($config, 'Updated config file');
	$output['success'] = Text::get('success_config_update');

} else {

	$output['error'] = Text::get('error_permission') . '<br>' . AM_CONFIG;

}


$this->jsonOutput($output);


?>