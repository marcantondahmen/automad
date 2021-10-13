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
use Automad\UI\Controllers\UserCollectionController;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The setup page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class CreateUser extends AbstractView {
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
		$error = UserCollectionController::install();

		$fn = $this->fn;

		return <<< HTML
			{$fn(Error::render($error))}
			{$fn(NoUserNavbar::render($this->Automad->Shared->get(AM_KEY_SITENAME), Text::get('install_title')))}
			<div class="uk-width-medium-1-2 uk-container-center" data-am-create-user>
				<div class="uk-panel uk-panel-box">
					<div class="am-text">
						{$fn(Text::get('install_help'))}
					</div>
					<hr>
					<form class="uk-form" method="post">
						<input 
						class="uk-form-controls uk-width-1-1" 
						type="text" 
						name="username" 
						placeholder="{$fn(Text::get('sys_user_name'))}" 
						required
						/>
						<input 
						class="uk-form-controls uk-width-1-1 uk-margin-small-top" 
						type="email" 
						name="email" 
						placeholder="{$fn(Text::get('sys_user_email'))}" 
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
						<button 
						type="submit" 
						class="uk-button uk-button-success uk-width-1-1" 
						>
							<i class="uk-icon-download"></i>&nbsp;
							{$fn(Text::get('btn_accounts_file'))}
						</button>
					</form>
				</div>
				<div class="uk-panel uk-panel-box uk-margin-small-bottom uk-hidden">
					<div class="am-text">
						{$fn(Text::get('install_login'))}
					</div>
					<hr>
					<a href="" class="uk-button uk-button-success uk-width-1-1">
						{$fn(Text::get('btn_login'))}
					</a>
				</div>
			</div>
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
}
