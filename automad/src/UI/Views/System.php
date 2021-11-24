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

use Automad\UI\Components\System\Cache;
use Automad\UI\Components\System\ConfigFile;
use Automad\UI\Components\System\Debug;
use Automad\UI\Components\System\Feed;
use Automad\UI\Components\System\Headless;
use Automad\UI\Components\System\Language;
use Automad\UI\Components\System\Overview;
use Automad\UI\Components\System\Update;
use Automad\UI\Components\System\Users;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\URLHashes;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The package manager page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class System extends AbstractView {
	/**
	 * Render body.
	 *
	 * @return string the rendered items
	 */
	protected function body() {
		$fn = $this->fn;
		$hashes = URLHashes::get();
		$Text = Text::getObject();

		return <<< HTML
			<ul class="uk-subnav uk-subnav-pill uk-margin-top">
				<li class="uk-disabled uk-hidden-small"><i class="uk-icon-sliders"></i></li>
				<li><a href="">{$Text->sys_title}</a></li>
			</ul>
			<div class="am-sticky am-system-switcher">
				<div class="am-switcher-bar uk-flex">
					<div class="am-switcher-tabs">
						<a href="#{$hashes->system->overview}" class="am-switcher-link">
							⟵
						</a>
					</div>
					<div 
					class="am-switcher uk-dropdown-close uk-button-dropdown uk-flex-item-1" 
					data-uk-dropdown="{mode:'click'}"
					>
						<div class="uk-button uk-button-large uk-width-1-1 uk-text-left">
							<div class="uk-flex uk-flex-space-between">
								<span class="am-switcher-dropdown-label">
									{$Text->sys_title}
								</span>
								<span><i class="uk-icon-caret-down"></i></span>
							</div>
						</div>
						<div class="uk-dropdown">
							<ul class="uk-nav uk-nav-dropdown">
								<li><a class="am-switcher-link" href="#{$hashes->system->cache}">{$Text->sys_cache}</a></li>
								<li><a class="am-switcher-link" href="#{$hashes->system->users}">{$Text->sys_user}</a></li>
								<li><a class="am-switcher-link" href="#{$hashes->system->update}">{$Text->sys_update}</a></li>
								<li><a class="am-switcher-link" href="#{$hashes->system->feed}">{$Text->sys_feed}</a></li>
								<li><a class="am-switcher-link" href="#{$hashes->system->language}">{$Text->sys_language}</a></li>
								<li><a class="am-switcher-link" href="#{$hashes->system->headless}">{$Text->sys_headless}</a></li>
								<li><a class="am-switcher-link" href="#{$hashes->system->debug}">{$Text->sys_debug}</a></li>
								<li><a class="am-switcher-link" href="#{$hashes->system->config}">{$Text->sys_config}</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div data-am-switcher-item="#{$hashes->system->overview}">{$fn(Overview::render())}</div>
			<div class="uk-margin-top">
				<div data-am-switcher-item="#{$hashes->system->cache}">{$fn(Cache::render())}</div>
				<div data-am-switcher-item="#{$hashes->system->users}">{$fn(Users::render())}</div>
				<div data-am-switcher-item="#{$hashes->system->update}">{$fn(Update::render())}</div>
				<div data-am-switcher-item="#{$hashes->system->feed}">{$fn(Feed::render())}</div>
				<div data-am-switcher-item="#{$hashes->system->language}">{$fn(Language::render())}</div>
				<div data-am-switcher-item="#{$hashes->system->headless}">{$fn(Headless::render())}</div>
				<div data-am-switcher-item="#{$hashes->system->debug}">{$fn(Debug::render())}</div>
				<div data-am-switcher-item="#{$hashes->system->config}">{$fn(ConfigFile::render())}</div>
			</div>
		HTML;
	}

	/**
	 * Get the title for the dashboard view.
	 *
	 * @return string the rendered items
	 */
	protected function title() {
		$title = Text::get('sys_title');

		return "$title &mdash; Automad";
	}
}
