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

namespace Automad\UI\Components\Layout\PasswordReset;

use Automad\UI\Components\Alert\Danger;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The token request form.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class TokenRequestForm {
	/**
	 * Render the token request form.
	 *
	 * @param string|null $error
	 * @return string the rendered form.
	 */
	public static function render(?string $error = null) {
		$fn = function ($expression) {
			return $expression;
		};

		$alert = '';

		if ($error) {
			$alert = Danger::render($error);
		}

		return <<< HTML
				$alert
				<p>
					{$fn(Text::get('reset_password_enter_username'))}
				</p>
				<input 
				class="uk-form-controls uk-width-1-1 uk-margin-small-bottom" 
				type="text" 
				name="username" 
				placeholder="{$fn(Text::get('login_username'))}" 
				required 
				/>
				<div class="uk-text-right">
					<a href="{$fn(AM_BASE_INDEX . '/')}" class="uk-button uk-button-link">
						<i class="uk-icon-close"></i>&nbsp;
						{$fn(Text::get('btn_cancel'))}
					</a>
					<button type="submit" class="uk-button uk-button-success">
						{$fn(Text::get('btn_ok'))}&nbsp;
						<i class="uk-icon-check"></i>
					</button>
				</div>
HTML;
	}
}
