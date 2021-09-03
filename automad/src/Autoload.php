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

namespace Automad;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Autoload class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Autoload {
	public static function init() {
		require AM_BASE_DIR . '/lib/vendor/autoload.php';

		$packagesAutoload = AM_BASE_DIR . '/vendor/autoload.php';

		if (file_exists($packagesAutoload)) {
			require $packagesAutoload;
		}

		spl_autoload_register(function ($class) {
			$prefix = 'Automad\\';

			if (strpos($class, $prefix) === 0) {
				$file = AM_BASE_DIR . '/automad/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

				if (file_exists($file)) {
					require_once $file;
				}
			}
		});
	}
}
