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

use Automad\UI\Components\Loading;
use Automad\UI\Components\Modal\Link;
use Automad\UI\Components\Modal\SelectImage;
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
class Shared extends AbstractView {
	/**
	 * Render body.
	 *
	 * @return string the rendered items
	 */
	protected function body() {
		$fn = $this->fn;

		return <<< HTML
			<ul class="uk-subnav uk-subnav-pill uk-margin-top">
				<li class="uk-disabled uk-hidden-small"><i class="uk-icon-files-o"></i></li>
				<li><a href="">{$fn(Text::get('shared_title'))}</a></li>
			</ul>
			{$fn($this->switcher())}
			<ul id="am-shared-content" class="uk-switcher">
				<li>
					<form 
					class="uk-form uk-form-stacked" 
					data-am-init 
					data-am-controller="Shared::data"
					>
						{$fn(Loading::render())}
					</form>
				</li>
				<li>
					<form 
					class="uk-form uk-form-stacked" 
					data-am-init 
					data-am-controller="FileCollection::edit" 
					data-am-confirm="{$fn(Text::get('confirm_delete_files'))}"
					>
						{$fn(Loading::render())}
					</form>
				</li>
			</ul>
			{$fn(SelectImage::render())}
			{$fn(Link::render())}
		HTML;
	}

	/**
	 * Get the title for the dashboard view.
	 *
	 * @return string the rendered items
	 */
	protected function title() {
		$title = Text::get('shared_title');

		return "$title &mdash; Automad";
	}

	/**
	 * Render the switcher menu.
	 *
	 * @return string the switcher markup
	 */
	private function switcher() {
		return Switcher::render('#am-shared-content', array(
			array(
				'icon' => '<i class="uk-icon-file-text"></i>',
				'text' => Text::get('btn_data')
			),
			array(
				'icon' => '<span class="uk-badge am-badge-folder" data-am-count="[data-am-file-info]"></span>',
				'text' => Text::get('btn_files') . '&nbsp;&nbsp;<span class="uk-badge" data-am-count="[data-am-file-info]"></span>'
			)
		));
	}
}
