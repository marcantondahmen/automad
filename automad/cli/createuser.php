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


use Automad\GUI as GUI;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

echo 'Creating new user account for the Automad dashboard ...' . PHP_EOL;

if (is_readable(AM_FILE_ACCOUNTS)) {
	$accounts = GUI\Accounts::get();
} else {
	$accounts = array();
}

$name = 'user_' . substr(str_shuffle(MD5(microtime())), 0, 5);
$password = substr(str_shuffle(MD5(microtime())), 0, 10);

$accounts[$name] = GUI\Accounts::passwordHash($password);

if (GUI\Accounts::write($accounts)) {
	echo PHP_EOL;
	echo '--------------------' . PHP_EOL;
	echo 'Name:     ' . $name . PHP_EOL;
	echo 'Password: ' . $password . PHP_EOL;
	echo '--------------------' . PHP_EOL;
	echo PHP_EOL;
} else {
	echo 'Error! Creating of user account failed.' . PHP_EOL;
}
