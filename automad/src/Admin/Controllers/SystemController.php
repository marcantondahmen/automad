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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin\Controllers;

use Automad\Admin\API\Response;
use Automad\Admin\UI\Utils\Messenger;
use Automad\System\Update;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The system controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2022 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SystemController {
	/**
	 * Check whether a system update is available.
	 *
	 * @return Response the response object
	 */
	public static function checkForUpdate() {
		$Response = new Response();
		$latest = Update::getVersion();
		$data = array(
			'latest' => $latest,
			'pending' => false
		);

		if (version_compare(AM_VERSION, $latest, '<')) {
			$data['pending'] = true;
		}

		$Response->setData($data);

		return $Response;
	}
	/**
	 * System updates.
	 *
	 * @return Response the response object
	 */
	public static function update() {
		$Response = new Response();
		$data = array(
			'state' => 'upToDate',
			'current' => AM_VERSION,
			'latest' => '',
			'items' => Update::items()
		);

		if (strpos(AM_BASE_DIR, '/automad-dev') !== false) {
			$data['state'] = 'disabled';
			$Response->setData($data);

			return $Response;
		}

		if (!Update::supported()) {
			$data['state'] = 'notSupported';
			$Response->setData($data);

			return $Response;
		}

		if (!empty($_POST['update'])) {
			$Messenger = new Messenger();

			if (Update::run($Messenger)) {
				$Response->setData($Messenger->getData());
			}

			$Response->setError($Messenger->getError());
			$Response->setSuccess($Messenger->getSuccess());

			return $Response;
		}

		$latest = Update::getVersion();

		if (empty($latest)) {
			$data['state'] = 'connectionError';
			$Response->setData($data);

			return $Response;
		}

		$data['latest'] = $latest;

		if (version_compare(AM_VERSION, $latest, '<')) {
			$data['state'] = 'pending';
		}

		$Response->setData($data);

		return $Response;
	}
}
