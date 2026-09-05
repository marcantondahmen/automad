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
	 * @return bool
	 */
	public function resize(
		string $path,
		string $output,
		int $newWidth,
		int $newHeight
	): bool {
		$getimagesize = @getimagesize($path);

		if (!$getimagesize) {
			return false;
		}

		$originalWidth = $getimagesize[0];
		$originalHeight = $getimagesize[1];

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

		$cropX = 0;
		$cropY = 0;

		$newAspect = round($newWidth / $newHeight, 3);
		$originalAspect = round($originalWidth / $originalHeight, 3);

		if ($newAspect !== $originalAspect) {
			if ($originalAspect > $newAspect) {
				if ($newHeight > $originalHeight) {
					$requestedAspect = $newWidth / $newHeight;
				}

				$cropX = Image::pixels((floatval($originalWidth) - (floatval($originalHeight) * floatval($newAspect))) / 2.0);
			} else {
				if ($newWidth > $originalWidth) {
					$requestedAspect = $newWidth / $newHeight;
				}

				$cropY = Image::pixels((floatval($originalHeight) - (floatval($originalWidth) / floatval($newAspect))) / 2.0);
			}
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
			$cropX,
			$cropY,
			$newWidth,
			$newHeight,
			Image::pixels($originalWidth - (2 * intval($cropX))),
			Image::pixels($originalHeight - (2 * intval($cropY)))
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
