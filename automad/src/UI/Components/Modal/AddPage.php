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

namespace Automad\UI\Components\Modal;

use Automad\Core\Automad;
use Automad\System\ThemeCollection;
use Automad\UI\Components\Form\CheckboxPrivate;
use Automad\UI\Components\Form\SelectTemplate;
use Automad\UI\Components\Nav\SiteTree;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The add page modal.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class AddPage {
	/**
	 * Renders the about modal.
	 *
	 * @param Automad $Automad
	 * @param ThemeCollection $ThemeCollection
	 * @return string The rendered HTML
	 */
	public static function render(Automad $Automad, ThemeCollection $ThemeCollection) {
		$fn = function ($expression) {
			return $expression;
		};

		return <<< HTML
			<div id="am-add-page-modal" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						{$fn(Text::get('btn_add_page'))}
						<a href="#" class="uk-modal-close uk-close"></a>
					</div>
					<form class="uk-form uk-form-stacked" data-am-controller="Page::add">
						<input id="am-add-page-input" type="hidden" name="url" value="" />
						<div class="uk-form-row">
							<label 
							for="am-add-page-modal-input-title" 
							class="uk-form-label uk-margin-top-remove"
							>Title</label>
							<input 
							id="am-add-page-modal-input-title" 
							class="uk-form-controls uk-form-large uk-width-1-1" 
							type="text" 
							name="subpage[title]" 
							value="" 
							required 
							/>
						</div>
						{$fn(CheckboxPrivate::render('subpage[private]'))}
						<hr>
						{$fn(self::template($Automad, $ThemeCollection))}
					</form>
					<div class="uk-form-stacked uk-margin-top">
						<label class="uk-form-label uk-margin-top-remove">
							{$fn(Text::get('page_add_location'))}
						</label>
						<div data-am-site-tree="#am-add-page-input">
							{$fn(SiteTree::render($Automad, '', array(), false, false))}
						</div>
					</div>
					<div class="uk-modal-footer uk-text-right">
						<button type="button" class="uk-modal-close uk-button">
							<i class="uk-icon-close"></i>&nbsp;
							{$fn(Text::get('btn_close'))}
						</button>
						<button 
						type="button" 
						class="uk-button uk-button-success" 
						data-am-submit="Page::add"
						>
							<i class="uk-icon-plus"></i>&nbsp;
							{$fn(Text::get('btn_add_page'))}
						</button>
					</div>
				</div>
			</div>
		HTML;
	}

	/**
	 * The template selection dropdown.
	 *
	 * @param object $Automad
	 * @param object $ThemeCollection
	 * @return string the rendered dropdown
	 */
	private static function template($Automad, $ThemeCollection) {
		if (!AM_HEADLESS_ENABLED) {
			$fn = function ($expression) {
				return $expression;
			};

			return <<< HTML
				<div class="uk-form-row">
					<label class="uk-form-label uk-margin-top-remove">
						{$fn(Text::get('page_theme_template'))}
					</label>
					{$fn(SelectTemplate::render($Automad, $ThemeCollection, 'subpage[theme_template]'))}
				</div>
			HTML;
		}
	}
}
