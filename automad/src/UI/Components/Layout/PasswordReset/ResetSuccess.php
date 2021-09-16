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

use Automad\UI\Components\Alert\Success;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The password reset success layout.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ResetSuccess {
	/**
	 * Render the success layout.
	 *
	 * @return string the rendered layout.
	 */
	public static function render() {
		$fn = function ($expression) {
			return $expression;
		};

		return <<< HTML
			{$fn(Success::render(Text::get('success_password_changed')))}
			<div class="uk-text-right">
				<a href="{$fn(AM_BASE_INDEX . '/')}" class="uk-button uk-button-link">
					<i class="uk-icon-home"></i>&nbsp;
					{$fn(Text::get('btn_home'))}
				</a>
				<a 
				href="{$fn(AM_BASE_INDEX . AM_PAGE_DASHBOARD)}" 
				class="uk-button uk-button-success"
				>
					{$fn(Text::get('btn_login'))}
				</a>
			</div>
HTML;
	}
}
