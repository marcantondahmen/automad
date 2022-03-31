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
 * Copyright (c) 2019-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Packagist class handles all requests to the Packagist API.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2019-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Packagist {
	/**
	 * Get a list op packages from Packagist filtered by type and tag.
	 *
	 * @param string $type
	 * @return array The list of packages
	 */
	public static function getPackages(string $type = '') {
		$query = http_build_query(
			array(
				'type' => $type
			)
		);

		$results = array();
		$url = 'https://packagist.org/search.json?' . $query;

		while ($url) {
			$data = self::request($url);

			if (!empty($data->results)) {
				$results = array_merge($results, $data->results);
			}

			if (!empty($data->next)) {
				$url = $data->next;
			} else {
				$url = false;
			}
		}

		return $results;
	}

	/**
	 * Make a request to the Packagist API.
	 *
	 * @param string $url
	 * @return array The response data
	 */
	private static function request(string $url) {
		$data = array();

		$options = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_URL => $url
		);

		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$output = curl_exec($curl);

		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200 && !curl_errno($curl)) {
			$data = json_decode($output);
		}

		curl_close($curl);

		return $data;
	}
}
