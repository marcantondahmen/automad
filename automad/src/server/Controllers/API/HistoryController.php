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

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Automad;
use Automad\Core\Debug;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Models\History\History;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page history controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class HistoryController {
	/**
	 * Get the log for the currently requested page.
	 *
	 * @return Response
	 */
	public static function log(): Response {
		$Response = new Response();
		$Automad = Automad::fromCache();
		$url = Request::post('url');
		$Page = $Automad->getPage($url);

		if (!$Page) {
			return $Response;
		}

		$History = History::get($Page->path);

		return $Response->setData($History->log());
	}

	/**
	 * Restore a page to specific revision.
	 *
	 * @return Response
	 */
	public static function restore(): Response {
		$Response = new Response();
		$Messenger = new Messenger();
		$Automad = Automad::fromCache();
		$url = Request::post('url');
		$hash = Request::post('revision');
		$Page = $Automad->getPage($url);
		Debug::log($hash, $url);

		if (!$Page || !$hash) {
			return $Response;
		}

		$History = History::get($Page->path);
		$dashboardUrl = $History->restore($hash, $Page->get(Fields::TITLE), $Messenger);

		if ($dashboardUrl) {
			$Response->setRedirect($dashboardUrl);
		} else {
			$Response->setReload(true);
		}

		return $Response->setError($Messenger->getError());
	}
}
