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

namespace Automad\System;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A simple cURL wrapper class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Fetch {
	/**
	 * Download a file.
	 *
	 * @param string $url
	 * @param string $file
	 * @return bool
	 */
	public static function download(string $url, string $file): bool {
		set_time_limit(0);

		$fp = fopen($file, 'w+');

		if (!$fp) {
			return false;
		}

		$options = array(
			CURLOPT_TIMEOUT => 300,
			CURLOPT_FILE => $fp,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_URL => $url
		);

		$curl = curl_init();

		if (!$curl) {
			return false;
		}

		curl_setopt_array($curl, $options);
		curl_exec($curl);

		$success = true;

		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200 || curl_errno($curl)) {
			$success = false;
		}

		curl_close($curl);
		fclose($fp);

		return $success;
	}

	/**
	 * A cURL GET request.
	 *
	 * @param string $url
	 * @param array $headers
	 * @return string The output from the cURL get request
	 */
	public static function get(string $url, array $headers = array()): string {
		$data = '';

		$options = array(
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 300,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_URL => $url
		);

		$curl = curl_init();

		if (!$curl) {
			return '';
		}

		curl_setopt_array($curl, $options);
		$output = curl_exec($curl);

		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200 && !curl_errno($curl) && is_string($output)) {
			$data = $output;
		}

		curl_close($curl);

		return $data;
	}
}
