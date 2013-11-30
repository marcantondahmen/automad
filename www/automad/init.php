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
	// Test if PATH_INFO ends with '/' without just being '/',
	// otherwise an infinite loop can be created when accessing the home page.
	if (substr($_SERVER['PATH_INFO'], -1) == '/' && $_SERVER['PATH_INFO'] != '/') {
		header('Location: ' . AM_BASE_URL . rtrim($_SERVER['PATH_INFO'], '/'), false, 301);
		exit;	
	}	
}	


// The cache folder must be writable (resized images), also when caching is disabled!
if (!is_writable(AM_BASE_DIR . AM_DIR_CACHE)) {
	exit('The folder "' . AM_DIR_CACHE . '" must be writable by the web server!');
}


// Autoload core classes and libraries
spl_autoload_register(function($class) {
		
	$class = str_replace('\\', '/', $class);
	$possibleFiles = 	array(
					AM_BASE_DIR . '/automad/core/' . strtolower($class) . '.php',
					AM_BASE_DIR . '/automad/libraries/' . $class . '.php' 
				);
	
	foreach($possibleFiles as $file) {
		if (file_exists($file)) {
			require_once $file;
			Debug::log('Load Class: ' . $file);	
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


// Display execution time and server info
Debug::timerEnd();
Debug::log('Server:');
Debug::log($_SERVER);


?>