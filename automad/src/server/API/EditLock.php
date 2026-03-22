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
 * The EditLock class handles locking of controllers by a given app instance id.
 *
 * Locks follow a quite simple concept where a form component can be configured to aquire
 * a lock for an endpoint in combination with an optional resource (the URL of a page).
 * Locks are aquired once when a form is connected to the DOM.
 *
 * Whenever a form is connected in another tab or browser afterwards, that new instance will
 * aquire the lock and the previously used form has no access anymore.
 * The last connected form always owns the lock.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class EditLock {
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
