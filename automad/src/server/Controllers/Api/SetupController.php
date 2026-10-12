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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Controllers\Api;

use Automad\Api\Response;
use Automad\Core\Request;
use Automad\System\Setup\Setup;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The setup controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class SetupController {
	/**
	 * Finish the setup.
	 *
	 * @return Response
	 */
	public static function finish(): Response {
		$Response = new Response();

		if (Request::post('finish')) {
			Setup::finish();
		}

		return $Response;
	}

	/**
	 * Start the setup by running all migrations and return the required wizard steps.
	 * Note that this is always the first request when the setup is started after a fresh install or an update.
	 *
	 * @return Response
	 */
	public static function start(): Response {
		$Response = new Response();

		return $Response->setData(array('steps' => Setup::start()));
	}
}
