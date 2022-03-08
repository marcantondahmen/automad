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

namespace Automad\Models;

use Automad\Core\Automad;
use Automad\UI\Autocomplete\Jumpbar;
use Automad\UI\Autocomplete\Links;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The App model handles all data modelling related to the app state of the dashboard.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class AppModel {
	/**
	 * Build the autocomplete data for the jumpbar.
	 *
	 * @param Automad $Automad
	 * @return array the rendered data array
	 */
	public static function autocompleteJumpbar(Automad $Automad) {
		return Jumpbar::render($Automad);
	}

	/**
	 * Build the autocomplete data for links.
	 *
	 * @param Automad $Automad
	 * @return array the rendered data array
	 */
	public static function autocompleteLinks(Automad $Automad) {
		return Links::render($Automad);
	}

	/**
	 * Build the pages array that is used to build a nav tree.
	 *
	 * @param Automad $Automad
	 * @return array the rendered data array
	 */
	public static function pages(Automad $Automad) {
		$pages = array();

		foreach ($Automad->getCollection() as $Page) {
			$pages[$Page->origUrl] = array(
				'title' => htmlspecialchars($Page->get(AM_KEY_TITLE)),
				'index' => $Page->index,
				'url' => $Page->origUrl,
				'path' => $Page->path,
				'parentPath' => rtrim(dirname($Page->path), '/') . '/',
				'private' => $Page->private
			);
		}

		return $pages;
	}
}
