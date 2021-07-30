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
use Automad\UI\Models\Image as ModelsImage;
use Automad\UI\Response;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Image controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Image {
	/**
	 * Copy an image resized based on $_POST.
	 *
	 * @return \Automad\UI\Response the response object
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

		return ModelsImage::copyResized(
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
	 * @return \Automad\UI\Response the response object
	 */
	public static function select() {
		$Response = new Response();

		// Check if file from a specified page or the shared files will be listed and managed.
		// To display a file list of a certain page, its URL has to be submitted along with the form data.
		$Response->setHtml(ModelsImage::select(Request::post('url')));

		return $Response;
	}
}
