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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
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
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The ImgLoaderSet allows for resizing local or remote images and also create the their tiny prelod version.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2024 by Marc Anton Dahmen - https://marcdahmen.de
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
	 * @param bool $crop
	 */
	public function __construct(string $file, Automad $Automad, float $width = 0, float $height = 0, bool $crop = true) {
		$Img = new Img($file, $Automad, $width, $height, $crop);

		$this->image = $Img->image;
		$this->width = $Img->width;
		$this->height = $Img->height;

		$Preload = new Image(AM_BASE_DIR . Str::stripStart($Img->image, AM_BASE_URL), 20);
		$this->preload = AM_BASE_URL . $Preload->file;
	}
}
