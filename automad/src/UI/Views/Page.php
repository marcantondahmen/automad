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

use Automad\Core\Page as CorePage;
use Automad\Core\Request;
use Automad\UI\Components\Alert\Alert;
use Automad\UI\Components\Loading;
use Automad\UI\Components\Modal\Link;
use Automad\UI\Components\Modal\SelectImage;
use Automad\UI\Components\Nav\Breadcrumbs;
use Automad\UI\Components\Nav\SiteTree;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\URLHashes;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page editing page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Page extends AbstractView {
	/**
	 * Render body.
	 *
	 * @return string the rendered items
	 */
	protected function body() {
		$hashes = URLHashes::get();
		$url = Request::query('url');
		$Page = $this->Automad->getPage($url);

		$fn = $this->fn;

		if (!$Page) {
			return Alert::render(
				"{$fn(Text::get('error_page_not_found'))}<br>$url",
				'uk-margin-large-top uk-alert-danger'
			);
		}

		return <<< HTML
			{$fn(Breadcrumbs::render($this->Automad))}
			{$fn($this->switcher($Page, $url))}
			<div class="uk-margin-top">
				<div data-am-switcher-item="#{$hashes->content->data}">
					<form 
					class="uk-form uk-form-stacked" 
					data-am-init 
					data-am-controller="Page::data" 
					data-am-url="$url"
					data-am-path="{$fn($Page->get(AM_KEY_PATH))}"
					>
						{$fn(Loading::render())}
					</form>
				</div>
				<div data-am-switcher-item="#{$hashes->content->files}">
					<form 
					class="uk-form uk-form-stacked" 
					data-am-init 
					data-am-controller="FileCollection::edit" 
					data-am-url="$url" 
					data-am-confirm="{$fn(Text::get('confirm_delete_files'))}"
					>
						{$fn(Loading::render())}
					</form>
				</div>
			</div>
			{$fn(SelectImage::render($url))}
			{$fn(Link::render())}
			<div id="am-move-page-modal" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						{$fn(Text::get('btn_move_page'))}
						<a href="#" class="uk-modal-close uk-close"></a>
					</div>
					<div class="uk-form-stacked">
						<label class="uk-form-label uk-margin-top-remove">
							{$fn(Text::get('page_move_destination'))}
						</label>
						<div data-am-site-tree="#am-move-page-input">
							{$fn(SiteTree::render($this->Automad, '', array(), true, false))}
						</div>
					</div>
					<form data-am-controller="Page::move" data-am-url="$url">
						<input 
						type="hidden" 
						name="title" 
						value="{$fn(htmlspecialchars($Page->get(AM_KEY_TITLE)))}" 
						/>
						<input id="am-move-page-input" type="hidden" name="destination" value="" />
					</form>
					<div class="uk-modal-footer uk-text-right">
						<button type="button" class="uk-modal-close uk-button">
							<i class="uk-icon-close"></i>&nbsp;
							{$fn(Text::get('btn_close'))}
						</button>
						<button 
						type="button" 
						class="uk-button uk-button-success" 
						data-am-submit="Page::move"
						>
							<i class="uk-icon-arrows"></i>&nbsp;
							{$fn(Text::get('btn_move_page'))}
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
		$url = Request::query('url');
		$title = '';

		if ($Page = $this->Automad->getPage($url)) {
			$title = htmlspecialchars($Page->get(AM_KEY_TITLE));
		}

		return "$title &mdash; Automad";
	}

	/**
	 * Render the switcher menu.
	 *
	 * @param CorePage $Page
	 * @param string $url
	 * @return string the switcher markup
	 */
	private function switcher(CorePage $Page, string $url) {
		$fn = $this->fn;
		$private = '';
		$dropdown = '';

		if ($Page->private) {
			$private = '<i class="am-switcher-icon uk-icon-lock" title="' . Text::get('page_private') . '" data-uk-tooltip></i>';
		}

		if ($url != '/') {
			$dropdown = <<< HTML
				<div data-uk-dropdown="{mode:'click', pos:'bottom-right'}">
					<a href="#" class="uk-button uk-button-large">
						<span class="uk-visible-large">{$fn(Text::get('btn_more'))}&nbsp;&nbsp;<i class="uk-icon-caret-down"></i></span>
						<i class="uk-hidden-large uk-icon-ellipsis-v uk-icon-justify"></i>
					</a>
					<div class="uk-dropdown uk-dropdown-small">
						<ul class="uk-nav uk-nav-dropdown">
							<li>
								<a href="{$fn(AM_BASE_INDEX . $url)}">
									<i class="uk-icon-pencil uk-icon-justify"></i>&nbsp;
									{$fn(Text::get('btn_inpage_edit'))}
								</a>
							</li>
							<li>
								<a href="#" data-am-submit="Page::duplicate">
									<i class="uk-icon-clone uk-icon-justify"></i>&nbsp;
									{$fn(Text::get('btn_duplicate_page'))}
								</a>
								<form data-am-controller="Page::duplicate" data-am-url="$url"></form>
							</li>
							<li>
								<a href="#am-move-page-modal" data-uk-modal>
									<i class="uk-icon-arrows uk-icon-justify"></i>&nbsp;
									{$fn(Text::get('btn_move_page'))}
								</a>
							</li>
							<li>
								<a href="#" data-am-submit="Page::delete">
									<i class="uk-icon-remove uk-icon-justify"></i>&nbsp;
									{$fn(Text::get('btn_delete_page'))}
								</a>
								<form 
								data-am-controller="Page::delete" 
								data-am-url="$url" 
								data-am-confirm="{$fn(Text::get('confirm_delete_page'))}"
								>
									<input 
									type="hidden" 
									name="title" 
									value="{$fn(htmlspecialchars($Page->get(AM_KEY_TITLE)))}" 
									/>
								</form>
							</li>
							<li>
								<a href="#" data-am-clipboard="$url">
									<i class="uk-icon-link uk-icon-justify"></i>&nbsp;
									{$fn(Text::get('btn_copy_url_clipboard'))}
								</a>
							</li>
						</ul>
					</div>
				</div>
			HTML;
		}

		return <<< HTML
			<div class="am-sticky">
				<div class="am-switcher am-switcher-bar uk-flex">
					<div class="am-switcher-tabs uk-flex-item-1">
						<a 
						href="#{$fn(URLHashes::get()->content->data)}" 
						class="am-switcher-link"
						>
							<span class="uk-visible-small"><i class="uk-icon-file-text"></i></span>
							<span class="uk-hidden-small">{$fn(Text::get('btn_data'))}</span>
						</a>
						<a 
						href="#{$fn(URLHashes::get()->content->files)}" 
						class="am-switcher-link"
						>
							<span class="uk-visible-small">
								<span class="uk-badge am-badge-folder" data-am-count="[data-am-file-info]"></span>
							</span>
							<span class="uk-hidden-small">
								{$fn(Text::get('btn_files'))}&nbsp;&nbsp;<span class="uk-badge" data-am-count="[data-am-file-info]"></span>
							</span>
						</a>
					</div>
					$private
					$dropdown
				</div>
			</div>
		HTML;
	}
}
