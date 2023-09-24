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

use Automad\Core\FileSystem;
use Automad\Core\Image;
use Automad\Core\RemoteFile;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Package class contains a collection of helper methods for handling package data.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Package {
	/**
	 * Get the contaning composer package info by a path somewhere below the package directory.
	 *
	 * @param string $path
	 * @return array the composer package object
	 */
	public static function getContainingPackage(string $path): array {
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
	 * Try to fetch and cache a package thumbnail based on the repository readme file.
	 *
	 * @param string $repository
	 * @return string
	 */
	public static function getThumbnail(string $repository): string {
		$repositorySlug = Str::stripStart($repository, 'https://github.com/');

		if (!preg_match('#\w+/\w+#', $repositorySlug)) {
			return '';
		}

		$lifetime = 604800;
		$cachePath = AM_BASE_DIR . AM_DIR_CACHE . "/packages/$repositorySlug/thumbnail";

		if (is_readable($cachePath) && filemtime($cachePath) > time() - $lifetime) {
			$cachedImageUrl = file_get_contents($cachePath);

			if (!$cachedImageUrl) {
				return '';
			}

			return $cachedImageUrl;
		}

		$readme = self::getReadme($repository);
		$imageUrl = Str::findFirstImage($readme);

		if (!$imageUrl) {
			FileSystem::write($cachePath, '');

			return '';
		}

		$RemoteFile = new RemoteFile($imageUrl);
		$download = $RemoteFile->getLocalCopy();

		$Image = new Image($download, 400, 300, true);
		$thumbnail = AM_BASE_URL . $Image->file;

		FileSystem::write($cachePath, $thumbnail);

		return $thumbnail;
	}

	/**
	 * Get the rendered README for a given package repository.
	 *
	 * @param string $repository
	 * @return string the thumbnail URL
	 */
	private static function getReadme(string $repository): string {
		$repositorySlug = Str::stripStart($repository, 'https://github.com/');

		preg_match(
			'#href="/' . $repositorySlug . '/blob/(\w+)/(readme[\w\.]*)"#i',
			Fetch::get($repository),
			$matches
		);

		if (empty($matches) || empty($matches[1]) || empty($matches[2])) {
			return '';
		}

		$blob = 'https://raw.githubusercontent.com/' . $repositorySlug . '/' . $matches[1] . '/' . $matches[2];
		$raw = Fetch::get($blob);

		return Str::markdown($raw);
	}
}
