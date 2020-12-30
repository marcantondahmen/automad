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
 *	Copyright (c) 2013-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */
 
 
defined('AUTOMAD') or die('Direct access not permitted!');


$requiredVersion = '5.6.0';

if (version_compare(PHP_VERSION, $requiredVersion, '<')) {
	exit('<h1>PHP out of date!</h1><h2>Please update your PHP version to ' . $requiredVersion . ' or newer!</h2>');
}


use Automad\Core as Core;
use Automad\GUI as GUI;


// Set default timezone if not set.
date_default_timezone_set(@date_default_timezone_get());


// Set base dir.
require_once __DIR__ . '/core/filesystem.php';
define('AM_BASE_DIR', Core\FileSystem::normalizeSlashes(dirname(__DIR__)));


// Autoloading.
require AM_BASE_DIR . '/automad/autoload.php';


// Load configuration and define constants.
require AM_BASE_DIR . '/automad/const.php';


// Enable full error reporting, when debugging is enabled.
Core\Debug::errorReporting();


// The cache folder must be writable (resized images), also when caching is disabled!
if (!is_writable(AM_BASE_DIR . AM_DIR_CACHE)) {	
	exit('<h1>Permission denied!</h1><h2>The "' . AM_DIR_CACHE . '" directory must be writable by the web server!</h2>');
}


// Start Session.
session_name('Automad-' . md5(AM_BASE_DIR));
session_set_cookie_params(0, '/', '', false, true);
session_start();


// Split GUI from regular pages.
if (AM_REQUEST == AM_PAGE_DASHBOARD && AM_PAGE_DASHBOARD) {
	
	$Dashboard = new GUI\Dashboard();
	$output = $Dashboard->output;
	
} else {

	// Set content type header.
	if (AM_HEADLESS_ENABLED) {
		header('Content-Type: application/json');
	}

	// Load page from cache or process template
	$Cache = new Core\Cache();

	if ($Cache->pageCacheIsApproved()) {

		// If cache is up to date and the cached file exists,
		// just get the page from the cache.
		$output = $Cache->readPageFromCache();
	
	} else {
	
		// Else check if the site object cache is ok...
		if ($Cache->automadObjectCacheIsApproved()) {
		
			// If approved, load site from cache...
			$Automad = $Cache->readAutomadObjectFromCache();
		
		} else {
	
			// Else create new Automad.
			$Automad = new Core\Automad();
			$Cache->writeAutomadObjectToCache($Automad);
	
			// Generate new sitemap.
			new Core\Sitemap($Automad->getCollection());
	
		}
	
		// Render template
		$View = new Core\View($Automad, AM_HEADLESS_ENABLED);
		$output = $View->render();
	
		// Save output to cache if page actually exists.
		if ($Automad->currentPageExists()) {
			
			$Cache->writePageToCache($output);
			
		} else {
			
			Core\Debug::log(AM_REQUEST, 'Page not found! Caching will be skipped!');
			
		}
		
	}
	
}


// If debug is enabled, prepend the logged information to the closing </body> tag and echo the page.
echo str_replace('</body>', Core\Debug::consoleLog() . '</body>', $output);


?>