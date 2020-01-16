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
 *	Copyright (c) 2018-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */
 

use Automad\System as System;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

// Test if updater gets called within development repo.
if (strpos(AM_BASE_DIR, '/automad-dev') !== false) {
    exit('Can\'t run updates within the development repository!' . PHP_EOL);
}

echo 'Automad version ' . AM_VERSION . PHP_EOL;
echo 'Update branch is ' . AM_UPDATE_BRANCH . PHP_EOL;

$updateVersion = System\Update::getVersion();

if (version_compare(AM_VERSION, $updateVersion, '<')) {
    
    echo 'Updating to version ' . $updateVersion . PHP_EOL;
    $output = System\Update::run();
    
    if (!empty($output['cli'])) {
        echo $output['cli'] . PHP_EOL;
    } else {
        echo 'Error! Update has failed!' . PHP_EOL;
    }
    
} else {
    
    echo 'Up to date!' . PHP_EOL;
    
}

