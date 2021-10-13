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
use Automad\Core\Page;
use Automad\Core\Parse;
use Automad\Engine\Headless;
use Automad\System\ThemeCollection;
use Automad\UI\Components\Accordion\UnusedVariables;
use Automad\UI\Components\Accordion\Variables;
use Automad\UI\Components\Alert\ThemeReadme;
use Automad\UI\Components\Form\CheckboxHidden;
use Automad\UI\Components\Form\CheckboxPrivate;
use Automad\UI\Components\Form\Field;
use Automad\UI\Components\Form\SelectTemplate;
use Automad\UI\Models\PageModel;
use Automad\UI\Utils\Keys;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page data layout component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageData {
	/**
	 * The Automad object.
	 */
	private $Automad = null;

	/**
	 * All color keys.
	 */
	private $colorKeys = null;

	/**
	 * The page data array.
	 */
	private $data = null;

	/**
	 * A helper to use function within heredoc strings.
	 */
	private $fn = null;

	/**
	 * Page is hidden.
	 */
	private $hidden = false;

	/**
	 * The Page object.
	 */
	private $Page = null;

	/**
	 * Page is private.
	 */
	private $private = false;

	/**
	 * All settings variable keys.
	 */
	private $settingKeys = null;

	/**
	 * All text content keys.
	 */
	private $textKeys = null;

	/**
	 * The themelist object.
	 */
	private $ThemeCollection = null;

	/**
	 * All unused variable keys.
	 */
	private $unusedDataKeys = null;

	/**
	 * The page URL.
	 */
	private $url = null;

	/**
	 * The page data layout constructor.
	 *
	 * @param Automad $Automad
	 * @param Page $Page
	 */
	public function __construct(Automad $Automad, Page $Page) {
		$this->Automad = $Automad;
		$this->Page = $Page;
		$this->data = Parse::dataFile(PageModel::getPageFilePath($Page));
		$this->url = $Page->get(AM_KEY_URL);
		$this->ThemeCollection = new ThemeCollection();

		$this->fn = function ($expression) {
			return $expression;
		};

		// Set up all standard variables.
		// Create empty array items for all missing standard variables in $this->data.
		foreach (Keys::$reserved as $key) {
			if (!isset($this->data[$key])) {
				$this->data[$key] = false;
			}
		}

		// Check if page is private.
		if (!empty($this->data[AM_KEY_PRIVATE]) && $this->data[AM_KEY_PRIVATE] != 'false') {
			$this->private = true;
		} else {
			$this->private = false;
		}

		// Check if page is hidden.
		if (!empty($this->data[AM_KEY_HIDDEN]) && $this->data[AM_KEY_HIDDEN] != 'false') {
			$this->hidden = true;
		} else {
			$this->hidden = false;
		}

		// All other fields.
		if (!AM_HEADLESS_ENABLED) {
			$keys = Keys::inCurrentTemplate($Page, $this->ThemeCollection->getThemeByKey($Page->get(AM_KEY_THEME)));
		} else {
			$keys = Keys::inTemplate(Headless::getTemplate());
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
		$Theme = $this->ThemeCollection->getThemeByKey($this->Page->get(AM_KEY_THEME));

		return <<< HTML
			{$fn($this->title())}
			{$fn(CheckboxPrivate::render('data[' . AM_KEY_PRIVATE . ']', $this->private))}
			{$fn(ThemeReadme::render($Theme))}
			<div 
			class="uk-accordion" 
			data-uk-accordion="{duration: 200, showfirst: false, collapse: false}"
			>
				{$fn($this->settings())}
				{$fn(Variables::render($this->Automad, $this->textKeys, $this->data, $Theme, Text::get('page_vars_content')))}
				{$fn(Variables::render($this->Automad, $this->colorKeys, $this->data, $Theme, Text::get('page_vars_color')))}
				{$fn(Variables::render($this->Automad, $this->settingKeys, $this->data, $Theme, Text::get('page_vars_settings')))}
				{$fn(UnusedVariables::render($this->Automad, $this->unusedDataKeys, $this->data, Text::get('page_vars_unused')))}
			</div>
		HTML;
	}

	/**
	 * The inpage link.
	 *
	 * @return string the rendered inpage link HTML
	 */
	private function inpage() {
		if (AM_HEADLESS_ENABLED) {
			return '';
		}

		$fn = $this->fn;
		$name = $this->url;

		if ($this->url == '/') {
			$name = getenv('SERVER_NAME');
		}

		return <<< HTML
			<a 
			href="{$fn(AM_BASE_INDEX . $this->url)}" 
			class="uk-button uk-button-mini uk-margin-small-top uk-text-truncate uk-display-inline-block" 
			title="{$fn(Text::get('btn_inpage_edit'))}" 
			data-uk-tooltip="pos:'bottom'"
			>
				$name
			</a>
		HTML;
	}

	/**
	 * Create an input field.
	 *
	 * @param string $key
	 * @param string $value
	 * @param string $label
	 * @param string $class
	 * @param string $attributes
	 * @return string the rendered input field
	 */
	private function input(string $key, $value, string $label, string $class = '', string $attributes = '') {
		$id = "am-input-$key";

		return <<< HTML
			<div class="uk-form-row">
				<label for="$id" class="uk-form-label uk-text-truncate">
					$label
				</label>
				<input 
				id="$id" 
				class="uk-form-controls uk-width-1-1 $class" 
				type="text" 
				name="$key" 
				value="$value" 
				$attributes
				/>
			</div>
		HTML;
	}

	/**
	 * The prefix field.
	 *
	 * @return string the rendered prefix field.
	 */
	private function prefix() {
		if ($this->url == '/') {
			return '';
		}

		return $this->input('prefix', PageModel::extractPrefixFromPath($this->Page->path), Text::get('page_prefix'));
	}

	/**
	 * The redirect field.
	 *
	 * @return string the rendered redirect field.
	 */
	private function redirect() {
		if ($this->url == '/') {
			return '';
		}

		return Field::render(
			$this->Automad,
			AM_KEY_URL,
			$this->data[AM_KEY_URL],
			false,
			null,
			Text::get('page_redirect')
		);
	}

	/**
	 * The select template modal and button.
	 *
	 * @return string the rendered select template modal and button HTML
	 */
	private function selectTemplate() {
		if (AM_HEADLESS_ENABLED) {
			return '';
		}

		$themeName = '';
		$themePath = $this->Automad->Shared->get(AM_KEY_THEME);

		if ($this->data[AM_KEY_THEME]) {
			$themePath = $this->data[AM_KEY_THEME];

			if ($Theme = $this->ThemeCollection->getThemeByKey($this->data[AM_KEY_THEME])) {
				$themeName = $Theme->name . ' / ';
			}
		}

		$template = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $themePath . '/' . $this->Page->template . '.php';
		$templateName = $themeName . ucwords(str_replace('_', ' ', $this->Page->template));

		if (file_exists($template)) {
			$templateButtonClass = 'uk-button-success';
		} else {
			$templateButtonClass = 'uk-button-danger';
			$templateName .= ' - ' . Text::get('error_template_missing');
		}

		$fn = $this->fn;

		return <<< HTML
			<div id="am-select-template-modal" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						{$fn(Text::get('page_theme_template'))}
						<a href="#" class="uk-modal-close uk-close"></a>
					</div>
					{$fn(SelectTemplate::render($this->Automad, $this->ThemeCollection, 'theme_template', $this->data[AM_KEY_THEME], $this->Page->template))} 
					<div class="uk-modal-footer uk-text-right">
						<button class="uk-modal-close uk-button">
							<i class="uk-icon-close"></i>&nbsp;
							{$fn(Text::get('btn_close'))}
						</button>
						<button 
						class="uk-modal-close uk-button uk-button-success" 
						type="button" 
						data-am-submit="Page::data">
							<i class="uk-icon-check"></i>&nbsp;
							{$fn(Text::get('btn_apply_reload'))}
						</button>
					</div>
				</div>
			</div>
			<div class="uk-form-row">
				<label class="uk-form-label uk-text-truncate">
					{$fn(Text::get('page_theme_template'))}
				</label>
				<button 
				type="button" 
				class="uk-button {$templateButtonClass} uk-button-large uk-width-1-1" 
				data-uk-modal="{target:'#am-select-template-modal'}"
				>
					<div class="uk-flex uk-flex-space-between">
						<div class="uk-text-truncate uk-text-left">
							$templateName 
						</div>
						<div class="uk-hidden-small">
							<i class="uk-icon-pencil"></i>
						</div>
					</div>
				</button>
			</div>
		HTML;
	}

	/**
	 * The settings section.
	 *
	 * @return string the rendered settings section HTML
	 */
	private function settings() {
		$fn = $this->fn;

		return <<< HTML
			<div class="uk-accordion-title">
				{$fn(Text::get('page_settings'))}
			</div>
			<div class="uk-accordion-content">
				{$fn($this->selectTemplate())}
				{$fn(CheckboxHidden::render('data[' . AM_KEY_HIDDEN . ']', $this->hidden))}
				{$fn($this->prefix())}
				{$fn($this->slug())}
				{$fn($this->redirect())}
				{$fn(Field::render($this->Automad, AM_KEY_DATE, $this->Page->get(AM_KEY_DATE), false, null, Text::get('page_date')))}
				{$fn($this->tags())}
			</div>
		HTML;
	}

	/**
	 * The page directory slug.
	 *
	 * @return string the slug input field HTML
	 */
	private function slug() {
		if ($this->url == '/') {
			return '';
		}

		$slug = PageModel::extractSlugFromPath($this->Page->path);

		return $this->input(
			'slug',
			$slug,
			Text::get('page_slug') . ' (Slug)',
			'am-validate',
			'data-am-slug pattern="^(?=[a-z0-9])[a-z0-9\-]*[a-z0-9]$"'
		);
	}

	/**
	 * The tags field.
	 *
	 * @return string the rendered tags field
	 */
	private function tags() {
		$tags = Parse::csv(htmlspecialchars($this->data[AM_KEY_TAGS]));
		sort($tags);

		$Pagelist = $this->Automad->getPagelist();
		$Pagelist->config(
			array_merge(
				$Pagelist->getDefaults(),
				array('excludeHidden' => false)
			)
		);

		$allTags = $Pagelist->getTags();
		sort($allTags);

		$allTagsAutocomplete = array();

		foreach ($allTags as $tag) {
			$allTagsAutocomplete[]['value'] = $tag;
		}

		$fn = $this->fn;

		return <<< HTML
			<div class="uk-form-row">
				<label class="uk-form-label">
					{$fn(Text::get('page_tags'))}
				</label>
				<div 
				id="am-taggle" 
				data-am-tags='{
					"tags": {$fn(json_encode($tags))},
					"autocomplete": {$fn(json_encode($allTagsAutocomplete))}
				}'
				></div>
				<input  
				id="am-input-data-tags"
				type="hidden" 
				name="data[{$fn(AM_KEY_TAGS)}]" 
				value="" 
				/>
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
				<label for="am-input-data-title" class="uk-form-label uk-margin-top-remove">
					{$fn(ucwords(AM_KEY_TITLE))}
				</label>
				<input 
				id="am-input-data-title" 
				class="am-form-title uk-form-controls uk-form-large uk-width-1-1" 
				type="text" 
				name="data[{$fn(AM_KEY_TITLE)}]" 
				value="{$fn(htmlspecialchars($this->data[AM_KEY_TITLE]))}" 
				placeholder="Required" 
				required 
				/>
				{$fn($this->inpage())}
			</div>
		HTML;
	}
}
