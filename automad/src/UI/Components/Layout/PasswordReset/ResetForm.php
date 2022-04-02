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

use Automad\UI\Components\Notify\Error;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The reset form.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ResetForm {
	/**
	 * Render the reset form.
	 *
	 * @param string $username
	 * @param string $error
	 * @return string the rendered form.
	 */
	public static function render(string $username, string $error = '') {
		$fn = function ($expression) {
			return $expression;
		};

		return <<< HTML
			{$fn(Error::render($error))}
			{$fn(Text::get('reset_password_enter_new_password'))}
			<hr>
			<input type="hidden" name="username" value="$username">
			<input
			class="uk-form-controls uk-width-1-1"
			type="text"
			name="token"
			placeholder="{$fn(Text::get('reset_password_token'))}"
			required
			/>
			<input
			class="uk-form-controls uk-width-1-1 uk-margin-small-top"
			type="password"
			name="password1"
			placeholder="{$fn(Text::get('sys_user_password'))}"
			required
			/>
			<input
			class="uk-form-controls uk-width-1-1 uk-margin-small-bottom"
			type="password"
			name="password2"
			placeholder="{$fn(Text::get('sys_user_repeat_password'))}"
			required
			/>
			<button type="submit" class="uk-button uk-button-success uk-width-1-1">
				{$fn(Text::get('btn_save_password'))}
			</button>
		HTML;
	}
}
