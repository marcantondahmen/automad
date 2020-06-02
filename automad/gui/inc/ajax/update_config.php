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
 *	Update/merge AM_CONFIG with requested items.
 */


$output = array();


// Get config from json file, if exsiting.
if (file_exists(AM_CONFIG)) {
	
	$config = json_decode(file_get_contents(AM_CONFIG), true);
	ksort($config);

} else {

	$config = array();

}

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


// Write config file.
if ((is_writable(dirname(AM_CONFIG)) && !file_exists(AM_CONFIG)) || is_writable(AM_CONFIG)) {
	
	$json = json_encode($config, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
	FileSystem::write(AM_CONFIG, $json);
	$output['success'] = Text::get('success_config_update');
	Core\Debug::log($config, 'config');

} else {

	$output['error'] = Text::get('error_permission') . '<p>' . AM_CONFIG . '</p>';

}


$this->jsonOutput($output);


?>