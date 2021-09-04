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

use Automad\UI\Controllers\AccountsController;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The setup page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class CreateUser extends View {
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
		$error = AccountsController::install();

		$fn = $this->fn;

		return <<< HTML
			<div class="uk-width-medium-1-2 uk-container-center">
				<div class="uk-animation-fade">
					<div class="uk-panel uk-panel-box">
						<div class="uk-panel-title">
							<i class="uk-icon-user-plus uk-icon-medium"></i>
						</div>
						<div class="am-text">
							{$fn(Text::get('install_help'))}
						</div>
					</div>
					<form class="uk-form uk-margin-small-top" method="post">
						<input 
						class="uk-form-controls uk-form-large uk-width-1-1" 
						type="text" 
						name="username" 
						placeholder="{$fn(Text::get('sys_user_add_name'))}" 
						/>
						<input 
						class="uk-form-controls uk-width-1-1 uk-margin-small-top" 
						type="password" 
						name="password1" 
						placeholder="{$fn(Text::get('sys_user_add_password'))}" 
						/>
						<input 
						class="uk-form-controls uk-width-1-1 uk-margin-small-bottom" 
						type="password" 
						name="password2" 
						placeholder="{$fn(Text::get('sys_user_add_repeat'))}" 
						/>
						<div class="uk-text-right">
							<button 
							type="submit" 
							class="uk-button uk-button-success" 
							data-uk-toggle="{target:'.uk-animation-fade'}"
							>
								<i class="uk-icon-download"></i>&nbsp;
								{$fn(Text::get('btn_accounts_file'))}
							</button>
						</div>
					</form>
				</div>
				<div class="uk-animation-fade uk-hidden">
					<div class="uk-panel uk-panel-box uk-margin-small-bottom">
						<div class="uk-panel-title">
							<i class="uk-icon-cloud-upload uk-icon-medium"></i>
						</div>
						{$fn(Text::get('install_login'))}
					</div>
					<div class="uk-text-right">
						<a href="" class="uk-button uk-button-success">
							{$fn(Text::get('btn_login'))}
						</a>
					</div>
				</div>
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
		$title = Text::get('install_title');

		return "$title &mdash; Automad";
	}

	/**
	 * Render error notification in case or errors.
	 *
	 * @param mixed $error
	 * @return string the error notification markup
	 */
	private function error($error) {
		if (!empty($error)) {
			return <<< HTML
				<script type="text/javascript">
					Automad.Notify.error('$error');
					$('form input').first().focus();
				</script>
HTML;
		}
	}
}
