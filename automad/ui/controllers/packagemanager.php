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

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The PackageManager class provides all methods required by the dashboard to manage packages.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2019-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PackageManager {
	/**
	 * The path to the composer.json file.
	 */
	private static $composerFile = AM_BASE_DIR . '/composer.json';

	/**
	 * Get a list of outdated packages.
	 *
	 * @return array the $output array
	 */
	public static function getOutdatedPackages() {
		$output = array();

		$Composer = new Composer();
		$output['buffer'] = $Composer->run('show -oD -f json', true);

		return $output;
	}

	/**
	 * Get a list of theme packages available on Packagist
	 * where the installed ones are at the beginning.
	 *
	 * @return array The output array
	 */
	public static function getPackages() {
		$output = array();

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
			$output['html'] = Packages::render($packages);
		} else {
			$output['error'] = Text::get('error_packages');
		}

		return $output;
	}

	/**
	 * Install a package.
	 *
	 * @return array The output array
	 */
	public static function install() {
		$output = array();

		if ($package = Request::post('package')) {
			$Composer = new Composer();
			$output['error'] = $Composer->run('require ' . $package);
			$output['trigger'] = 'composerDone';

			if (!$output['error']) {
				$output['success'] = Text::get('success_installed') . '<br>' . $package;
			}
		}

		return $output;
	}

	/**
	 * Remove a package.
	 *
	 * @return array The output array
	 */
	public static function remove() {
		$output = array();

		if ($package = Request::post('package')) {
			$Composer = new Composer();
			$output['error'] = $Composer->run('remove ' . $package);
			$output['trigger'] = 'composerDone';

			if (!$output['error']) {
				$output['success'] = Text::get('success_remove') . '<br>' . $package;
			}
		}

		return $output;
	}

	/**
	 * Update a single package.
	 *
	 * @return array The output array
	 */
	public static function update() {
		$output = array();

		if ($package = Request::post('package')) {
			$Composer = new Composer();
			$output['error'] = $Composer->run('update --with-dependencies ' . $package);
			$output['trigger'] = 'composerDone';

			if (!$output['error']) {
				$output['success'] = Text::get('success_package_updated') . '<br>' . $package;
				Cache::clear();
			}
		}

		return $output;
	}

	/**
	 * Update all packages.
	 *
	 * @return array The output array
	 */
	public static function updateAll() {
		$output = array();

		$Composer = new Composer();
		$output['error'] = $Composer->run('update');
		$output['trigger'] = 'composerDone';

		if (!$output['error']) {
			$output['success'] = Text::get('success_packages_updated_all');
			Cache::clear();
		}

		return $output;
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
