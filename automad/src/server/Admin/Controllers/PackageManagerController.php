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
 * Copyright (c) 2019-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin\Controllers;

use Automad\Admin\API\Response;
use Automad\Admin\UI\Utils\Text;
use Automad\Core\Cache;
use Automad\Core\FileSystem;
use Automad\Core\Image;
use Automad\Core\RemoteFile;
use Automad\Core\Request;
use Automad\Core\Str;
use Automad\System\Composer;
use Automad\System\Fetch;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The PackageManager class provides all methods required by the dashboard to manage packages.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2019-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PackageManagerController {
	/**
	 * The path to the composer.json file.
	 */
	private static $composerFile = AM_BASE_DIR . '/composer.json';

	/**
	 * Get the array of installed packages.
	 *
	 * @return array The array with all installed packages.
	 */
	public static function getInstalled() {
		$Response = new Response();

		if (is_readable(self::$composerFile)) {
			$decoded = json_decode(file_get_contents(self::$composerFile), true);

			if (is_array($decoded) && !empty($decoded['require'])) {
				$Response->setData(array('installed' => $decoded['require']));
			}
		}

		return $Response;
	}

	/**
	 * Get a list of outdated packages.
	 *
	 * @return Response the response object
	 */
	public static function getOutdated() {
		$Response = new Response();

		$Composer = new Composer();
		$buffer = $Composer->run('show -oD -f json', true);
		$decoded = json_decode($buffer, true);

		if ($decoded) {
			$Response->setData(array('outdated' => $decoded['installed']));
		}

		return $Response;
	}

	/**
	 * Get the thumbnail for a given package repository.
	 *
	 * @return Response the response object
	 */
	public static function getThumbnail() {
		$Response = new Response();
		$repository = Request::post('repository');
		$repositorySlug = Str::stripStart($repository, 'https://github.com/');
		$thumbnail = '';
		$lifetime = 216000;

		if (!preg_match('#\w+/\w+#', $repositorySlug)) {
			return $Response;
		}

		$cachePath = AM_BASE_DIR . AM_DIR_CACHE . "/packages/$repositorySlug/thumbnail";

		if (is_readable($cachePath) && filemtime($cachePath) > time() - $lifetime) {
			$thumbnail = file_get_contents($cachePath);
		} else {
			$readme = self::getReadme($repository);
			$imageUrl = Str::findFirstImage($readme);

			if (!$imageUrl) {
				return $Response;
			}

			$RemoteFile = new RemoteFile($imageUrl);
			$download = $RemoteFile->getLocalCopy();

			$Image = new Image($download, 800, 600, true);
			$thumbnail = AM_BASE_URL . $Image->file;

			FileSystem::write($cachePath, $thumbnail);
		}

		$Response->setData(array('thumbnail' => $thumbnail));

		return $Response;
	}

	/**
	 * Install a package.
	 *
	 * @return Response the response object
	 */
	public static function install() {
		$Response = new Response();

		if ($package = Request::post('package')) {
			$Composer = new Composer();

			if ($error = $Composer->run('require ' . $package)) {
				$Response->setError($error);
			} else {
				$Response->setSuccess(Text::get('packageInstalledSuccess') . '<br>' . $package);
			}
		}

		return $Response;
	}

	/**
	 * Remove a package.
	 *
	 * @return Response the response object
	 */
	public static function remove() {
		$Response = new Response();

		if ($package = Request::post('package')) {
			$Composer = new Composer();

			if ($error = $Composer->run('remove ' . $package)) {
				$Response->setError($error);
			} else {
				$Response->setSuccess(Text::get('deteledSuccess') . '<br>' . $package);
			}
		}

		return $Response;
	}

	/**
	 * Update a single package.
	 *
	 * @return Response the response object
	 */
	public static function update() {
		$Response = new Response();

		if ($package = Request::post('package')) {
			$Composer = new Composer();

			if ($error = $Composer->run('update --with-dependencies ' . $package)) {
				return $Response->setError($error);
			}

			Cache::clear();

			return $Response->setSuccess(Text::get('packageUpdatedSuccess') . '<br>' . $package);
		}

		return $Response;
	}

	/**
	 * Update all packages.
	 *
	 * @return Response the response object
	 */
	public static function updateAll() {
		$Response = new Response();
		$Composer = new Composer();

		if ($error = $Composer->run('update')) {
			return $Response->setError($error);
		}

		Cache::clear();

		return $Response->setSuccess(Text::get('packageUpdatedAllSuccess'));
	}

	/**
	 * Get the rendered README for a given package repository.
	 *
	 * @param string $repository
	 * @return string the thumbnail URL
	 */
	private static function getReadme(string $repository) {
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
