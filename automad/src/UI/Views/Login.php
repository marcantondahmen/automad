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

use Automad\UI\Controllers\UserController;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The login page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Login extends View {
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
		$error = UserController::login();
		$fn = $this->fn;

		return <<< HTML
			<div class="uk-width-medium-1-2 uk-container-center">
				<h1>
					{$fn($this->Automad->Shared->get(AM_KEY_SITENAME))}
				</h1>
				<form class="uk-form uk-margin-top" method="post">
					<input 
					class="uk-form-controls uk-width-1-1" 
					type="text" 
					name="username" 
					placeholder="{$fn(Text::get('login_username'))}" 
					required 
					/>
					<input 
					class="uk-form-controls uk-width-1-1 uk-margin-small-bottom" 
					type="password" 
					name="password" 
					placeholder="{$fn(Text::get('login_password'))}" 
					required 
					/>
					<div class="uk-text-right">
						<a 
						href="{$fn(AM_BASE_INDEX . '/')}" 
						class="uk-button uk-button-link"
						>
							{$fn(Text::get('btn_home'))}
						</a>
						<button type="submit" class="uk-button uk-button-success">
							{$fn(Text::get('btn_login'))}
						</button>
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
	 * @param mixed $error
	 * @return string the rendered notification
	 */
	private function error($error) {
		if (!empty($error)) {
			return <<< HTML
				<script type="text/javascript">
					Automad.Notify.error('$error');
				</script>
HTML;
		}
	}
}
