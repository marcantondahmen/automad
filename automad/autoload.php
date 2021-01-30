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
 *	Copyright (c) 2018-2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */
 
 
defined('AUTOMAD') or die('Direct access not permitted!');

// Composer lib.
require AM_BASE_DIR . '/lib/vendor/autoload.php';

// Composer packages.
$packagesAutoload = AM_BASE_DIR . '/vendor/autoload.php';

if (file_exists($packagesAutoload)) {
    require $packagesAutoload;
}

// Automad.
spl_autoload_register(function($class) {
	
	$file = strtolower(str_replace('\\', '/', $class)) . '.php';
		
	if (strpos($file, 'automad') === 0 && strpos($file, 'automad/composer') === false) {	
		require_once AM_BASE_DIR . '/' . $file;
	}
		
});


