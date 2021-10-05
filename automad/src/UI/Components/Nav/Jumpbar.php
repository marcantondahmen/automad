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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Components\Nav;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The jump bar component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Jumpbar {
	/**
	 * Create a jump bar field.
	 *
	 * @param string $placeholder
	 * @param string $tooltip
	 * @return string The HTML for the jump bar
	 */
	public static function render(string $placeholder = '', string $tooltip = '') {
		if ($tooltip) {
			$tooltip = 'title="' . htmlspecialchars($tooltip) . '" data-uk-tooltip="{pos:\'bottom\'}" ';
		}

		return <<< HTML
			<form 
			class="uk-form uk-width-1-1" 
			data-am-controller="UI::jump" 
			data-am-jumpbar
			>
				<div 
				class="uk-autocomplete uk-width-1-1"
				>
					<input
					class="uk-form-controls uk-width-1-1"
					name="target"
					type="search"
					placeholder="$placeholder"
					$tooltip
					data-am-watch-exclude
					/>
				</div> 
			</form>
		HTML;
	}
}
