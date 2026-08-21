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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\System\ImageProcessors;

use Automad\Core\Image;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The GD image processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class GdProcessor implements ImageProcessor {
	/**
	 * The resize function.
	 *
	 * @param string $path
	 * @param string $output
	 * @param int $newWidth
	 * @param int $newHeight
	 * @param int $originalWidth
	 * @param int $originalHeight
	 * @param int $requestedWidth
	 * @param int $requestedHeight
	 * @param bool $crop
	 * @return bool
	 */
	public function resize(
		string $path,
		string $output,
		int $newWidth,
		int $newHeight,
		int $originalWidth,
		int $originalHeight,
		int $requestedWidth,
		int $requestedHeight,
		bool $crop
	): bool {
		$getimagesize = @getimagesize($path);

		if (!$getimagesize) {
			return false;
		}

		$type = $getimagesize['mime'];

		switch ($type) {
			case 'image/jpeg':
				$src = imagecreatefromjpeg($path);

				break;
			case 'image/gif':
				$src = imagecreatefromgif($path);

				break;
			case 'image/png':
				$src = imagecreatefrompng($path);

				break;
			case 'image/webp':
				$src = imagecreatefromwebp($path);

				break;
			default:
				$src = false;

				break;
		}

		if (!$src) {
			return false;
		}

		$x = 0;
		$y = 0;

		if ($crop) {
			$originalAspect = $originalWidth / $originalHeight;
			$requestedAspect = $requestedWidth / $requestedHeight;

			if ($originalAspect > $requestedAspect) {
				if ($newHeight > $originalHeight) {
					$requestedAspect = $newWidth / $newHeight;
				}

				$x = Image::pixels((floatval($originalWidth) - (floatval($originalHeight) * floatval($requestedAspect))) / 2.0);
			} else {
				if ($newWidth > $originalWidth) {
					$requestedAspect = $newWidth / $newHeight;
				}

				$y = Image::pixels((floatval($originalHeight) - (floatval($originalWidth) / floatval($requestedAspect))) / 2.0);
			}
			// exit((string) json_encode(array('x' => $x, 'y' => $y, 'aspect' => $requestedAspect, 'ow' => $originalWidth, 'oh' => $originalHeight)));
		}

		$dest = imagecreatetruecolor($newWidth, $newHeight);

		if (!$dest) {
			return false;
		}

		imagealphablending($dest, false);
		imagesavealpha($dest, true);
		imagecopyresampled(
			$dest,
			$src,
			0,
			0,
			$x,
			$y,
			$newWidth,
			$newHeight,
			Image::pixels($originalWidth - (2 * intval($x))),
			Image::pixels($originalHeight - (2 * intval($y)))
		);

		switch ($type) {
			case 'image/jpeg':
				imagejpeg($dest, $output, AM_IMG_JPG_QUALITY);

				break;
			case 'image/gif':
				imagegif($dest, $output);

				break;
			case 'image/png':
				imagepng($dest, $output, AM_IMG_PNG_QUALITY);

				break;
			case 'image/webp':
				imagewebp($dest, $output, AM_IMG_WEBP_QUALITY);

				break;
		}

		$src = null;
		$dest = null;

		return true;
	}
}
