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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks\Utils;

use Automad\Core\Automad;
use Automad\Core\Image;
use Automad\Core\RemoteFile;
use Automad\Core\Resolve;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The ImgLoaderSet allows for resizing local or remote images and also create the their tiny prelod version.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ImgLoaderSet {
	/**
	 * The resized image height.
	 */
	public float $height;

	/**
	 * The url to the actual large image.
	 */
	public string $image;

	/**
	 * The url to the tiny blurred placeholder image.
	 */
	public string $preload;

	/**
	 * The resized image width.
	 */
	public float $width;

	/**
	 * Create a pair of two images, the actual cached image and the tiny preload-background.
	 * The specified $file can either be a remote URL or a local path.
	 *
	 * @param string $file
	 * @param Automad $Automad
	 * @param float $width
	 * @param float $height
	 */
	public function __construct(string $file, Automad $Automad, float $width = 0, float $height = 0) {
		if (preg_match('/\:\/\//is', $file)) {
			$RemoteFile = new RemoteFile($file);
			$file = $RemoteFile->getLocalCopy();
		} else {
			$file = Resolve::filePath($Automad->Context->get()->path, $file);
		}

		preg_match('/(\/[\w\.\-\/]+(?:jpg|jpeg|gif|png|webp))(\?(\d+)x(\d+))?/is', $file, $matches);

		$Image = new Image($matches[1], $matches[3] ?? $width, $matches[4] ?? $height, true);

		$this->image = AM_BASE_URL . $Image->file;
		$this->width = $Image->width;
		$this->height = $Image->height;

		$Preload = new Image(AM_BASE_DIR . $Image->file, 20);
		$this->preload = AM_BASE_URL . $Preload->file;
	}
}
