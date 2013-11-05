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


// Configuration overrides
include BASE_DIR . '/config/overrides.php';


// Default constants
include BASE_DIR . '/automad/const.php';


// Remove trailing slash from URL to keep relative links consistent
if (isset($_SERVER['PATH_INFO'])) {
	
	if (substr($_SERVER['PATH_INFO'], -1) == '/') {
		
		header('Location: ' . BASE_URL . rtrim($_SERVER['PATH_INFO'], '/'), false, 301);
		exit;
		
	}
	
}	


// Auto load classes
spl_autoload_register(function ($class) {
		
	$class = strtolower($class);
	include BASE_DIR . '/automad/core/' . $class . '.php';
	
});


// 3rd party libraries
include BASE_DIR . '/automad/libraries/parsedown/Parsedown.php';


// Debug: Turn on error reporting for all errors.
Debug::reportAllErrors();


// Init new template
$T = new Template();

$T->render();


?>