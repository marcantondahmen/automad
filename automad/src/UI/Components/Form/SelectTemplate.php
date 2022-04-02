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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Components\Form;

use Automad\Core\Automad;
use Automad\System\ThemeCollection;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The template selection component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SelectTemplate {
	/**
	 * Create a select box containing all installed themes/templates to be included in a HTML form.
	 *
	 * @param Automad $Automad
	 * @param ThemeCollection $ThemeCollection
	 * @param string $name
	 * @param string $selectedTheme
	 * @param string $selectedTemplate
	 * @return string The HTML for the select box including a label and a wrapping div.
	 */
	public static function render(Automad $Automad, ThemeCollection $ThemeCollection, string $name = '', string $selectedTheme = '', string $selectedTemplate = '') {
		$themes = $ThemeCollection->getThemes();
		$mainTheme = $ThemeCollection->getThemeByKey($Automad->Shared->get(AM_KEY_THEME));

		// Create HTML.
		$html = <<< HTML
			<div 
			class="uk-form-select uk-button uk-button-large uk-button-success uk-width-1-1 uk-text-left" 
			data-uk-form-select
			> 
				<span></span>&nbsp;
				<span class="uk-float-right">
					<i class="uk-icon-caret-down"></i>
				</span>
				<select class="uk-width-1-1" name="$name">
		HTML;

		// List templates of current main theme.
		if ($mainTheme) {
			$html .= '<optgroup label="' . ucwords(Text::get('shared_theme')) . '">';

			foreach ($mainTheme->templates as $template) {
				$html .= '<option';

				if (!$selectedTheme && basename($template) === $selectedTemplate . '.php') {
					$html .= ' selected';
				}

				$html .= ' value="' . basename($template) . '">' .
					 '* / ' . ucwords(str_replace(array('_', '.php'), array(' ', ''), basename($template))) .
					 '</option>';
			}

			$html .= '</optgroup>';
		}

		// List all other templated grouped by theme.
		foreach ($themes as $theme) {
			$html .= '<optgroup label="' . $theme->name . '">';

			foreach ($theme->templates as $template) {
				$html .= '<option';

				if ($selectedTheme === $theme->path && basename($template) === $selectedTemplate . '.php') {
					$html .= ' selected';
				}

				$html .= ' value="' . $theme->path . '/' . basename($template) . '">' .
						 $theme->name .
						 ' / ' .
						 ucwords(str_replace(array('_', '.php'), array(' ', ''), basename($template))) .
						 '</option>';
			}

			$html .= '</optgroup>';
		}

		$html .= <<< HTML
				</select>
			</div>
		HTML;

		return $html;
	}
}
