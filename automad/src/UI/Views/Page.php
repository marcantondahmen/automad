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
use Automad\UI\Components\Nav\Switcher;
use Automad\UI\Utils\Text;

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
			<ul id="am-page-content" class="uk-switcher">
				<li>
					<form 
					class="uk-form uk-form-stacked" 
					data-am-init 
					data-am-controller="Page::data" 
					data-am-url="$url"
					data-am-path="{$fn($Page->get(AM_KEY_PATH))}"
					>
						{$fn(Loading::render())}
					</form>
				</li>
				<li>
					<form 
					class="uk-form uk-form-stacked" 
					data-am-init 
					data-am-controller="FileCollection::edit" 
					data-am-url="$url" 
					data-am-confirm="{$fn(Text::get('confirm_delete_files'))}"
					>
						{$fn(Loading::render())}
					</form>
				</li>
			</ul>
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
		$items = array(
			array(
				'icon' => '<i class="uk-icon-file-text"></i>',
				'text' => Text::get('btn_data')
			),
			array(
				'icon' => '<span class="uk-badge am-badge-folder" data-am-count="[data-am-file-info]"></span>',
				'text' => Text::get('btn_files') . '&nbsp;&nbsp;<span class="uk-badge" data-am-count="[data-am-file-info]"></span>'
			)
		);

		$dropdown = array();

		if ($url != '/') {
			$dropdown = array(
				// Edit data inpage.
				'<a href="' . AM_BASE_INDEX . $url . '">' .
					'<i class="uk-icon-pencil uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('btn_inpage_edit') .
				'</a>',
				// Duplicate Page.
				'<a href="#" data-am-submit="Page::duplicate">' .
					'<i class="uk-icon-clone uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('btn_duplicate_page') .
				'</a>' .
				'<form data-am-controller="Page::duplicate" data-am-url="' . $url . '"></form>',
				// Move Page.
				'<a href="#am-move-page-modal" data-uk-modal>' .
					'<i class="uk-icon-arrows uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('btn_move_page') .
				'</a>',
				// Delete Page.
				'<a href="#" data-am-submit="Page::delete">' .
					'<i class="uk-icon-remove uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('btn_delete_page') .
				'</a>' .
				'<form data-am-controller="Page::delete" data-am-url="' . $url . '" data-am-confirm="' . Text::get('confirm_delete_page') . '">' .
					'<input type="hidden" name="title" value="' . htmlspecialchars($Page->get(AM_KEY_TITLE)) . '" />' .
				'</form>',
				// Copy page URL to clipboard.
				'<a href="#" data-am-clipboard="' . $url . '">' .
					'<i class="uk-icon-link uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('btn_copy_url_clipboard') .
				'</a>'
			);
		}

		return Switcher::render(
			'#am-page-content',
			$items,
			$dropdown,
			$Page->private
		);
	}
}
