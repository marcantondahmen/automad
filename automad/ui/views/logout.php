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

use Automad\UI\Components\Alert\Danger;
use Automad\UI\Components\Alert\Success;
use Automad\UI\Controllers\User;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The logout page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Logout extends View {
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
		$isLoggedOut = User::logout();
		$fn = $this->fn;

		return <<< HTML
			<div class="uk-width-medium-1-2 uk-container-center">
				<h1>
					{$fn($this->Automad->Shared->get(AM_KEY_SITENAME))}
				</h1>
				{$fn($this->alert($isLoggedOut))}
			</div>
HTML;
	}

	/**
	 * Get the title for the dashboard view.
	 *
	 * @return string the rendered items
	 */
	protected function title() {
		$title = Text::get('log_out_title');

		return "$title &mdash; Automad";
	}

	/**
	 * Render the success or error alert.
	 *
	 * @param boolean $isLoggedOut
	 * @return string the rendered alert box with buttons
	 */
	private function alert($isLoggedOut) {
		$fn = $this->fn;

		if ($isLoggedOut) {
			return <<< HTML
				{$fn(Success::render(Text::get('success_log_out')))}
				<div class="uk-text-right">
					<a 
					href="{$fn(AM_BASE_INDEX . '/')}" 
					class="uk-button uk-button-link"
					>
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
		} else {
			return Danger::render(Text::get('error_log_out'));
		}
	}
}
