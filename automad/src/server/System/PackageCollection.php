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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

use Automad\API\Response;
use Automad\Core\Debug;
use Automad\Core\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The PackageCollection class provides all methods required for handling the packgae collection.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PackageCollection {
	/**
	 * Get the full list of available packages and their install/update state.
	 *
	 * @return array the list of packages
	 */
	public static function get(): array {
		$next = AM_PACKAGE_REPO_QUERY;
		$packages = array();

		while (!empty($next)) {
			$data = json_decode(Fetch::get($next), true);
			$next = '';

			if (!empty($data)) {
				$next = $data['next'];

				if (!empty($data['results'])) {
					$packages = array_merge($packages, $data['results']);
				}
			}
		}

		$outdated = array();
		$installed = array();

		foreach (self::getOutdated() as $pkg) {
			if (!empty($pkg['name'])) {
				$outdated[$pkg['name']] = $pkg;
			}
		}

		foreach (self::getInstalled() as $pkg) {
			if (!empty($pkg['name'])) {
				$installed[$pkg['name']] = $pkg;
			}
		}

		return array_map(function ($pkg) use ($installed, $outdated) {
			if (!empty($pkg['name'])) {
				$name = $pkg['name'];
				$isOutdated = array_key_exists($name, $outdated);
				$isInstalled = array_key_exists($name, $installed);

				$pkg['outdated'] = $isOutdated;
				$pkg['latest'] = $isOutdated ? $outdated[$name]['latest'] ?? '' : '';
				$pkg['installed'] = $isInstalled;
				$pkg['version'] = $isInstalled ? $installed[$name]['version'] ?? '' : '';
			}

			return $pkg;
		}, $packages);
	}

	/**
	 * Get a list of outdated packages.
	 *
	 * @return array the response object
	 */
	public static function getOutdated(): array {
		$Composer = new Composer();
		$buffer = $Composer->run('show -oD -f json', true);
		$decoded = json_decode($buffer, true);

		if ($decoded && !empty($decoded['installed'])) {
			return $decoded['installed'];
		}

		return array();
	}

	/**
	 * Get the array of installed packages.
	 *
	 * @return array The array with all installed packages.
	 */
	private static function getInstalled(): array {
		$installedJson = AM_BASE_DIR . '/vendor/composer/installed.json';
		$installed = FileSystem::readJson($installedJson);

		if (!empty($installed) && !empty($installed['packages'])) {
			return $installed['packages'];
		}

		return array();
	}
}
