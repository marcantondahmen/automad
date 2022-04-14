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

namespace Automad\UI\Controllers;

use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Request;
use Automad\UI\Components\Layout\SharedData;
use Automad\UI\Response;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Shared data controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SharedController {
	/**
	 * Send form when there is no posted data in the request or save data if there is.
	 *
	 * @return Response the response object
	 */
	public static function data() {
		$Automad = UICache::get();

		if ($data = Request::post('data')) {
			// Save changes.
			$Response = self::save($Automad, $data);
		} else {
			// If there is no data, just get the form ready.
			$SharedData = new SharedData($Automad);
			$Response = new Response();
			$Response->setHtml($SharedData->render());
		}

		return $Response;
	}

	/**
	 * Save shared data.
	 *
	 * @param Automad $Automad
	 * @param array $data
	 * @return Response the response object
	 */
	private static function save(Automad $Automad, array $data) {
		$Response = new Response();

		if (is_writable(AM_FILE_SHARED_DATA)) {
			FileSystem::writeData($data, AM_FILE_SHARED_DATA);
			Cache::clear();

			if (!empty($data[AM_KEY_THEME]) && $data[AM_KEY_THEME] != $Automad->Shared->get(AM_KEY_THEME)) {
				$Response->setReload(true);
			} else {
				$Response->setSuccess(Text::get('success_saved'));
			}
		} else {
			$Response->setError(Text::get('error_permission') . '<br /><small>' . AM_FILE_SHARED_DATA . '</small>');
		}

		return $Response;
	}
}
