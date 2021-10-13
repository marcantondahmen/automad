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

namespace Automad\UI\Views;

use Automad\UI\Components\Nav\NoUserNavbar;
use Automad\UI\Components\Notify\Error;
use Automad\UI\Controllers\SessionController;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The login page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Login extends AbstractView {
	/**
	 * Disable navbar and sidebar.
	 */
	protected $hasNav = false;

	/**
	 * Render body.
	 *
	 * @return string the rendered items
	 */
	protected function body() {
		$error = SessionController::login();
		$sitename = $this->Automad->Shared->get(AM_KEY_SITENAME);
		$fn = $this->fn;

		return <<< HTML
			{$fn(NoUserNavbar::render($sitename, Text::get('btn_login')))}
			<div class="uk-width-medium-1-2 uk-container-center uk-margin-large-top">
				<div class="uk-panel uk-panel-box">
					<form class="uk-form" method="post">
						<input 
						class="uk-form-controls uk-width-1-1" 
						type="text" 
						name="name-or-email" 
						placeholder="{$fn(Text::get('login_name_or_email'))}" 
						required 
						/>
						<input 
						class="uk-form-controls uk-width-1-1" 
						type="password" 
						name="password" 
						placeholder="{$fn(Text::get('login_password'))}" 
						required 
						/>
						<div class="uk-grid uk-grid-width-medium-1-2">
							<div>
								<a 
								href="{$fn(AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=ResetPassword')}" 
								class="uk-button uk-width-1-1 uk-margin-small-top"
								>
									{$fn(Text::get('btn_forgot_password'))}
								</a>
							</div>
							<div>
								<button 
								type="submit" 
								class="uk-button uk-button-success uk-width-1-1 uk-margin-small-top"
								>
									{$fn(Text::get('btn_login'))}
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			{$fn(Error::render($error))}
		HTML;
	}

	/**
	 * Get the title for the dashboard view.
	 *
	 * @return string the rendered items
	 */
	protected function title() {
		$title = Text::get('login_title');

		return "$title &mdash; Automad";
	}
}
