<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2018-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
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
spl_autoload_register(function ($class) {
	$prefix = 'Automad\\';

	if (strpos($class, $prefix) === 0) {
		$file = AM_BASE_DIR . '/automad/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

		if (file_exists($file)) {
			require_once $file;
		}
	}
});
