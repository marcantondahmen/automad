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

use Automad\Core\Request;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The search page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Search extends AbstractView {
	/**
	 * Render body.
	 *
	 * @return string the rendered items
	 */
	protected function body() {
		$fn = $this->fn;

		return <<< HTML
			<ul class="uk-subnav uk-subnav-pill uk-margin-top">
				<li class="uk-disabled"><i class="uk-icon-search"></i></li>
				<li><a href="">{$fn(Text::get('search_title'))}</a></li>
			</ul>
			<div class="uk-form" data-am-search>
				<div class="am-sticky uk-form-row">
					<div class="uk-flex">
						<input 
						class="uk-width-1-1" 
						type="search" 
						name="searchValue" 
						placeholder="{$fn(Text::get('search_placeholder'))}"
						value="{$fn(Request::query('search'))}"
						>
						<label 
						class="am-u-button uk-button-large uk-text-nowrap" 
						title="{$fn(Text::get('search_is_regex'))}"
						data-uk-tooltip
						data-am-toggle
						> 
							.*
							<input type="checkbox" name="isRegex">
						</label>
						<label 
						class="am-u-button uk-button-large uk-text-nowrap" 
						title="{$fn(Text::get('search_is_case_sensitive'))}"
						data-uk-tooltip
						data-am-toggle
						> 
							Aa
							<input type="checkbox" name="isCaseSensitive">
						</label>
					</div>
				</div>
				<div class="uk-form-row uk-margin-small-bottom">
					<input 
					class="uk-width-1-1" 
					type="text" 
					name="replaceValue" 
					placeholder="{$fn(Text::get('search_replace_placeholder'))}"
					value=""
					>
				</div>
				<div class="uk-flex uk-flex-space-between">
					<button 
					type="button" 
					class="uk-button uk-button-success" 
					name="replaceSelected"
					>
						<i class="uk-icon-refresh"></i>&nbsp;
						{$fn(Text::get('search_replace_selected'))}
					</button>
					<div>
						<div class="uk-button-group">
							<button type="button" class="uk-button" name="checkAll">
								<span class="uk-hidden-small">
									{$fn(Text::get('search_replace_check_all'))}&nbsp;
								</span>
								<i class="uk-icon-check-circle"></i>
							</button>
							<button type="button" class="uk-button" name="unCheckAll">
								<span class="uk-hidden-small">
									{$fn(Text::get('search_replace_uncheck_all'))}&nbsp;

								</span>
								<i class="uk-icon-circle-thin"></i>
							</button>
						</div>
					</div>
				</div>
				<form class="uk-margin-large-top"></form>
			</div>
		HTML;
	}

	/**
	 * Get the title for the dashboard view.
	 *
	 * @return string the rendered items
	 */
	protected function title() {
		$title = Text::get('search_title');

		return "$title &mdash; Automad";
	}
}
