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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\API;

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The ResponseCache allows for caching API response data objects.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ResponseCache {
	/**
	 * The cache lifetime.
	 */
	const LIFETIME = 3600;

	/**
	 * The response object.
	 */
	private Response $Response;

	/**
	 * The constructor
	 *
	 * @param callable():Response $create
	 */
	public function __construct(callable $create) {
		if (!AM_CACHE_ENABLED) {
			Debug::log(AM_REQUEST, 'Caching is disabled. Creating fresh response');
			$this->Response = $create();

			return;
		}

		$Cache = new Cache();
		$siteMTime = $Cache->getSiteMTime();
		$hash = sha1(serialize($_GET) . serialize($_POST) . serialize($_SESSION));
		$path = strtolower(AM_DIR_TMP . AM_REQUEST . '/' . $hash);
		$responseMTime = is_readable($path) ? intval(filemtime($path)) : 0;

		Debug::log($path, 'Response caching path');

		if (is_readable($path) && $siteMTime < $responseMTime && time() < $responseMTime + ResponseCache::LIFETIME) {
			Debug::log(AM_REQUEST, 'Loading response from cache');
			$this->Response = unserialize(strval(file_get_contents($path)));

			return;
		}

		Debug::log(AM_REQUEST, 'Creating new response');
		$this->Response = $create();

		Debug::log($path, 'Saving response to the cache');
		FileSystem::write($path, serialize($this->Response));
	}

	/**
	 * Get the cached or create response object.
	 *
	 * @return Response
	 */
	public function get(): Response {
		return $this->Response;
	}
}
