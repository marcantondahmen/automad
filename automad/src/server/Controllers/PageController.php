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
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Controllers;

use Automad\App;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\I18n;
use Automad\Core\Request;
use Automad\Engine\View;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Page controller class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageController {
	const REVISION_PARAM = 'revision';

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

		if ($hash = strval(Request::query(PageController::REVISION_PARAM))) {
			$Page = $Automad->Context->get();

			if (!$Page->checkout($hash)) {
				App::exit('Revision not found', "The revision $hash for the page [$Page->origUrl] not found!");
			}
		}

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
