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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Controllers;

use Automad\Core\Request;
use Automad\UI\Models\File as ModelsFile;
use Automad\UI\Response;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class File {
	/**
	 * Edit file information (file name and caption).
	 *
	 * @return \Automad\UI\Response the response object
	 */
	public static function editInfo() {
		$Response = new Response();

		$Response->setError(
			ModelsFile::editInfo(
				Request::post('new-name'),
				Request::post('old-name'),
				Request::post('caption')
			)
		);

		return $Response;
	}

	/**
	 * Import file from URL.
	 *
	 * @return \Automad\UI\Response the response object
	 */
	public static function import() {
		$Response = new Response();

		$Response->setError(
			ModelsFile::import(
				Request::post('importUrl'),
				Request::post('url')
			)
		);

		return $Response;
	}
}
