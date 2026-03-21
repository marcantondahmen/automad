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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\API;

use Automad\Core\FileSystem;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Lock class handles locking of controllers by a given app instance id.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class ControllerLock {
	const DIR = AM_DIR_TMP . '/locks';

	/**
	 * Verify locked controller.
	 *
	 * @param string $controller
	 * @param string $url
	 * @param string $instanceId
	 * @return bool
	 */
	public static function isLocked(string $controller, string $url, string $instanceId): bool {
		if (empty($instanceId)) {
			return false;
		}

		$lockFile = self::getLockFile($controller, $url);

		if (!is_readable($lockFile)) {
			return false;
		}

		$lockId = file_get_contents($lockFile);

		return ($lockId != $instanceId);
	}

	/**
	 * Set a lock.
	 *
	 * @param string $controller
	 * @param string $url
	 * @param string $instanceId
	 * @return bool
	 */
	public static function set(string $controller, string $url, string $instanceId): bool {
		return FileSystem::write(self::getLockFile($controller, $url), $instanceId);
	}

	/**
	 * Get the lock file path for a given controller and url.
	 *
	 * @param string $controller
	 * @param string $url
	 * @return string
	 */
	private static function getLockFile(string $controller, string $url): string {
		$controller = Str::sanitize($controller);
		$lockFileName = $url ? $controller . '-' . md5($url) . '.lock' : "{$controller}.lock";

		return self::DIR . "/$lockFileName";
	}
}
