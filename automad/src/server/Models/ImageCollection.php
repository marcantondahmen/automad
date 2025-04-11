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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models;

use Automad\Core\FileSystem;
use Automad\Core\Image;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The image collection model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ImageCollection {
	/**
	 * List all images of a page or the shared data directory.
	 *
	 * @param string $path
	 * @return array
	 */
	public static function list(string $path): array {
		$images = array();
		$globGrep = FileSystem::globGrep(
			$path . '*.*',
			'/\.(' . implode('|', FileSystem::FILE_TYPES_IMAGE) . ')$/i'
		);

		foreach ($globGrep as $file) {
			$image = new Image($file, 250, 250);

			$item = array();
			$item['name'] = basename($file);
			$item['thumbnail'] = AM_BASE_URL . $image->file;

			$images[] = $item;
		}

		return $images;
	}
}
