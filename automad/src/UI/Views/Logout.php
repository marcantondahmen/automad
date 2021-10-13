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
use Automad\UI\Components\Nav\NoUserNavbar;
use Automad\UI\Controllers\SessionController;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The logout page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Logout extends AbstractView {
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
		$isLoggedOut = SessionController::logout();
		$fn = $this->fn;

		return <<< HTML
			{$fn(NoUserNavbar::render($this->Automad->Shared->get(AM_KEY_SITENAME), Text::get('log_out_title')))}
			<div class="uk-width-medium-1-2 uk-container-center uk-margin-large-top">
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
	 * @param bool $isLoggedOut
	 * @return string the rendered alert box with buttons
	 */
	private function alert(bool $isLoggedOut) {
		$fn = $this->fn;

		if ($isLoggedOut) {
			return <<< HTML
				<div class="uk-panel uk-panel-box">
					{$fn(Text::get('success_log_out'))}
					<hr>
					<div class="uk-grid uk-grid-width-medium-1-2" data-uk-margin>
						<div>
							<a 
							href="{$fn(AM_BASE_INDEX . '/')}" 
							class="uk-button uk-width-1-1"
							>
								{$fn(Text::get('btn_home'))}
							</a>
						</div>
						<div>
							<a 
							href="{$fn(AM_BASE_INDEX . AM_PAGE_DASHBOARD)}" 
							class="uk-button uk-button-success uk-width-1-1"
							>
								{$fn(Text::get('btn_login'))}
							</a>
						</div>
					</div>
				</div>
			HTML;
		} else {
			return Danger::render(Text::get('error_log_out'));
		}
	}
}
