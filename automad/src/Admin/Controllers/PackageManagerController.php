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

namespace Automad\UI\Controllers;

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\UI\Components\Layout\Packages;
use Automad\UI\Utils\Text;
use Automad\System\Composer;
use Automad\System\Packagist;
use Automad\UI\Response;

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
	 * Get a list of outdated packages.
	 *
	 * @return Response the response object
	 */
	public static function getOutdatedPackages() {
		$Response = new Response();

		$Composer = new Composer();
		$Response->setBuffer($Composer->run('show -oD -f json', true));

		return $Response;
	}

	/**
	 * Get a list of theme packages available on Packagist
	 * where the installed ones are at the beginning.
	 *
	 * @return Response the response object
	 */
	public static function getPackages() {
		$Response = new Response();

		// For now only get theme packages and therefore set the tags
		// parameter to 'theme'.
		$packages = Packagist::getPackages('automad-package');
		$installed = array();
		$available = array();
		$installedPackages = self::getInstalled();

		Debug::log($packages, 'Packages on Packagist');
		Debug::log($installedPackages, 'Installed packages');

		if ($packages) {
			foreach ($packages as $package) {
				$package->info = 'https://packages.automad.org/' . $package->name;

				if (array_key_exists($package->name, $installedPackages)) {
					$package->installed = true;
					$installed[] = $package;
				} else {
					$package->installed = false;
					$available[] = $package;
				}
			}

			$packages = array_merge($installed, $available);
			$Response->setHtml(Packages::render($packages));
		} else {
			$Response->setError(Text::get('error_packages'));
		}

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
			$Response->setError($Composer->run('require ' . $package));
			$Response->setTrigger('composerDone');

			if (!$Response->getError()) {
				$Response->setSuccess(Text::get('success_installed') . '<br>' . $package);
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
			$Response->setError($Composer->run('remove ' . $package));
			$Response->setTrigger('composerDone');

			if (!$Response->getError()) {
				$Response->setSuccess(Text::get('success_remove') . '<br>' . $package);
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
			$Response->setError($Composer->run('update --with-dependencies ' . $package));
			$Response->setTrigger('composerDone');

			if (!$Response->getError()) {
				$Response->setSuccess(Text::get('success_package_updated') . '<br>' . $package);
				Cache::clear();
			}
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
		$Response->setError($Composer->run('update'));
		$Response->setTrigger('composerDone');

		if (!$Response->getError()) {
			$Response->setSuccess(Text::get('success_packages_updated_all'));
			Cache::clear();
		}

		return $Response;
	}

	/**
	 * Get the array of installed packages.
	 *
	 * @return array The array with all installed packages.
	 */
	private static function getInstalled() {
		if (is_readable(self::$composerFile)) {
			$composerArray = json_decode(file_get_contents(self::$composerFile), true);

			if (is_array($composerArray) && !empty($composerArray['require'])) {
				return $composerArray['require'];
			}
		}

		return array();
	}
}
