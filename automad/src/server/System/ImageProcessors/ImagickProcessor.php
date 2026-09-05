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

use Automad\App;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The ImageMagick image processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class ImagickProcessor implements ImageProcessor {
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
		if (!extension_loaded('imagick')) {
			App::exit(
				'ImageMagic extension is missing',
				'Please remove the <code>AM_IMG_PROCESSOR</code> entry from your configuration file in order to enable the GD extension instead.'
			);
		}

		$quality = array(
			'jpg' => AM_IMG_JPG_QUALITY,
			'png' => AM_IMG_PNG_QUALITY,
			'webp' => AM_IMG_WEBP_QUALITY
		);

		$Imagick = new \Imagick($path);

		$profiles = $Imagick->getImageProfiles('*', true);

		$Imagick->autoOrient();
		$Imagick->cropThumbnailImage($newWidth, $newHeight);

		$format = str_replace('jpeg', 'jpg', strtolower($Imagick->getImageFormat()));

		if (isset($quality[$format])) {
			$value = $quality[$format];

			if ($format === 'png') {
				$Imagick->setOption('png:compression-level', strval($value));
			} else {
				$Imagick->setImageCompressionQuality($value);
			}
		}

		unset($profiles['exif']);

		foreach ($profiles as $profile => $data) {
			$Imagick->setImageProfile($profile, $data);
		}

		$Imagick->writeImage($output);

		return true;
	}
}
