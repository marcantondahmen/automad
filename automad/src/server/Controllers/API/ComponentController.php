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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Cache;
use Automad\Core\PublicationState;
use Automad\Core\Request;
use Automad\Core\Session;
use Automad\Core\Text;
use Automad\Stores\ComponentStore;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The component controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ComponentController {
	/**
	 * Save or get shared components.
	 */
	public static function data(): Response {
		$Response = new Response();
		$components = Request::post('components');
		$ComponentStore = new ComponentStore();

		if (isset($components) && is_array($components)) {
			if (filemtime($ComponentStore->getFile()) > Request::post('fetchTime')) {
				return $Response->setError(Text::get('preventDataOverwritingError'))->setCode(403);
			}

			if ($ComponentStore->setState(PublicationState::DRAFT, array('components' => $components))->save()) {
				Cache::clear();

				return $Response;
			}

			return $Response->setError(Text::get('componentsSavingError'));
		}

		$components = $ComponentStore->getState(empty(Session::getUsername())) ?? array();

		return $Response->setData($components);
	}

	/**
	 * Discard a draft and revert content to the last published version.
	 *
	 * @return Response the response object
	 */
	public static function discardDraft(): Response {
		$Response = new Response();

		$ComponentStore = new ComponentStore();
		$ComponentStore->setState(PublicationState::DRAFT, array())->save();

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
		$ComponentStore = new ComponentStore();

		return $Response->setData(
			array(
				'isPublished' => $ComponentStore->isPublished(),
				'lastPublished' => $ComponentStore->lastPublished()
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

		$ComponentStore = new ComponentStore();

		if ($ComponentStore->publish()) {
			$Response->setSuccess(Text::get('componentsPublishedSuccess'));
		}

		Cache::clear();

		return $Response;
	}
}
