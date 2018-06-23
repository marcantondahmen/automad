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
 *	Copyright (c) 2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad;


define('AUTOMAD', true);
define('AM_BASE_DIR', dirname(dirname(__DIR__)));


// Test location of updater to avoid unintended updates.

// Protect development repository.
if (strpos(AM_BASE_DIR, '/automad-dev') !== false) {
	exit('Can\'t run updates within development repository!');		
}

// Test if updater is run in external location.
if (strpos(__DIR__, '/automad/external') !== false) {
	exit('Can\'t run updates from ' . __DIR__);
}


require 'update.php';
require 'session.php';


System\Session::start();


if (!System\Session::user()) {
	exit('Access denied!');
}

$output = array();
$output['html'] = AM_BASE_DIR;
echo json_encode($output);
