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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

use Automad\Core\FileSystem;
use Automad\Core\Image;
use Automad\Core\RemoteFile;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Package class contains a collection of helper methods for handling package data.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Package {
	/**
	 * Get the contaning composer package info by a path somewhere below the package directory.
	 *
	 * @param string $path
	 * @return array the composer package object
	 */
	public static function getPackageForPath(string $path): array {
		$dir = $path;
		$composerJsonPath = $dir . '/composer.json';

		while ($dir != AM_BASE_DIR . AM_DIR_PACKAGES && !is_readable($composerJsonPath)) {
			$dir = realpath($dir . '/..');
			$composerJsonPath = $dir . '/composer.json';
		}

		if (!is_readable($composerJsonPath)) {
			return array();
		}

		return FileSystem::readJson($composerJsonPath);
	}

	/**
	 * Check whether a package name matches the filter regex in the config.
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function isValidPackageName(string $name): bool {
		return preg_match('#' . AM_PACKAGE_FILTER_REGEX . '#', $name) == 1;
	}
}
