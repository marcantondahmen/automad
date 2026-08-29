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
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Blocks\Utils;

use Automad\Core\Automad;
use Automad\Core\Image;
use Automad\Core\Resolve;
use Automad\System\RemoteFile;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Img class is a tiny wrapper for resizing local or remote images.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Img {
	/**
	 * The resized image height.
	 */
	public float $height = 0;

	/**
	 * The url to the actual large image.
	 */
	public string $image = '';

	/**
	 * The resized image width.
	 */
	public float $width = 0;

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

		if (empty($file) || !is_readable($file)) {
			return;
		}

		$Image = new Image($file, $width, $height, $crop);

		$this->image = $Image->file ? AM_BASE_URL . $Image->file : '';
		$this->width = $Image->width;
		$this->height = $Image->height;
	}
}
