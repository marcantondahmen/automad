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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
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



// Cache
if (isset($_POST['cache'])) {
	
	$cache = $_POST['cache'];
	
	if ($cache['enabled'] == 'on') {
		$config['AM_CACHE_ENABLED'] = true;
	} else {
		$config['AM_CACHE_ENABLED'] = false;
	}
	
	$config['AM_CACHE_MONITOR_DELAY'] = intval($cache['monitor-delay']);
	
}


// Allowed file types
if (isset($_POST['file-types'])) {
	
	if ($_POST['file-types']) {
		// If there string actually contains file types and is not empty.
		$config['AM_ALLOWED_FILE_TYPES'] = $_POST['file-types'];
	} else {
		// If the string is empty, remove the variable from the config, to not overwrite the defaults.
		unset($config['AM_ALLOWED_FILE_TYPES']);
	}
	
}


// Debugging
if (isset($_POST['debug'])) {
	
	if ($_POST['debug'] == 'on') {
		$config['AM_DEBUG_ENABLED'] = true;
	} else {
		$config['AM_DEBUG_ENABLED'] = false;
	}
	
}




// Write config file.
if ((is_writable(dirname(AM_CONFIG)) && !file_exists(AM_CONFIG)) || is_writable(AM_CONFIG)) {

	$old = umask(0);
	file_put_contents(AM_CONFIG, json_encode($config, JSON_PRETTY_PRINT));
	umask($old);

} else {

	$output['error'] = $this->tb['error_permission'] . '<p>' . AM_CONFIG . '</p>';

}


echo json_encode($output);


?>