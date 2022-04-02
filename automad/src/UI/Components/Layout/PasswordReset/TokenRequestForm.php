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
use Automad\UI\Components\Notify\Error;
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
	 * @param string $error
	 * @return string the rendered form.
	 */
	public static function render(string $error = '') {
		$fn = function ($expression) {
			return $expression;
		};

		return <<< HTML
			{$fn(Error::render($error))}
			{$fn(Text::get('reset_password_enter_name_or_email'))}
			<hr>
			<input 
			class="uk-form-controls uk-width-1-1 uk-margin-small-bottom" 
			type="text" 
			name="name-or-email" 
			placeholder="{$fn(Text::get('login_name_or_email'))}" 
			value="{$fn(Request::query('username'))}"
			required
			/>
			<button type="submit" class="uk-button uk-button-success uk-width-1-1">
				{$fn(Text::get('btn_submit'))}
			</button>
		HTML;
	}
}
