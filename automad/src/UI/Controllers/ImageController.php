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

use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\UI\Models\ImageModel;
use Automad\UI\Response;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Image controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ImageController {
	/**
	 * Copy an image resized based on $_POST.
	 *
	 * @return Response the response object
	 */
	public static function copyResized() {
		$options = array_merge(
			array(
				'url' => '',
				'filename' => '',
				'width' => false,
				'height' => false,
				'crop' => false
			),
			$_POST
		);

		Debug::log($options, 'options');

		return ImageModel::copyResized(
			$options['filename'],
			$options['url'],
			$options['width'],
			$options['height'],
			$options['crop']
		);
	}

	/**
	 * Select an image.
	 *
	 * @return Response the response object
	 */
	public static function select() {
		$Response = new Response();

		// Check if file from a specified page or the shared files will be listed and managed.
		// To display a file list of a certain page, its URL has to be submitted along with the form data.
		$Response->setHtml(ImageModel::select(Request::post('url')));

		return $Response;
	}
}
