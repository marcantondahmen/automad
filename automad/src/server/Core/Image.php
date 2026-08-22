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
 * Copyright (c) 2013-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Core;

use Automad\System\ImageProcessors\GdProcessor;
use Automad\System\ImageProcessors\ImagickProcessor;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Image object represents a resized (and cropped) copy of a given image.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Image {
	const GD = 'gd';
	const IMAGICK = 'imagick';

	/**
	 * The filename of generated image.
	 */
	public string $file = '';

	/**
	 * The height of the generated image.
	 */
	public int $height = 0;

	/**
	 * The height of the source image.
	 */
	public int $originalHeight = 0;

	/**
	 * The width of the source image.
	 */
	public int $originalWidth = 0;

	/**
	 * The width of the generated image.
	 */
	public int $width = 0;

	/**
	 * Cropping parameter.
	 */
	private bool $crop = false;

	/**
	 * The full file system path to the generated image.
	 */
	private string $fileFullPath = '';

	/**
	 * The filename of the source image
	 */
	private string $originalFile;

	/**
	 * The desired height of the new image. (May not be the resulting width, depending on cropping or original image size)
	 */
	private int $requestedHeight = 0;

	/**
	 * The disired width of the new image. (May not be the resulting width, depending on cropping or original image size)
	 */
	private int $requestedWidth = 0;

	/**
	 * The constructor defines the main object properties from the given parameters and initiates the main methods.
	 *
	 * @param string $originalFile
	 * @param float|string $requestedWidth
	 * @param float|string $requestedHeight
	 * @param bool $crop
	 */
	public function __construct(string $originalFile, float|string $requestedWidth = 0, float|string $requestedHeight = 0, bool $crop = false) {
		$this->originalFile = $originalFile;

		if (!$originalFile) {
			return;
		}

		$requestedWidth = floatval($requestedWidth);
		$requestedHeight = floatval($requestedHeight);

		ini_set('memory_limit', '-1');

		if (!is_readable($originalFile) || !is_file($originalFile)) {
			return;
		}

		$getimagesize = @getimagesize($originalFile);

		if (!$getimagesize) {
			return;
		}

		$this->originalWidth = $getimagesize[0];
		$this->originalHeight = $getimagesize[1];

		$this->requestedWidth = self::pixels($requestedWidth ? $requestedWidth : $this->originalWidth);
		$this->requestedHeight = self::pixels($requestedHeight ? $requestedHeight : $this->originalHeight);

		$this->crop = $crop;

		// Get the possible size for the generated image (based on crop and original size).
		$this->calculateSize();

		// Get the filename hash, based on the given settings, to check later, if the file exists.
		$this->file = $this->getImageCacheFilePath();
		$this->fileFullPath = AM_BASE_DIR . $this->file;

		// Check if an image with the generated hash exists already and create the file, when neccassary.
		$this->verifyCachedImage();
	}

	/**
	 * Round value to full pixels.
	 *
	 * @param float|int|string $value
	 * @return int
	 */
	public static function pixels(int|float|string $value): int {
		return intval(round(floatval($value)));
	}

	/**
	 * Calculate the size and pixels to crop for the generated image.
	 */
	private function calculateSize(): void {
		if (!$this->originalWidth || !$this->originalHeight || !$this->requestedWidth || !$this->requestedHeight) {
			return;
		}

		$originalAspect = $this->originalWidth / $this->originalHeight;
		$requestedAspect = $this->requestedWidth / $this->requestedHeight;

		if ($this->crop) {
			if ($originalAspect > $requestedAspect) {
				$w = $this->requestedWidth < $this->originalWidth ? $this->requestedWidth : $this->originalWidth;
				$h = floatval($w) / floatval($requestedAspect);

				if ($h > $this->originalHeight) {
					$h = $this->originalHeight;
				}
			} else {
				$h = $this->requestedHeight < $this->originalHeight ? $this->requestedHeight : $this->originalHeight;
				$w = floatval($h) * floatval($requestedAspect);

				if ($w > $this->originalWidth) {
					$w = $this->originalWidth;
				}
			}
		} else {
			if ($originalAspect > $requestedAspect) {
				$w = $this->requestedWidth < $this->originalWidth ? $this->requestedWidth : $this->originalWidth;
				$h = floatval($w) / floatval($originalAspect);
			} else {
				$h = $this->requestedHeight < $this->originalHeight ? $this->requestedHeight : $this->originalHeight;
				$w = floatval($h) * floatval($originalAspect);
			}
		}

		$this->width = self::pixels($w);
		$this->height = self::pixels($h);
	}

	/**
	 * Create a new (resized and cropped) image from the source image and save that image in the cache directory.
	 */
	private function createImage(): void {
		FileSystem::makeDir(AM_BASE_DIR . Cache::DIR_IMAGES);

		$Processor = AM_IMG_PROCESSOR === self::IMAGICK ? new ImagickProcessor() : new GdProcessor();
		$Processor->resize(
			$this->originalFile,
			$this->fileFullPath,
			$this->width,
			$this->height
		);

		chmod($this->fileFullPath, AM_PERM_FILE);
	}

	/**
	 * Determine the corresponding image file to a source file based on a md5 hash.
	 * That hash is based on the source image's path, mtime, the new width and height and the cropping parameter.
	 * If one parameter changes, the hash will be different, which will result in recreating an image.
	 * Since the mtime is part of the hash, also any modification to the source image will be reflected in a different name.
	 * For each size and cropping setting, a unique filename will be returned, to clearly identify that setting.
	 *
	 * @return string The matching filename for the requested source image, based on its parameters
	 */
	private function getImageCacheFilePath(): string {
		$extension = strtolower(pathinfo($this->originalFile, PATHINFO_EXTENSION));
		$sanitized = Str::sanitize(pathinfo($this->originalFile, PATHINFO_FILENAME), true, 64);

		if ($extension == 'jpeg') {
			$extension = 'jpg';
		}

		// Create unique filename in the cache folder:
		// The hash makes it possible to clearly identify an unchanged file in the cache,
		// since the given hashData will always result in the same hash.
		// So if a file gets requested, the hash is generated from the path, calculated width x height, the mtime from the original and the cropping setting.
		$hashData = $this->originalFile . '-' . $this->width . 'x' . $this->height . '-' . strval(filemtime($this->originalFile)) . '-' . var_export($this->crop, true);
		$hash = hash('crc32', $hashData);

		$file = Cache::DIR_IMAGES . '/' . $sanitized . '.' . $hash . '.' . $extension;

		Debug::log($hashData, 'Hash data for ' . basename($file));

		return $file;
	}

	/**
	 * To verify, if the requested image is up to date, only the existence has to be tested,
	 * since any changes in the source image will be reflected in the filename's hash.
	 */
	private function verifyCachedImage(): void {
		if (!file_exists($this->fileFullPath)) {
			$this->createImage();
		}
	}
}
