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
 * Copyright (c) 2021-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Models\File;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileController {
	/**
	 * Edit file information (file name and caption).
	 *
	 * @return Response the response object
	 */
	public static function editInfo(): Response {
		$Response = new Response();
		$Messenger = new Messenger();

		$reload = File::editInfo(
			Request::post('new-name'),
			Request::post('old-name'),
			Request::post('caption'),
			$Messenger
		);

		return $Response->setReload($reload)->setError($Messenger->getError());
	}

	/**
	 * Import file from URL.
	 *
	 * @return Response the response object
	 */
	public static function import(): Response {
		$Response = new Response();
		$Messenger = new Messenger();

		File::import(
			Request::post('importUrl'),
			Request::post('url'),
			$Messenger
		);

		return $Response->setError($Messenger->getError());
	}
}
