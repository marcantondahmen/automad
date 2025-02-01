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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers;

use Automad\Core\Image;
use Automad\Core\RemoteFile;
use Automad\Core\Request;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Image controller class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ImageController {
	/**
	 * Resize a given image and output the file content.
	 *
	 * @return string
	 */
	public static function resize(): string {
		$url = Request::query('url');
		$width = Request::query('w');
		$height = Request::query('h');

		if (empty($url) || empty($width) || empty($height)) {
			http_response_code(404);
			exit();
		}

		$path = AM_BASE_DIR . Str::stripStart($url, AM_BASE_URL);

		if (preg_match('/:\/\//', $url, $matches)) {
			$remote = new RemoteFile($url);
			$path = $remote->getLocalCopy();
		}

		$image = new Image($path, $width, $height, true);

		if (!$image->file) {
			http_response_code(404);
			exit();
		}

		$resized = AM_BASE_DIR . $image->file;

		header('Content-Type:' . $image->type);
		header('Content-Length: ' . strval(filesize($resized)));
		readfile($resized);

		exit();
	}
}
