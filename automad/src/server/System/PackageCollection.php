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

namespace Automad\System;

use Automad\API\Response;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Messenger;
use Automad\System\Composer\Composer;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The PackageCollection class provides all methods required for handling the packgae collection.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PackageCollection {
	const FILE_OUTDATED_CACHE =	AM_DIR_TMP . '/' . 'outdated_packages';
	const OUTDATED_CACHE_LIFETIME = 600;

	/**
	 * The cached array of items in the packages directory.
	 */
	private static array $packageDirectoryItems = array();

	/**
	 * Clear the outdated cache.
	 */
	public static function clearOutdatedCache(): void {
		Debug::log('Clearing package update cache ...');

		unlink(self::FILE_OUTDATED_CACHE);
	}

	/**
	 * Get the full list of available packages and their install/update state.
	 *
	 * @return array the list of packages
	 */
	public static function get(): array {
		$apiResponse = Fetch::get(AM_PACKAGE_REGISTRY);

		if (empty($apiResponse)) {
			return array();
		}

		$packages = json_decode($apiResponse, true);
		$packages = array_values(
			array_filter($packages, function (array $package) {
				return Package::isValidPackageName($package['name']);
			})
		);
		$outdated = array();
		$installed = array();

		$composerJson = Composer::readConfig();
		$required = $composerJson['require'] ?? array();

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

		return array_map(function (array $pkg) use ($installed, $outdated, $required): array {
			if (!empty($pkg['name'])) {
				$name = $pkg['name'];
				$isOutdated = array_key_exists($name, $outdated);
				$isInstalled = array_key_exists($name, $installed);
				$isDependency = !array_key_exists($name, $required) && $isInstalled;

				$pkgVersion = $pkg['version'] ?? '';

				$pkg['outdated'] = $isOutdated;
				$pkg['isDependency'] = $isDependency;
				$pkg['latest'] = $isOutdated ? $outdated[$name]['latest'] ?? $pkgVersion : $pkgVersion;
				$pkg['installed'] = $isInstalled;
				$pkg['version'] = $isInstalled ? $installed[$name]['version'] ?? $pkgVersion : $pkgVersion;
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
		$outdatedMtime = is_readable(self::FILE_OUTDATED_CACHE)
			? intval(filemtime(self::FILE_OUTDATED_CACHE))
			: 0;

		if ($outdatedMtime + self::OUTDATED_CACHE_LIFETIME > time()) {
			Debug::log('Reading outdated packages from cache ...');

			return FileSystem::readJson(self::FILE_OUTDATED_CACHE);
		}

		Debug::log('Searching for package updates ...');

		$Composer = new Composer();
		$Messenger = new Messenger();
		$buffer = $Composer->run('show -oD -f json', $Messenger);
		$decoded = $Messenger->getData();
		$outdated = array();

		if ($decoded && !empty($decoded['installed'])) {
			$outdated = $decoded['installed'];
		}

		FileSystem::writeJson(self::FILE_OUTDATED_CACHE, $outdated);

		return $outdated;
	}

	/**
	 * Get all items in the packages directory, optionally filtered by a regex string.
	 *
	 * @param string $filter
	 * @return array A filtered list with all items in the packages directory
	 */
	public static function getPackagesDirectoryItems(string $filter = '') {
		if (empty(self::$packageDirectoryItems)) {
			$packagesDir = AM_BASE_DIR . AM_DIR_PACKAGES;

			self::$packageDirectoryItems = FileSystem::listDirectoryRecursively($packagesDir, $packagesDir);
		}

		if ($filter) {
			return array_values(preg_grep($filter, self::$packageDirectoryItems));
		}

		return self::$packageDirectoryItems;
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
