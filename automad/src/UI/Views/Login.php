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
		$fn = $this->fn;

		return <<< HTML
			<div class="uk-width-medium-1-2 uk-container-center">
				<h1>
					{$fn($this->Automad->Shared->get(AM_KEY_SITENAME))}
				</h1>
				<form class="uk-form uk-margin-large-top" method="post">
					<input 
					class="uk-form-controls uk-width-1-1" 
					type="text" 
					name="name-or-email" 
					placeholder="{$fn(Text::get('login_name_or_email'))}" 
					required 
					/>
					<input 
					class="uk-form-controls uk-width-1-1 uk-margin-small-bottom" 
					type="password" 
					name="password" 
					placeholder="{$fn(Text::get('login_password'))}" 
					required 
					/>
					<div class="uk-flex uk-flex-space-between">
						<a href="{$fn(AM_BASE_INDEX . '/')}" class="uk-button uk-button-link">
							<i class="uk-icon-close"></i>&nbsp;
							{$fn(Text::get('btn_cancel'))}
						</a>
						<span>
							<a href="{$fn(AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=ResetPassword')}" class="uk-button uk-button-link">
								{$fn(Text::get('btn_forgot_password'))}
							</a>
							<button type="submit" class="uk-button uk-button-success">
								{$fn(Text::get('btn_login'))}
							</button>
						</span>
					</div>
				</form>
			</div>
			{$fn($this->error($error))}
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

	/**
	 * Render the error notification in case there is one.
	 *
	 * @param string|null $error
	 * @return string the rendered notification
	 */
	private function error(?string $error = null) {
		if (!empty($error)) {
			return <<< HTML
				<script type="text/javascript">
					Automad.Notify.error('$error');
				</script>
HTML;
		}
	}
}
