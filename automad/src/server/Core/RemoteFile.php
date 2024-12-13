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
 * Copyright (c) 2021-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\System\Fetch;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The remote file class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class RemoteFile {
	/**
	 * The local copy in the downloads directory of the external file.
	 */
	private string $localCopy = '';

	/**
	 * The constructor.
	 *
	 * @param string $url
	 */
	public function __construct(string $url) {
		$this->localCopy = $this->download($url);
	}

	/**
	 * Returns the local copy's path.
	 *
	 * @return string The local copy's path.
	 */
	public function getLocalCopy(): string {
		return $this->localCopy;
	}

	/**
	 * Downloads the remote file to the cache/downloads directory.
	 *
	 * @param string $url
	 * @return string The local copy's file path or false
	 */
	private function download(string $url): string {
		$downloads = AM_BASE_DIR . AM_DIR_CACHE . '/downloads';
		FileSystem::makeDir($downloads);
		$sanitized = Str::sanitize(pathinfo(basename($url), PATHINFO_FILENAME), true, 64);
		$file = $downloads . '/' . $sanitized . '.' . hash('crc32', $url);

		$existing = FileSystem::glob("$file*");

		if (!empty($existing)) {
			$file = $existing[0];
			Debug::log(array($url, $file), 'Already downloaded before');

			return $file;
		}

		if (!Fetch::download($url, $file)) {
			Debug::log($url, 'File not found');

			return '';
		}

		if ($extension = FileSystem::getImageExtensionFromMimeType($file)) {
			rename($file, "$file$extension");
			$file = "$file$extension";
		}

		Debug::log(array($url, $file), 'Downloaded');

		return $file;
	}
}
