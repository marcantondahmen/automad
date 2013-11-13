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
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */
 

define('AUTOMAD', true);


// Load lonfiguration
include BASE_DIR . '/config/overrides.php';


// Load defaults
include BASE_DIR . '/automad/const.php';


// Remove trailing slash from URL to keep relative links consistent
if (isset($_SERVER['PATH_INFO'])) {
	if (substr($_SERVER['PATH_INFO'], -1) == '/') {
		header('Location: ' . BASE_URL . rtrim($_SERVER['PATH_INFO'], '/'), false, 301);
		exit;	
	}	
}	


// Autoload classes
spl_autoload_register(function ($class) {
	$class = strtolower($class);
	include BASE_DIR . '/automad/core/' . $class . '.php';	
});


// Load 3rd party libs
include BASE_DIR . '/automad/libraries/parsedown/Parsedown.php';


// Setup basic debugging
Debug::reportAllErrors();
Debug::timerStart();


// Load page from cache or process template
$C = new Cache;

if ($C->cacheIsApproved()) {

	// If cache is up to date and the cached file exists,
	// just get the page from the cache.
	$output = $C->readCache();
	
} else {
	
	// If the cache is not approved,
	// everything has to be re-rendered.
	$S = new Site();
	$T = new Template($S);
	$output = $T->render();
	$C->writeCache($output);
	
}


// Display page
echo $output;


// Display execution time and user constants
Debug::timerEnd();
Debug::pr(get_defined_constants(true)['user']);


?>