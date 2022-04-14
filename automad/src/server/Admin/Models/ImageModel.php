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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin\Models;

use Automad\Admin\UI\Utils\Messenger;
use Automad\Admin\UI\Utils\Text;
use Automad\Core\FileSystem;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Image controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2022 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ImageModel {
	/**
	 * Save an image.
	 *
	 * @param string $path
	 * @param string $name
	 * @param string $extension
	 * @param string $base64
	 * @param Messenger $Messenger
	 */
	public static function save(string $path, string $name, string $extension, string $base64, Messenger $Messenger) {
		$data = preg_replace('/^data:image\/[a-z]+;base64,/', '', $base64);
		$data = base64_decode($data);

		$name = $path . Str::slug($name) . '.' . $extension;

		if (FileSystem::write($name, $data) === false) {
			$Messenger->setError(Text::get('couldNotSaveError') . ' ' . $name);
		}
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
