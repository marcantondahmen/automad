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

namespace Automad\Controllers;

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\I18n;
use Automad\Engine\View;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Page controller class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageController {
	/**
	 * Render a public page.
	 *
	 * @return string
	 */
	public static function render(): string {
		$Cache = new Cache();
		$I18n = I18n::get();

		if ($Cache->pageCacheIsApproved()) {
			return $Cache->readPageFromCache();
		}

		$Automad = $Cache->getAutomad();

		$I18n->apply($Automad);

		$View = new View($Automad);
		$output = $View->render();

		if ($Automad->currentPageExists()) {
			$Cache->writePageToCache($output);
		} else {
			Debug::log(AM_REQUEST, 'Page not found! Caching will be skipped!');
		}

		return $output;
	}
}
