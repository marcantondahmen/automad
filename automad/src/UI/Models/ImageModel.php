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

namespace Automad\UI\Models;

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Image;
use Automad\UI\Components\Layout\SelectImage;
use Automad\UI\Response;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Messenger;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Image controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ImageModel {
	/**
	 * Copy an image resized based on $_POST.
	 *
	 * @param string $filename
	 * @param string $url
	 * @param mixed $width
	 * @param mixed $height
	 * @param mixed $crop
	 * @return Response the response object
	 */
	public static function copyResized(string $filename, string $url, $width, $height, $crop) {
		$Automad = UICache::get();
		$Response = new Response();

		if (!((is_numeric($width) || is_bool($width)) && (is_numeric($height) || is_bool($height)))) {
			$Response->setError(Text::get('error_file_size'));

			return $Response;
		}

		if ($filename) {
			// Get parent directory.
			if ($url) {
				$Page = $Automad->getPage($url);
				$directory = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
			} else {
				$directory = AM_BASE_DIR . AM_DIR_SHARED . '/';
			}

			$file = $directory . $filename;

			Debug::log($file, 'file');

			if (file_exists($file)) {
				if (is_writable($directory)) {
					$img = new Image(
						$file,
						$width,
						$height,
						boolval($crop)
					);

					$cachedFile = AM_BASE_DIR . $img->file;
					$resizedFile = preg_replace(
						'/(\.\w{3,4})$/',
						'-' . floor($img->width) . 'x' . floor($img->height) . '$1',
						$file
					);

					$Messenger = new Messenger();

					if (FileSystem::renameMedia($cachedFile, $resizedFile, $Messenger)) {
						$Response->setSuccess(Text::get('success_created') . ' "' . basename($resizedFile) . '"');
						Cache::clear();
					} else {
						$Response->setError($Messenger->getError());
					}
				} else {
					$Response->setError(Text::get('error_permission') . ' "' . $directory . '"');
				}
			} else {
				$Response->setError(Text::get('error_file_not_found'));
			}
		}

		return $Response;
	}

	/**
	 * Select an image.
	 *
	 * @param string $url
	 * @return string the rendered HTML
	 */
	public static function select(string $url) {
		$Automad = UICache::get();
		$pageImages = array();

		if (!array_key_exists($url, $Automad->getCollection())) {
			$url = '';
		}

		if ($url) {
			$pageImages = FileSystem::globGrep(
				FileSystem::getPathByPostUrl($Automad) . '*.*',
				'/\.(jpg|jpeg|gif|png)$/i'
			);

			sort($pageImages);
		}

		$sharedImages = FileSystem::globGrep(
			AM_BASE_DIR . AM_DIR_SHARED . '/*.*',
			'/\.(jpg|jpeg|gif|png)$/i'
		);

		sort($sharedImages);

		return SelectImage::render($pageImages, $sharedImages);
	}
}
