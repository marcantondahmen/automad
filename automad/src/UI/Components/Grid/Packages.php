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

namespace Automad\UI\Components\Grid;

use Automad\UI\Components\Card\Package;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The package grid component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Packages {
	/**
	 * Create a grid based package list for the given array of packages.
	 *
	 * @param array $packages
	 * @return string The HTML for the grid
	 */
	public static function render(array $packages) {
		$cards = '';

		foreach ($packages as $package) {
			$cards .= '<li>' . Package::render($package) . '</li>';
		}

		return <<< HTML
			<ul 
			class="uk-grid uk-grid-width-medium-1-3 uk-margin-top" 
			data-uk-grid-margin 
			data-uk-grid-match="{target:'.uk-panel'}"
			>
				$cards
			</ul>
		HTML;
	}
}
