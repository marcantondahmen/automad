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

/**
 *	The Automad GUI starting sequence. 
 */


defined('AUTOMAD') or die('Direct access not permitted!');


session_start(); 


// Check PHP version
if (version_compare(PHP_VERSION, '5.3') < 0) {
	die('Please update your PHP version to 5.3 or higher! (Your current version is ' . PHP_VERSION . ')');
}


// Define base directory
define('AM_BASE_DIR', realpath(__DIR__ . '/../../../'));


// Load configuration
require AM_BASE_DIR . '/config/config.php';
require AM_BASE_DIR . '/automad/const.php';


// Check permissions
$dirs = array(
	'/automad',
	'/config',
	AM_DIR_CACHE,
	AM_DIR_PAGES,
	AM_DIR_SHARED
);

foreach ($dirs as $dir) {
	if (!is_writable(AM_BASE_DIR . $dir)) {
		die('The directory "' . $dir . '" must be writable by the web server!');
	}
}


// Redirect usere depending on login, page and installation status
$page = str_replace('.php', '', basename($_SERVER['SCRIPT_NAME']));

switch ($page) {
	
	case 'login':
		
		// If the current page is the login page, first test, if ths gui is installed, by searching for the users.txt file.
		// If the file can't be found, redirect to the installation page.
		if (!file_exists(AM_BASE_DIR . AM_FILE_ACCOUNTS)) {
			header('Location: http://' . $_SERVER['SERVER_NAME'] . AM_BASE_URL . '/automad/gui/install.php');
			die;
		}
		
		break;
	
	case 'install':
	
		// If the GUI is already installed, just redirect to automad/index.php (only for direct access of the install page after installing)
		if (file_exists(AM_BASE_DIR . AM_FILE_ACCOUNTS)) {
			header('Location: http://' . $_SERVER['SERVER_NAME'] . AM_BASE_URL . '/automad');
			die;
		}
		
		break;
	
	default:
		
		// All normal GUI pages
		// If the user is not logged in, redirect to login page.
		if (!isset($_SESSION['username'])) {
			header('Location: http://' . $_SERVER['SERVER_NAME'] . AM_BASE_URL . '/automad/gui/login.php');
			die;
		}
		
}


// Autoload core classes and libraries
spl_autoload_register(function($class) {
		
	$file = AM_BASE_DIR . '/automad/' . strtolower(str_replace('\\', '/', $class)) . '.php';
	require_once $file;
	
});


// Get GUI instance
$G = new Core\GUI();


?>