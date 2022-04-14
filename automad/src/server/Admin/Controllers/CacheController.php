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

namespace Automad\Admin\Controllers;

use Automad\Admin\API\Response;
use Automad\Admin\UI\Utils\Text;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Cache controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class CacheController {
	/**
	 * Clear the cache.
	 *
	 * @return Response the response object
	 */
	public static function clear() {
		$Response = new Response();
		Cache::clear();
		$Response->setSuccess(Text::get('cacheClearedSuccess'));

		return $Response;
	}

	/**
	 * Purge the cache directory.
	 *
	 * @return Response the response object
	 */
	public static function purge() {
		$Response = new Response();
		$tempDir = FileSystem::purgeCache();

		if ($tempDir) {
			$Response->setSuccess(Text::get('cachePurgedSuccess'));
			Debug::log($tempDir, 'temp directory');
		} else {
			$Response->setError(Text::get('cachePurgedError'));
		}

		return $Response;
	}
}
