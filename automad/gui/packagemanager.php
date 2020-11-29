<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2019-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;
use Automad\System as System;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The PackageManager class provides all methods required by the dashboard to manage packages. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2019-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class PackageManager {


	/**
	 * 	The path to the composer.json file.
	 */
	private static $composerFile = AM_BASE_DIR . '/composer.json';



	/**
	 *	Get the array of installed packages.
	 *
	 *	@return array The array with all installed packages.
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


	/**
	 *	Get a list of theme packages available on Packagist
	 *	where the installed ones are at the beginning.
	 *
	 *	@return array The array with installed and available themes
	 */

	public static function getPackages() {

		// For now only get theme packages and therefore set the tags 
		// parameter to 'theme'.
		$packages = System\Packagist::getPackages('automad-package');
		$installed = array();
		$available = array();
		$installedPackages = self::getInstalled();

		Core\Debug::log($packages, 'Packages on Packagist');
		Core\Debug::log($installedPackages, 'Installed packages');

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

			return array_merge($installed, $available);

		}

	}

}