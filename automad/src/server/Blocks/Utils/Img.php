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

namespace Automad\Blocks\Utils;

use Automad\Core\Automad;
use Automad\Core\FileSystem;
use Automad\Core\Image;
use Automad\Core\RemoteFile;
use Automad\Core\Resolve;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Img class is a tiny wrapper for resizing local or remote images.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Img {
	/**
	 * The resized image height.
	 */
	public float $height;

	/**
	 * The url to the actual large image.
	 */
	public string $image;

	/**
	 * The resized image width.
	 */
	public float $width;

	/**
	 * The specified $file can either be a remote URL or a local path.
	 *
	 * @param string $file
	 * @param Automad $Automad
	 * @param float $width
	 * @param float $height
	 * @param bool $crop
	 */
	public function __construct(string $file, Automad $Automad, float $width = 0, float $height = 0, bool $crop = true) {
		if (preg_match('/\:\/\//is', $file)) {
			$RemoteFile = new RemoteFile($file);
			$file = $RemoteFile->getLocalCopy();
		} else {
			$file = Resolve::filePath($Automad->Context->get()->path, $file);
		}

		preg_match('/(\/[\w\.\-\/]+(?:' . join('|', FileSystem::FILE_TYPES_IMAGE) . '))(\?(\d+)x(\d+))?/is', $file, $matches);

		$Image = new Image($matches[1], $matches[3] ?? $width, $matches[4] ?? $height, $crop);

		$this->image = AM_BASE_URL . $Image->file;
		$this->width = $Image->width;
		$this->height = $Image->height;
	}
}
