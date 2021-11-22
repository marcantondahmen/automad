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

namespace Automad\UI\Views\Elements;

use Automad\Core\Request;
use Automad\UI\Components\Logo;
use Automad\UI\Components\Nav\Jumpbar;
use Automad\UI\Utils\Session;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\URLHashes;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Dashboard navbar element.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Navbar {
	/**
	 * Render the dashboard navbar.
	 *
	 * @return string the rendered dashboard navbar
	 */
	public static function render() {
		if (!Session::getUsername()) {
			return false;
		}

		$fn = function ($expression) {
			return $expression;
		};

		return <<< HTML
			<nav class="am-navbar">
				<div class="am-navbar-nav">
					<div class="am-navbar-logo">
						<a href="{$fn(AM_BASE_INDEX . AM_PAGE_DASHBOARD)}">
							{$fn(Logo::render())}
						</a>
					</div>
					<div class="am-navbar-jump">
						{$fn(Jumpbar::render(Text::get('jumpbar_placeholder'), '[Ctrl + J]'))}
					</div>
					<div class="am-navbar-buttons">
						<div class="am-icon-buttons">
							<span data-am-status="debug_navbar"></span>
							<a 
							href="#am-add-page-modal" 
							class="uk-button uk-button-primary" 
							title="{$fn(Text::get('btn_add_page'))}"
							data-uk-modal
							data-uk-tooltip="{pos:'bottom-right'}"
							>
								<i class="uk-icon-plus"></i>
							</a>
							{$fn(self::saveButton())}
							<div 
							class="uk-position-relative uk-visible-large" 
							data-uk-dropdown="{mode:'click'}"
							>
								<div class="uk-button">
									<i class="uk-icon-ellipsis-v"></i>
								</div>
								<div class="uk-dropdown uk-dropdown-small">
									<ul class="uk-nav uk-nav-dropdown">
										<li>
											<a href="?view=Logout">
												<i class="uk-icon-power-off uk-icon-justify"></i>&nbsp;
												{$fn(Text::get('btn_log_out'))}
												<i class="uk-icon-angle-double-left"></i>
												{$fn(Session::getUsername())}
												<i class="uk-icon-angle-double-right"></i>
											</a>
										</li>
										<li>
											<a href="?view=System#{$fn(URLHashes::get()->system->users)}">
												<i class="uk-icon-user uk-icon-justify"></i>&nbsp;
												{$fn(Text::get('btn_manage_users'))}
											</a>
										</li>
										<li>
											<a href="#am-about-modal" data-uk-modal>
												<i class="uk-icon-lightbulb-o uk-icon-justify"></i>&nbsp;
												{$fn(Text::get('btn_about'))}
											</a>
										</li>
									</ul>
								</div>
							</div>
							<a href="#am-sidebar" 
							class="uk-button uk-hidden-large" 
							data-uk-modal
							>
								<i class="uk-icon-navicon uk-icon-justify"></i>
							</a>
						</div>
					</div>
				</div>
			</nav>
		HTML;
	}

	/**
	 * Generate save button depending on controller type.
	 *
	 * @return string the rendered button
	 */
	private static function saveButton() {
		$view = Request::query('view');
		$handlers = array('Page' => 'Page::data', 'Shared' => 'Shared::data');

		if (isset($handlers[$view])) {
			$title = Text::get('btn_save') . '[Ctrl + S]';
			$submit = $handlers[$view];

			return <<< HTML
				<button
				title="$title" 
				class="uk-button uk-button-success" 
				data-am-submit="$submit" 
				data-uk-tooltip="{pos:'bottom-right'}" 
				disabled
				>
					<i class="uk-icon-check"></i>
				</button>
			HTML;
		}
	}
}
