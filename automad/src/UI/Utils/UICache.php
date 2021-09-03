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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Utils;

use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Debug;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The UI cache is the cache of the main Automad object including private pages that are
 * only accessible when a user is logged in.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UICache {
	/**
	 * Restore Automad object including private pages from cache or create
	 * a new version and write it to the cache if outdated.
	 *
	 * @return object the Automad object
	 */
	public static function get() {
		$Cache = new Cache();

		if ($Cache->automadObjectCacheIsApproved()) {
			$Automad = $Cache->readAutomadObjectFromCache();
		} else {
			$Automad = new Automad();
			$Cache->writeAutomadObjectToCache($Automad);
			Debug::log('Created a new Automad instance for the dashboard');
		}

		return $Automad;
	}

	/**
	 * Force a rebuild of the UI cache.
	 *
	 * @return object The fresh Automad object
	 */
	public static function rebuild() {
		$Automad = new Automad();
		$Cache = new Cache();
		$Cache->writeAutomadObjectToCache($Automad);
		Debug::log('Rebuilt Automad cache for the dashboard');

		return $Automad;
	}
}
