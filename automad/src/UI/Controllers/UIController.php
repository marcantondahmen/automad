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

use Automad\Core\Request;
use Automad\UI\Components\Autocomplete\Jumpbar;
use Automad\UI\Components\Autocomplete\Link;
use Automad\UI\Components\Form\Field;
use Automad\UI\Response;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The UI controller class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UIController {
	/**
	 * Return the autocomplete values for a search field.
	 *
	 * @return Response the response object
	 */
	public static function autocompleteJump() {
		$Automad = UICache::get();

		return Jumpbar::render($Automad);
	}

	/**
	 * Return the autocomplete values for a link field.
	 *
	 * @return Response the response object
	 */
	public static function autocompleteLink() {
		$Automad = UICache::get();

		return Link::render($Automad);
	}

	/**
	 * Return the UI component for a variable field based on the name.
	 *
	 * @return Response the response object
	 */
	public static function field() {
		$Response = new Response();

		if ($name = Request::post('name')) {
			$Automad = UICache::get();
			$Response->setHtml(Field::render($Automad, $name, '', true));
		}

		return $Response;
	}

	/**
	 * Redirect to a given target URL.
	 *
	 * @return Response the response object
	 */
	public static function jump() {
		$Response = new Response();

		if ($target = Request::post('target')) {
			if (strpos($target, '?view=') !== false || $target == AM_BASE_INDEX) {
				$Response->setRedirect($target);
			}
		}

		return $Response;
	}
}
