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
use Automad\Admin\Models\FileModel;
use Automad\Admin\UI\Utils\Messenger;
use Automad\Core\Request;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2022 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileController {
	/**
	 * Edit file information (file name and caption).
	 *
	 * @return Response the response object
	 */
	public static function editInfo() {
		$Response = new Response();
		$Messenger = new Messenger();

		FileModel::editInfo(
			Request::post('new-name'),
			Request::post('old-name'),
			Request::post('caption'),
			$Messenger
		);

		$Response->setError($Messenger->getError());

		return $Response;
	}

	/**
	 * Import file from URL.
	 *
	 * @return Response the response object
	 */
	public static function import() {
		$Response = new Response();
		$Messenger = new Messenger();

		FileModel::import(
			Request::post('importUrl'),
			Request::post('url'),
			$Messenger
		);

		$Response->setError($Messenger->getError());

		return $Response;
	}
}
