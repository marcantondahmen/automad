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
use Automad\System\Composer\Auth;
use Automad\System\Composer\Composer;
use Automad\System\Composer\RepositoryCollection;
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
	 * Install a package from a repository.
	 *
	 * @return Response
	 */
	public static function addRepository(): Response {
		$Response = new Response();
		$Messenger = new Messenger();
		$Composer = new Composer();

		$name = Request::post('name');
		$branch = Request::post('branch');
		$repositoryUrl = rtrim(Request::post('repositoryUrl'), '/');
		$platform = Request::post('platform');

		RepositoryCollection::add($name, $repositoryUrl, $branch, $platform, $Messenger);

		if ($Messenger->getError()) {
			return $Response->setError($Messenger->getError());
		}

		$exitCode = $Composer->run("require {$name}:dev-{$branch}", $Messenger);

		if ($exitCode !== 0) {
			RepositoryCollection::remove($name);
			$Response->setError($Messenger->getError());
			$Composer->run("remove {$name}");
		}

		return $Response;
	}

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
		$packages = PackageCollection::get();

		if (empty($packages)) {
			$Response->setError(Text::get('packageRegistryFetchError'));
		}

		return $Response->setData(array('packages' => $packages));
	}

	/**
	 * Get a list of repositories.
	 *
	 * @return Response
	 */
	public static function getRepositoryCollection(): Response {
		$Response = new Response();

		return $Response->setData(RepositoryCollection::get());
	}

	/**
	 * Get the Composer auth config.
	 *
	 * @return Response
	 */
	public static function getSafeAuth(): Response {
		$Response = new Response();
		$Auth = Auth::get();

		return $Response->setData($Auth->getSafeValues());
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
			$error = $Messenger->getError();

			if (preg_match('/(Removal failed.*?it may be required by another package)/', $error, $matches)) {
				$error = Text::get('packageIsDependencyError');
			}

			return $Response->setError($error);
		}

		Cache::clear();

		return $Response->setSuccess(Text::get('deteledSuccess') . '<br>' . $package);
	}

	/**
	 * Remove a repository and uninstall the package.
	 *
	 * @return Response
	 */
	public static function removeRepository(): Response {
		$Response = new Response();
		$name = Request::post('name');

		if (!$name) {
			return $Response;
		}

		$Composer = new Composer();
		$Messenger = new Messenger();
		$exitCode = $Composer->run('remove ' . $name, $Messenger);

		if ($exitCode !== 0) {
			return $Response->setError($Messenger->getError());
		}

		if (RepositoryCollection::remove($name)) {
			$Response->setSuccess(Text::get('repositoryRemovedSuccess'));
		}

		return $Response;
	}

	/**
	 * Reset the Composer auth config.
	 *
	 * @return Response
	 */
	public static function resetAuth(): Response {
		$Response = new Response();
		$Auth = Auth::get();

		if ($Auth->reset()) {
			$Response->setSuccess(Text::get('composerAuthResetSuccess'));
		}

		return $Response;
	}

	/**
	 * Save the Composer auth config.
	 *
	 * @return Response
	 */
	public static function saveAuth(): Response {
		$Response = new Response();
		$Auth = Auth::get();

		if ($githubToken = Request::post('githubToken')) {
			$Auth->githubToken = $githubToken;
		}

		if ($gitlabToken = Request::post('gitlabToken')) {
			$Auth->gitlabToken = $gitlabToken;
		}

		$Auth->gitlabUrl = Request::post('gitlabUrl') ? Request::post('gitlabUrl') : 'gitlab.com';

		if ($Auth->save()) {
			$Response->setSuccess(Text::get('savedSuccess'));
		} else {
			$Response->setError(Text::get('composerAuthConfigError'));
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

	/**
	 * Update a repository package.
	 *
	 * @return Response the response object
	 */
	public static function updateRepository(): Response {
		$Response = new Response();
		$name = Request::post('name');

		if (!$name) {
			return $Response;
		}

		$Composer = new Composer();
		$Messenger = new Messenger();
		$ref = RepositoryCollection::getPackageVersion($name);

		$exitCode = $Composer->run("remove $name", $Messenger);

		if ($exitCode !== 0) {
			return $Response->setError($Messenger->getError());
		}

		$exitCode = $Composer->run("require $name:$ref", $Messenger);

		if ($exitCode !== 0) {
			return $Response->setError($Messenger->getError());
		}

		Cache::clear();

		return $Response->setSuccess(Text::get('repositoryUpdateSuccess'));
	}
}
