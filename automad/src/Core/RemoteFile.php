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

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The remote file class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class RemoteFile {
	/**
	 * The local copy in the downloads directory of the external file.
	 */
	private $localCopy = false;

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
	public function getLocalCopy() {
		return $this->localCopy;
	}

	/**
	 * Downloads the remote file to the cache/downloads directory.
	 *
	 * @param string $url
	 * @return string The local copy's file path or false
	 */
	private function download(string $url) {
		$downloads = AM_BASE_DIR . AM_DIR_CACHE . '/downloads';
		FileSystem::makeDir($downloads);
		$file = $downloads . '/' . AM_FILE_PREFIX_CACHE . '_' . sha1($url);

		$existing = FileSystem::glob("$file*");

		if (!empty($existing)) {
			$file = $existing[0];
			Debug::log(array($url, $file), 'Already downloaded before');

			return $file;
		}

		set_time_limit(0);

		$fp = fopen($file, 'w+');

		$options = array(
			CURLOPT_TIMEOUT => 120,
			CURLOPT_FILE => $fp,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_URL => $url
		);

		$curl = curl_init();
		curl_setopt_array($curl, $options);
		curl_exec($curl);

		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200 || curl_errno($curl)) {
			$file = false;
		}

		curl_close($curl);
		fclose($fp);

		if (!$file) {
			Debug::log($url, 'File not found');

			return false;
		}

		if ($extension = FileSystem::getImageExtensionFromMimeType($file)) {
			rename($file, "$file$extension");
			$file = "$file$extension";
		}

		Debug::log(array($url, $file), 'Downloaded');

		return $file;
	}
}
