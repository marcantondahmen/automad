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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Messenger;
use Automad\Core\PublicationState;
use Automad\Core\Request;
use Automad\Core\Text;
use Automad\Stores\DataStore;
use Automad\System\Fields;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Shared data controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SharedController {
	/**
	 * Send form when there is no posted data in the request or save data if there is.
	 *
	 * @return Response the response object
	 */
	public static function data(): Response {
		$Response = new Response();
		$Automad = Automad::fromCache();
		$Shared = $Automad->Shared;
		$data = Request::post('data');
		$DataStore = new DataStore();

		if (!empty($data) && is_array($data)) {
			if (filemtime($DataStore->getFile()) > Request::post('dataFetchTime')) {
				return $Response->setError(Text::get('preventDataOverwritingError'))->setCode(403);
			}

			$Messenger = new Messenger();

			$Response->setReload($Shared->save($data, $Messenger));
			$Response->setError($Messenger->getError());

			return $Response;
		}

		$ThemeCollection = new ThemeCollection();
		$mainThemeName = $Shared->get(Fields::THEME) ? $Shared->get(Fields::THEME) : array_keys($ThemeCollection->getThemes())[0];
		$Theme = $ThemeCollection->getThemeByKey($mainThemeName);
		$keys = isset($Theme) ? Fields::inTheme($Theme) : array();

		$supportedFields = array_merge(
			array_fill_keys(Fields::$reserved, ''),
			array_fill_keys($keys, ''),
		);

		$fields = array_intersect_key(
			array_merge(
				$supportedFields,
				$Shared->data
			),
			$supportedFields
		);

		$unusedKeys = array_diff(array_keys($Shared->data), array_keys($fields));
		$unusedFields = array_intersect_key($Shared->data, array_fill_keys($unusedKeys, ''));

		Debug::log($unusedFields, 'Unused data');

		return $Response->setData(array('fields' => $fields, 'unused' => $unusedFields));
	}

	/**
	 * Discard a draft and revert content to the last published version.
	 *
	 * @return Response the response object
	 */
	public static function discardDraft(): Response {
		$Response = new Response();

		$DataStore = new DataStore();
		$DataStore->setState(PublicationState::DRAFT, array())->save();

		Cache::clear();

		return $Response->setReload(true);
	}

	/**
	 * Get the publication state for shared data.
	 *
	 * @return Response
	 */
	public static function getPublicationState(): Response {
		$Response = new Response();
		$DataStore = new DataStore();

		return $Response->setData(
			array(
				'isPublished' => $DataStore->isPublished(),
				'lastPublished' => $DataStore->lastPublished()
			)
		);
	}

	/**
	 * Publish shared data.
	 *
	 * @return Response
	 */
	public static function publish(): Response {
		$Response = new Response();
		$Messenger = new Messenger();
		$Automad = Automad::fromCache();
		$Shared = $Automad->Shared;

		$Shared->publish($Messenger);
		$Response->setError($Messenger->getError());
		$Response->setSuccess($Messenger->getSuccess());

		return $Response;
	}
}
