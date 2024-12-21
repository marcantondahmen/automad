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
 * Copyright (c) 2024 by Marc Anton Dahmen
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
use Automad\Stores\SharedComponentStore;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Shared components controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SharedComponentController {
	/**
	 * Save or get shared components.
	 */
	public static function data(): Response {
		$Response = new Response();
		$components = Request::post('components');
		$SharedComponentStore = new SharedComponentStore();

		if (isset($components) && is_array($components)) {
			if ($SharedComponentStore->setState(PublicationState::DRAFT, array('components' => $components))->save()) {
				return $Response;
			}

			return $Response->setError(Text::get('sharedComponentsSavingError'));
		}

		$components = $SharedComponentStore->getState(empty(Session::getUsername())) ?? array();

		return $Response->setData($components);
	}

	/**
	 * Discard a draft and revert content to the last published version.
	 *
	 * @return Response the response object
	 */
	public static function discardDraft(): Response {
		$Response = new Response();

		$SharedComponentStore = new SharedComponentStore();
		$SharedComponentStore->setState(PublicationState::DRAFT, array())->save();

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
		$SharedComponentStore = new SharedComponentStore();

		return $Response->setData(
			array(
				'isPublished' => $SharedComponentStore->isPublished(),
				'lastPublished' => $SharedComponentStore->lastPublished()
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

		$SharedComponentStore = new SharedComponentStore();

		if ($SharedComponentStore->publish()) {
			$Response->setSuccess(Text::get('sharedComponentsPublishedSuccess'));
		}

		Cache::clear();

		return $Response;
	}
}
