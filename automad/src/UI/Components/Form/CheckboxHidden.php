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

namespace Automad\UI\Components\Form;

use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The checkbox component to hide a page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class CheckboxHidden {
	/**
	 * 	Create a checkbox to hide a page.
	 *
	 * @param string $key
	 * @param bool $hidden
	 * @return string The HTML for the hidden input field
	 */
	public static function render(string $key, $hidden = false) {
		$Text = Text::getObject();
		$checked = '';

		if ($hidden) {
			$checked = 'checked';
		}

		return <<<HTML
			<div class="uk-form-row">
				<label class="uk-form-label uk-text-truncate">
					$Text->page_visibility
				</label>
				<label 
				class="am-toggle-switch uk-button" 
				data-am-toggle
				>
					$Text->btn_hide_page
					<input 
					id="am-checkbox-hidden" 
					type="checkbox" 
					name="$key"
					$checked 
					/>
				</label>
			</div>
		HTML;
	}
}
