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


// Load configuration
include AM_BASE_DIR . '/config/const.php';


// Load defaults
include AM_BASE_DIR . '/automad/const.php';


// Remove trailing slash from URL to keep relative links consistent
if (isset($_SERVER['PATH_INFO'])) {
	if (substr($_SERVER['PATH_INFO'], -1) == '/') {
		header('Location: ' . AM_BASE_URL . rtrim($_SERVER['PATH_INFO'], '/'), false, 301);
		exit;	
	}	
}	


// Autoload core classes and libraries
spl_autoload_register(function($class) {
	
	$class = strtolower($class);	
	$possibleFiles = 	array(
					AM_BASE_DIR . '/automad/core/' . $class . '.php',
					AM_BASE_DIR . '/automad/libraries/' . $class . '/' . $class . '.php' 
				);
	
	foreach($possibleFiles as $file) {
		if (file_exists($file)) {
			require_once $file;	
		}	
	}
	
});


// Setup basic debugging
Debug::reportAllErrors();
Debug::timerStart();


// Load page from cache or process template
$C = new Cache;

if ($C->pageCacheIsApproved()) {

	// If cache is up to date and the cached file exists,
	// just get the page from the cache.
	$output = $C->readPageFromCache();
	
} else {
	
	// Else check if the site object cache is ok...
	if ($C->siteObjectCacheIsApproved()) {
		
		// If approved, load site from cache...
		$S = $C->readSiteObjectFromCache();
		
	} else {
	
		// Else create new Site.
		$S = new Site();
		$C->writeSiteObjectToCache($S);
	
	}
	
	// Render template
	$T = new Template($S);
	$output = $T->render();
	
	// Save output to cache...
	$C->writePageToCache($output);
	
}


// Display page
echo $output;


// Display execution time and user constants
Debug::timerEnd();
Debug::log('User constants:');
Debug::log(get_defined_constants(true)['user']);


?>