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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Cache controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class CacheController {
	/**
	 * Clear the cache.
	 *
	 * @return Response the response object
	 */
	public static function clear(): Response {
		$Response = new Response();
		Cache::clear();

		return $Response->setSuccess(Text::get('cacheClearedSuccess'));
	}

	/**
	 * Purge the cache directory.
	 *
	 * @return Response the response object
	 */
	public static function purge(): Response {
		$Response = new Response();
		$tempDir = FileSystem::purgeCache();

		if ($tempDir) {
			Debug::log($tempDir, 'Temp directory');

			return $Response->setSuccess(Text::get('cachePurgedSuccess'));
		}

		return $Response->setError(Text::get('cachePurgedError'));
	}
}
