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
 * Copyright (c) 2019-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Cache;
use Automad\Core\Request;
use Automad\Core\Text;
use Automad\System\Composer;
use Automad\System\Fetch;
use Automad\System\Package;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The PackageManager class provides all methods required by the dashboard to manage packages.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2019-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PackageManagerController {
	/**
	 * The path to the composer.json file.
	 */
	private static string $composerFile = AM_BASE_DIR . '/composer.json';

	/**
	 * Get the array of installed packages.
	 *
	 * @return Response The array with all installed packages.
	 */
	public static function getInstalled(): Response {
		$Response = new Response();

		if (is_readable(self::$composerFile)) {
			$decoded = json_decode(file_get_contents(self::$composerFile), true);
			$installed = array();

			if (is_array($decoded) && !empty($decoded['require'])) {
				$installed = $decoded['require'];
			}

			$Response->setData(array('installed' => $installed));
		}

		return $Response;
	}

	/**
	 * Get a list of outdated packages.
	 *
	 * @return Response the response object
	 */
	public static function getOutdated(): Response {
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
	public static function getThumbnail(): Response {
		// Close session here already in order to prevent blocking other requests.
		session_write_close();
		ignore_user_abort(true);

		$Response = new Response();
		$repository = Request::post('repository');

		return $Response->setData(array('thumbnail' => Package::getThumbnail($repository)));
	}

	/**
	 * Install a package.
	 *
	 * @return Response the response object
	 */
	public static function install(): Response {
		$Response = new Response();
		$package = Request::post('package');

		if (!$package) {
			return $Response;
		}

		$Composer = new Composer();

		if ($error = $Composer->run('require ' . $package)) {
			return $Response->setError($error);
		}

		return $Response->setSuccess(Text::get('packageInstalledSuccess') . '<br>' . $package);
	}

	/**
	 * Pre-fetch all package thumbnails in the background.
	 *
	 * @return Response
	 */
	public static function preFetchThumbnails(): Response {
		$Response = new Response();

		// Close session here already in order to prevent blocking other requests.
		session_write_close();
		ignore_user_abort(true);
		set_time_limit(0);
		ini_set('memory_limit', '-1');

		$packages = json_decode(Fetch::get(AM_PACKAGE_REPO));
		$thumbnails = array();

		foreach ($packages->results as $package) {
			$thumbnails[] = Package::getThumbnail($package->repository);
		}

		return $Response->setData(array('thumbnails' => $thumbnails));
	}

	/**
	 * Remove a package.
	 *
	 * @return Response the response object
	 */
	public static function remove(): Response {
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
	public static function update(): Response {
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
	public static function updateAll(): Response {
		$Response = new Response();
		$Composer = new Composer();

		if ($error = $Composer->run('update')) {
			return $Response->setError($error);
		}

		Cache::clear();

		return $Response->setSuccess(Text::get('packageUpdatedAllSuccess'));
	}
}
