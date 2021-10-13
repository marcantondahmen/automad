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

namespace Automad\UI\Components\Layout;

use Automad\Core\Automad;
use Automad\Core\Debug;
use Automad\System\ThemeCollection;
use Automad\UI\Components\Accordion\UnusedVariables;
use Automad\UI\Components\Accordion\Variables;
use Automad\UI\Components\Alert\ThemeReadme;
use Automad\UI\Components\Card\Theme;
use Automad\UI\Components\Form\FieldHidden;
use Automad\UI\Utils\Keys;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The shared data layout component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SharedData {
	/**
	 * The Automad object.
	 */
	private $Automad = null;

	/**
	 * All color keys.
	 */
	private $colorKeys = null;

	/**
	 * The data array.
	 */
	private $data = null;

	/**
	 * A helper to use function within heredoc strings.
	 */
	private $fn = null;

	/**
	 * The data array.
	 */
	private $mainTheme = null;

	/**
	 * All settings variable keys.
	 */
	private $settingKeys = null;

	/**
	 * All text content keys.
	 */
	private $textKeys = null;

	/**
	 * The data array.
	 */
	private $themes = null;

	/**
	 * All unused variable keys.
	 */
	private $unusedDataKeys = null;

	/**
	 * The shared data layout constructor.
	 *
	 * @param Automad $Automad
	 */
	public function __construct(Automad $Automad) {
		$this->Automad = $Automad;
		$this->data = $Automad->Shared->data;

		$ThemeCollection = new ThemeCollection();
		$this->themes = $ThemeCollection->getThemes();
		$this->mainTheme = $ThemeCollection->getThemeByKey($Automad->Shared->get(AM_KEY_THEME));

		$this->fn = function ($expression) {
			return $expression;
		};

		if (!AM_HEADLESS_ENABLED) {
			if ($this->mainTheme) {
				$keys = Keys::inTheme($this->mainTheme);
			} else {
				$keys = array();
			}
		} else {
			$keys = Keys::inTemplate(Headless::getTemplate());

			// Also submit the saved theme form the non-headless mode.
			// The value gets stored in a hidden input field.
			echo FieldHidden::render(AM_KEY_THEME, $Automad->Shared->get(AM_KEY_THEME));
		}

		$this->textKeys = Keys::filterTextKeys($keys);
		$this->colorKeys = Keys::filterColorKeys($keys);
		$this->settingKeys = Keys::filterSettingKeys($keys);
		$this->unusedDataKeys = array_diff(array_keys($this->data), $keys, Keys::$reserved);
	}

	/**
	 * Create the main page data form.
	 *
	 * @return string The rendered HTML
	 */
	public function render() {
		$fn = $this->fn;

		return <<< HTML
			{$fn($this->title())}
			{$fn(ThemeReadme::render($this->mainTheme))}
			<div 
			class="uk-accordion" 
			data-uk-accordion="{duration: 200, showfirst: false, collapse: false}"
			>
				{$fn($this->themes())}
				{$fn(Variables::render($this->Automad, $this->textKeys, $this->data, $this->mainTheme, Text::get('shared_vars_content')))}
				{$fn(Variables::render($this->Automad, $this->colorKeys, $this->data, $this->mainTheme, Text::get('shared_vars_color')))}
				{$fn(Variables::render($this->Automad, $this->settingKeys, $this->data, $this->mainTheme, Text::get('shared_vars_settings')))}
				{$fn(UnusedVariables::render($this->Automad, $this->unusedDataKeys, $this->data, Text::get('shared_vars_unused')))}
			</div>
		HTML;
	}

	/**
	 * The themes section.
	 *
	 * @return string the rendered themes section HTML
	 */
	private function themes() {
		if (AM_HEADLESS_ENABLED) {
			return '';
		}

		Debug::log($this->themes, 'themes');

		$fn = $this->fn;
		$alert = '';
		$themes = '';
		$i = 0;

		foreach ($this->themes as $Theme) {
			$id = 'am-theme-' . ++$i;
			$themes .= "<li>{$fn(Theme::render($Theme, $this->mainTheme, $id))}</li>";
		}

		if (!$this->mainTheme) {
			$alert = "<div class=\"uk-alert uk-alert-danger uk-margin-large-top\">{$fn(Text::get('error_no_theme'))}</div>";
		}

		return <<< HTML
			$alert
			<div class="uk-accordion-title">
				{$fn(Text::get('shared_theme'))}
			</div>
			<div class="uk-accordion-content">
				<div id="am-apply-theme-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						{$fn(Text::get('shared_theme_apply'))}
						<div class="uk-modal-footer uk-text-right">
							<button 
							class="uk-modal-close uk-button"
							>
								<i class="uk-icon-close"></i>&nbsp;
								{$fn(Text::get('btn_close'))}
							</button>
							<button 
							class="uk-modal-close uk-button uk-button-success" 
							type="button" 
							data-am-submit="Shared::data"
							>
								<i class="uk-icon-check"></i>&nbsp;
								{$fn(Text::get('btn_apply_reload'))}
							</button>
						</div>
					</div>
				</div>
				<ul 
				class="uk-grid uk-grid-match uk-grid-width-1-2 uk-grid-width-medium-1-4 uk-margin-top" 
				data-uk-grid-match="{target:'.uk-panel'}" 
				data-uk-grid-margin
				>
					$themes
				</ul>
				<a 
				href="?view=packages" 
				class="uk-button uk-button-large uk-button-success uk-margin-top"
				>
					<i class="uk-icon-download"></i>&nbsp;
					{$fn(Text::get('btn_get_themes'))}
				</a>
			</div>
		HTML;
	}

	/**
	 * The title field.
	 *
	 * @return string the rendered title field
	 */
	private function title() {
		$fn = $this->fn;

		return <<<HTML
			<div class="uk-form-row">
				<label for="am-input-data-sitename" class="uk-form-label uk-margin-top-remove">
					{$fn(ucwords(AM_KEY_SITENAME))}
				</label>
				<input 
				id="am-input-data-sitename" 
				class="am-form-title uk-form-controls uk-form-large uk-width-1-1" 
				type="text" 
				name="data[{$fn(AM_KEY_SITENAME)}]" 
				value="{$fn(htmlspecialchars($this->data[AM_KEY_SITENAME]))}" 
				/>
			</div>
		HTML;
	}
}
