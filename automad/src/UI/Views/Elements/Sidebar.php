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

use Automad\Core\Automad;
use Automad\Core\Request;
use Automad\UI\Components\Logo;
use Automad\UI\Components\Nav\Jumpbar;
use Automad\UI\Components\Nav\SiteTree;
use Automad\UI\Components\Status\Span;
use Automad\UI\Utils\Session;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Dashboard sidebar element.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Sidebar {
	/**
	 * Render the dashboard sidebar.
	 *
	 * @param Automad $Automad
	 * @return string the rendered dashboard sidebar
	 */
	public static function render(Automad $Automad) {
		if (!Session::getUsername()) {
			return false;
		}

		$fn = function ($expression) {
			return $expression;
		};

		$active = function ($condition) {
			if ($condition) {
				return 'uk-active';
			}
		};

		return <<< HTML
			<div id="am-sidebar" class="am-sidebar uk-modal">
				<div class="am-sidebar-modal-dialog uk-modal-dialog uk-modal-dialog-blank">
					<div data-am-scroll-box='{"scrollToItem": ".uk-active"}'>
						<div data-am-site-tree>
							<div class="am-navbar-push uk-visible-large uk-margin-bottom">
								<a 
								href="{$fn(AM_BASE_INDEX . AM_PAGE_DASHBOARD)}"
								class="am-sidebar-logo"
								>
									{$fn(Logo::render())}
								</a>
							</div>
							<div class="am-sidebar-jump uk-visible-small uk-margin-bottom">
								{$fn(Jumpbar::render(Text::get('jumpbar_placeholder')))}
							</div>
							<ul class="uk-nav uk-nav-side uk-margin-small-top">
								<li class="uk-nav-header">
									{$fn(Text::get('sidebar_header_global'))}
								</li>
								{$fn(self::inPageLink(AM_BASE_INDEX . '/', $Automad->Shared->get(AM_KEY_SITENAME)))}
								<li class="{$active((Request::query('view') == 'Search'))}">
									<a href="?view=Search">
										<i class="uk-icon-search uk-icon-justify"></i>&nbsp;
										{$fn(Text::get('search_title'))}
									</a>
								</li>
								<li class="{$active((!Request::query('view')))}">
									<a href="{$fn(AM_BASE_INDEX . AM_PAGE_DASHBOARD)}">
										<i class="uk-icon-tv uk-icon-justify"></i>&nbsp;
										{$fn(Text::get('dashboard_title'))}
									</a>
								</li>
								<li class="{$active((Request::query('view') == 'System'))}">
									<a href="?view=System">
										<i class="uk-icon-sliders uk-icon-justify"></i>&nbsp;
										{$fn(Text::get('sys_title'))}&nbsp;
										{$fn(Span::render('update_badge'))}
									</a>
								</li>
								<li class="{$active((Request::query('view') == 'Shared'))}">
									<a href="?view=Shared">
										<i class="uk-icon-files-o uk-icon-justify"></i>&nbsp;
										{$fn(Text::get('shared_title'))}
									</a>
								</li>
								<li class="{$active((Request::query('view') == 'Packages'))}">
									<a href="?view=Packages">
										<i class="uk-icon-download uk-icon-justify"></i>&nbsp;
										{$fn(Text::get('packages_title'))}&nbsp;
										{$fn(Span::render('outdated_packages'))}
									</a>
								</li>
								<li class="uk-nav-divider"></li>
							</ul>
							{$fn(SiteTree::render($Automad, '', array('view' => 'Page'), false, Text::get('sidebar_header_pages') . '&nbsp;&mdash;&nbsp;' . count($Automad->getCollection())))}
							<ul class="uk-nav uk-nav-side uk-hidden-large">
								<li class="uk-nav-divider"></li>
								<li>
									<a href="?view=Logout">
										<i class="uk-icon-power-off"></i>&nbsp;
										{$fn(Text::get('btn_log_out'))}
										<i class="uk-icon-angle-double-left"></i>
										{$fn(Session::getUsername())}
										<i class="uk-icon-angle-double-right"></i>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		HTML;
	}

	/**
	 * Render a link to the homepage.
	 *
	 * @param string $url
	 * @param string $sitename
	 * @return string the rendered menu item
	 */
	private static function inPageLink(string $url, string $sitename) {
		if (AM_HEADLESS_ENABLED) {
			return false;
		}

		return <<< HTML
			<li>
				<a href="$url">
					<i class="uk-icon-bookmark-o uk-icon-justify"></i>&nbsp;
					$sitename
				</a>
			</li>
		HTML;
	}
}
