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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers;

use Automad\API\Response;
use Automad\Core\Cache;
use Automad\Models\AppModel;
use Automad\System\Fields;
use Automad\System\ThemeCollection;
use Automad\UI\Utils\SwitcherSections;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The App controller handles all requests related to the app state of the dashboard.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class AppController {
	/**
	 * Send the minimum static data that is required to bootstrap the dashboard app.
	 *
	 * /api/App/boostrap
	 */
	public static function bootstrap() {
		$Response = new Response;
		$ThemeCollection = new ThemeCollection();

		$Response->setData(array(
			'version' => AM_VERSION,
			'sections' => SwitcherSections::get(),
			'text' => Text::getObject(),
			'themes' => $ThemeCollection->getThemes(),
			'base' => AM_BASE_URL,
			'baseIndex' => AM_BASE_INDEX,
			'dashboard' => AM_BASE_INDEX . AM_PAGE_DASHBOARD,
			'reservedFields' => Fields::$reserved
		));

		return $Response;
	}

	/**
	 * Send updated dynamic data that is required to update the dashboard app state.
	 *
	 * /api/App/updateState
	 */
	public static function updateState() {
		$Response = new Response;
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();

		$Response->setData(array(
			'tags' => $Automad->getPagelist()->getTags(),
			'pages' => AppModel::pages($Automad),
			'jumpbar' => AppModel::autocompleteJumpbar($Automad),
			'autocomplete' => AppModel::autocompleteLinks($Automad),
			'sitename' => $Automad->Shared->get(AM_KEY_SITENAME)
		));

		return $Response;
	}
}
