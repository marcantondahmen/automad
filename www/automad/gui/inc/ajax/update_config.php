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
 *	Copyright (c) 2014-2018 by Marc Anton Dahmen
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

if (!empty($_POST['type'])) {
	
	$type = $_POST['type'];
	
	// Cache
	if ($type == 'cache' && isset($_POST['cache'])) {
		
		$cache = $_POST['cache'];
		
		if (isset($cache['enabled'])) {
			$config['AM_CACHE_ENABLED'] = true;
		} else {
			$config['AM_CACHE_ENABLED'] = false;
		}
		
		$config['AM_CACHE_MONITOR_DELAY'] = intval($cache['monitor-delay']);
		$config['AM_CACHE_LIFETIME'] = intval($cache['lifetime']);
		
	}

	// Allowed file types
	if ($type == 'file-types' && isset($_POST['file-types'])) {
		
		if ($_POST['file-types']) {
			// If there string actually contains file types and is not empty.
			$config['AM_ALLOWED_FILE_TYPES'] = $_POST['file-types'];
		} else {
			// If the string is empty, remove the variable from the config, to not overwrite the defaults.
			unset($config['AM_ALLOWED_FILE_TYPES']);
		}
		
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
	Core\Debug::ajax($output, 'config', $config);

} else {

	$output['error'] = Text::get('error_permission') . '<p>' . AM_CONFIG . '</p>';

}


echo json_encode($output);


?>