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

namespace Automad\System;

use Automad\Core\Debug;
use Exception;
use FilesystemIterator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The DiskUsage util class contains helper functions for getting disk usage information.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class DiskUsage {
	const CACHE_LIFETIME = 3600;
	const FILE_CACHE = AM_DIR_TMP . '/disk_usage';

	/**
	 * Get the disk usage of the installation in MB.
	 *
	 * @return float the disk usage in MB
	 */
	public static function calculate(): float {
		if (is_readable(self::FILE_CACHE) && intval(filemtime(self::FILE_CACHE)) + self::CACHE_LIFETIME > time()) {
			$diskUsage = floatval(file_get_contents(self::FILE_CACHE));

			Debug::log('Read disk usage from cache');

			return $diskUsage;
		}

		Debug::log('Calculating disk usage ...');

		$bytes = 0.0;
		$dirIterator = new RecursiveDirectoryIterator(AM_BASE_DIR, FilesystemIterator::SKIP_DOTS);

		$filterIterator = new RecursiveCallbackFilterIterator($dirIterator, function ($item) {
			if (is_link($item->getPathname())) {
				return false;
			}

			return true;
		});

		$objects = new RecursiveIteratorIterator($filterIterator);

		foreach ($objects as $object) {
			try {
				$bytes += $object->getSize();
			} catch (Exception $e) {
				Debug::log($e->getMessage());
			}
		}

		$diskUsage = round($bytes / (1024.0 * 1024.0), 2);

		FileSystem::write(self::FILE_CACHE, strval($diskUsage));

		return $diskUsage;
	}

	/**
	 * Clear the disk usage cache.
	 */
	public static function clearCache(): void {
		unlink(self::FILE_CACHE);
	}

	/**
	 * Check whether the disk quota is exeeded.
	 *
	 * @return bool
	 */
	public static function quotaExceeded(): bool {
		if (!AM_DISK_QUOTA) {
			return false;
		}

		$isExceeded = self::calculate() > AM_DISK_QUOTA;

		// Remove cache when exeeded in order to reflect updated disk usage
		// after removing files.
		if ($isExceeded) {
			self::clearCache();
		}

		return $isExceeded;
	}

	/**
	 * Clear the cache and re-calculate disk usage.
	 */
	public static function refresh(): void {
		self::clearCache();
		self::calculate();
	}
}
