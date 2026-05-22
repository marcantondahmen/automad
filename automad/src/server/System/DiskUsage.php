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
use Automad\Core\FileSystem;
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
	const CACHE_LIFETIME = 300;
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
	 * Check whether the disk quota is exeeded.
	 *
	 * @return bool
	 */
	public static function quotaExceeded(): bool {
		if (!AM_DISK_QUOTA) {
			return false;
		}

		return (self::calculate() > AM_DISK_QUOTA);
	}
}
