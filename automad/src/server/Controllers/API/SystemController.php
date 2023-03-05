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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Messenger;
use Automad\System\Update;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The system controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SystemController {
	/**
	 * Check whether a system update is available.
	 *
	 * @return Response the response object
	 */
	public static function checkForUpdate(): Response {
		// Close session here already in order to prevent blocking other requests.
		session_write_close();

		$Response = new Response();
		$latest = Update::getVersion();
		$data = array(
			'latest' => $latest,
			'pending' => false
		);

		if (version_compare(AM_VERSION, $latest, '<')) {
			$data['pending'] = true;
		}

		return $Response->setData($data);
	}

	/**
	 * System updates.
	 *
	 * @return Response the response object
	 */
	public static function update(): Response {
		$Response = new Response();
		$data = array(
			'state' => 'upToDate',
			'current' => AM_VERSION,
			'latest' => '',
			'items' => Update::items()
		);

		if (strpos(AM_BASE_DIR, '/automad-dev') !== false) {
			$data['state'] = 'disabled';

			return $Response->setData($data);
		}

		if (!Update::supported()) {
			$data['state'] = 'notSupported';

			return $Response->setData($data);
		}

		if (!empty($_POST['update'])) {
			$Messenger = new Messenger();

			if (Update::run($Messenger)) {
				$Response->setData($Messenger->getData());
			}

			return $Response
				->setError($Messenger->getError())
				->setSuccess($Messenger->getSuccess());
		}

		$latest = Update::getVersion();

		if (empty($latest)) {
			$data['state'] = 'connectionError';

			return $Response->setData($data);
		}

		$data['latest'] = $latest;

		if (version_compare(AM_VERSION, $latest, '<')) {
			$data['state'] = 'pending';
		}

		return $Response->setData($data);
	}
}
