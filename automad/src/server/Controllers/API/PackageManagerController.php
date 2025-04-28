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
 * Copyright (c) 2019-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Cache;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Core\Text;
use Automad\System\Composer;
use Automad\System\Package;
use Automad\System\PackageCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The PackageManagerController class provides all methods required by the dashboard to manage packages.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2019-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PackageManagerController {
	/**
	 * Get a list of outdated packages.
	 *
	 * @return Response the list of outdated packages
	 */
	public static function getOutdated(): Response {
		$Response = new Response();

		return $Response->setData(array('outdated'=> PackageCollection::getOutdated()));
	}

	/**
	 * Get the package collection including the install/update state.
	 *
	 * @return Response the package collection
	 */
	public static function getPackageCollection(): Response {
		$Response = new Response();

		return $Response->setData(array('packages' => PackageCollection::get()));
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

		if (!Package::isValidPackageName($package)) {
			return $Response;
		}

		$Composer = new Composer();
		$Messenger = new Messenger();
		$exitCode = $Composer->run('require --update-no-dev ' . $package, $Messenger);

		if ($exitCode !== 0) {
			return $Response->setError($Messenger->getError());
		}

		Cache::clear();

		return $Response->setSuccess(Text::get('packageInstalledSuccess') . '<br>' . $package);
	}

	/**
	 * Remove a package.
	 *
	 * @return Response the response object
	 */
	public static function remove(): Response {
		$Response = new Response();
		$package = Request::post('package');

		if (!$package) {
			return $Response;
		}

		$Composer = new Composer();
		$Messenger = new Messenger();
		$exitCode = $Composer->run('remove ' . $package, $Messenger);

		if ($exitCode !== 0) {
			return $Response->setError($Messenger->getError());
		}

		Cache::clear();

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
			if (!Package::isValidPackageName($package)) {
				return $Response;
			}

			$Composer = new Composer();
			$Messenger = new Messenger();
			$exitCode = $Composer->run('update --with-dependencies --no-dev ' . $package, $Messenger);

			if ($exitCode !== 0) {
				return $Response->setError($Messenger->getError());
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
		$Messenger = new Messenger();
		$exitCode = $Composer->run('update --no-dev', $Messenger);

		if ($exitCode !== 0) {
			return $Response->setError($Messenger->getError());
		}

		Cache::clear();

		return $Response->setSuccess(Text::get('packageUpdatedAllSuccess'));
	}
}
