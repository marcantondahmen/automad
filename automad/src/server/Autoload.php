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
 * Copyright (c) 2018-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad;

use Automad\Core\Error;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Autoload class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Autoload {
	/**
	 * Init the autoloader.
	 */
	public static function init(): void {
		$vendors = array(
			AM_BASE_DIR . '/lib/vendor/autoload.php',
			AM_BASE_DIR . '/vendor/autoload.php'
		);

		foreach($vendors as $file) {
			if (!is_readable($file)) {
				Error::exit(
					'Missing dependencies',
					'Please run the command <code>composer install</code> inside the directory ' . dirname(dirname($file)) . '.'
				);
			}

			require_once $file;
		}

		spl_autoload_register(function ($class) {
			$prefix = 'Automad\\';

			if (strpos($class, $prefix) === 0) {
				$file = AM_BASE_DIR . '/automad/src/server/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

				if (file_exists($file)) {
					require_once $file;
				}
			}
		});
	}
}
