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

use Automad\Core\Request;
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
					{$fn(Text::get('reset_password_enter_name_or_email'))}
				</p>
				<input 
				class="uk-form-controls uk-width-1-1 uk-margin-small-bottom" 
				type="text" 
				name="name-or-email" 
				placeholder="{$fn(Text::get('login_name_or_email'))}" 
				value="{$fn(Request::query('username'))}"
				required
				/>
				<div class="uk-text-right">
					<a href="{$fn(AM_BASE_INDEX . '/')}" class="uk-button uk-button-link">
						<i class="uk-icon-close"></i>&nbsp;
						{$fn(Text::get('btn_cancel'))}
					</a>
					<button type="submit" class="uk-button uk-button-success">
						{$fn(Text::get('btn_submit'))}
					</button>
				</div>
HTML;
	}
}
