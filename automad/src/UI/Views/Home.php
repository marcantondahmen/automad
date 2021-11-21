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

use Automad\Core\Cache;
use Automad\Core\Selection;
use Automad\Core\Str;
use Automad\UI\Components\Alert\Danger;
use Automad\UI\Components\Grid\Pages;
use Automad\UI\Components\Status\Button;
use Automad\UI\Models\UserCollectionModel;
use Automad\UI\Utils\Session;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\URLHashes;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The home page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Home extends AbstractView {
	/**
	 * Render body.
	 *
	 * @return string the rendered items
	 */
	protected function body() {
		$Cache = new Cache();
		$mTime = intval($Cache->getSiteMTime());
		$Selection = new Selection($this->Automad->getCollection());
		$Selection->sortPages(AM_KEY_MTIME . ' desc');
		$latestPages = $Selection->getSelection(false, false, 0, 12);

		$fn = $this->fn;

		return <<< HTML
			<div class="uk-margin-large-top">
				<h1>
					{$fn($this->Automad->Shared->get(AM_KEY_SITENAME))}
					<a href="?view=Shared" class="am-button-title">
						<i class="uk-icon-pencil"></i>
					</a>
				</h1>
			</div>
			<p class="uk-text-small uk-margin-large-bottom">	
				<i class="uk-icon-hdd-o uk-icon-justify"></i>&nbsp;
				{$fn(getenv('SERVER_NAME'))}
				<br>
				<i class="uk-icon-heart-o uk-icon-justify"></i>&nbsp;
				{$fn(Text::get('dashboard_modified'))}
				{$fn(date('j. M Y, G:i', $mTime))}h
				<br>
				<a 
				href="#am-server-info-modal" 
				class="uk-button uk-button-mini uk-margin-small-top" 
				data-uk-modal
				>
					{$fn(Text::get('btn_more'))}
					&nbsp;<i class="uk-icon-ellipsis-h"></i>
				</a>
			</p>
			{$fn($this->missingEmailAlert())}
			<ul class="uk-grid uk-grid-width-medium-1-3 uk-margin-small-top">
				{$fn($this->editButton())}
				<li class="uk-margin-small-bottom">
					{$fn(Button::render('cache', URLHashes::get()->system->cache))}
				</li>
				<li class="uk-margin-small-bottom">
					{$fn(Button::render('update', URLHashes::get()->system->update))}
				</li>
			</ul>
			<div class="uk-margin-large-top">
				<h2>{$fn(Text::get('dashboard_recently_edited'))}</h2>
				{$fn(Pages::render($latestPages))}
			</div>
			<div id="am-server-info-modal" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						{$fn(getenv('SERVER_NAME'))}
						<a class="uk-modal-close uk-close"></a>
					</div>
					<p>
						Automad Version:<br /> 
						{$fn(AM_VERSION)}
					</p>
					<p>
						Operating System:<br />
						{$fn(php_uname('s') . ' / ' . php_uname('r'))}
					</p>
					<p>
						Server Software:<br />
						{$fn(getenv('SERVER_SOFTWARE'))}
					</p>
					<p>
						PHP:<br /> 
						{$fn(phpversion() . ' / ' . php_sapi_name())}
					</p>
					<hr>
					<div class="uk-alert uk-margin-top-remove" data-icon="&#xf1fe">
						{$fn(Text::get('dashboard_memory'))}
						{$fn((memory_get_peak_usage(true) / 1048576))} M
						({$fn(ini_get('memory_limit'))})
					</div>
					<div class="uk-modal-footer uk-text-right">
						<button type="button" class="uk-modal-close uk-button">
							<i class="uk-icon-close"></i>&nbsp;
							{$fn(Text::get('btn_close'))}
						</button>
					</div>
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
		$title = Text::get('dashboard_title');

		return "$title &mdash; Automad";
	}

	/**
	 * Render the main edit button.
	 *
	 * @return string the rendered button
	 */
	private function editButton() {
		$fn = $this->fn;

		if (AM_HEADLESS_ENABLED) {
			return <<< HTML
				<li class="uk-margin-small-bottom">
					<a 
					href="?view=System#{$fn(URLHashes::get()->system->headless)}"
					class="uk-button uk-button-success uk-button-large uk-text-truncate uk-width-1-1 uk-text-left"
					>
						<i class="uk-icon-toggle-on uk-icon-justify"></i>&nbsp;
						{$fn(Text::get('sys_headless_enable'))}
					</a>
				</li>
			HTML;
		} else {
			return <<< HTML
				<li class="uk-margin-small-bottom">
					<a href="{$fn(AM_BASE_INDEX . '/')}" 
					class="uk-button uk-button-primary uk-button-large uk-width-1-1 uk-text-left uk-text-truncate"
					>
						<i class="uk-icon-mouse-pointer"></i>&nbsp;
						{$fn(Text::get('btn_inpage_edit'))}
					</a>
				</li>
			HTML;
		}
	}

	/**
	 * Render an alert box in case a user has no email added.
	 *
	 * @return string the rendered alert box
	 */
	private function missingEmailAlert() {
		$UserCollectionModel = new UserCollectionModel();
		$User = $UserCollectionModel->getUser(Session::getUsername());

		if (!$User->email) {
			$fn = $this->fn;

			return <<< HTML
				<a href="?view=System#{$fn(URLHashes::get()->system->users)}">
					{$fn(Danger::render(Text::get('sys_user_alert_no_email')))}
				</a>
			HTML;
		}
	}
}
