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
	public string $image;

	public string $preload;

	/**
	 * Create a pair of two images, the actual cached image and the tiny preload-background.
	 * The specified $file can either be a remote URL or a local path.
	 *
	 * @param string $file
	 * @param Automad $Automad
	 */
	public function __construct(string $file, Automad $Automad) {
		if (preg_match('/\:\/\//is', $file)) {
			$RemoteFile = new RemoteFile($file);
			$file = $RemoteFile->getLocalCopy();
		} else {
			$file = Resolve::filePath($Automad->Context->get()->path, $file);
		}

		preg_match('/(\/[\w\.\-\/]+(?:jpg|jpeg|gif|png|webp))(\?(\d+)x(\d+))?/is', $file, $matches);

		$file = $matches[1];
		$width = $matches[3] ?? 0;
		$height = $matches[4] ?? 0;

		$Image = new Image($file, $width, $height, true);
		$Preload = new Image(AM_BASE_DIR . $Image->file, 20);

		$this->image = AM_BASE_URL . $Image->file;
		$this->preload = AM_BASE_URL . $Preload->file;
	}
}
