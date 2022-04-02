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
 * Copyright (c) 2017-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI;

use Automad\Core\Context;
use Automad\Core\Str;
use Automad\Engine\PatternAssembly;
use Automad\UI\Components\Header\BlockSnippetArrays;
use Automad\UI\Components\Header\EditorTextModules;
use Automad\UI\Components\Modal\Link;
use Automad\UI\Components\Modal\SelectImage;
use Automad\UI\Utils\Prefix;
use Automad\UI\Utils\Session;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\URLHashes;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The InPage class provides all methods related to edit content directly in the page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2017-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class InPage {
	/**
	 * The constructor.
	 */
	public function __construct() {
		if (Session::getUsername()) {
			// Prepare text modules.
			Text::parseModules();
		}
	}

	/**
	 * Process the page markup and inject all needed UI markup if an user is logged in.
	 *
	 * @param string $str
	 * @return string The processed $str
	 */
	public function createUI(string $str) {
		if (Session::getUsername()) {
			$str = $this->injectAssets($str);
			$str = $this->injectMarkup($str);
			$str = $this->processTemporaryEditButtons($str);
		}

		return $str;
	}

	/**
	 * Inject a temporary markup for an edit button.
	 *
	 * @param string $value
	 * @param string $key
	 * @param Context $Context
	 * @return string The processed $value
	 */
	public function injectTemporaryEditButton(string $value, string $key, Context $Context) {
		// Only inject button if $key is no runtime var and a user is logged in.
		if (preg_match('/^(\+|\w)/', $key) && Session::getUsername()) {
			$value .= 	AM_DEL_INPAGE_BUTTON_OPEN .
						json_encode(array(
							'context' => $Context->get()->origUrl,
							'key' => $key
						), JSON_UNESCAPED_SLASHES) .
						AM_DEL_INPAGE_BUTTON_CLOSE;
		}

		return $value;
	}

	/**
	 * Add all needed assets for inpage-editing to the <head> element.
	 *
	 * @param string $str
	 * @return string The processed markup
	 */
	private function injectAssets(string $str) {
		$version = AM_VERSION;
		$baseUrl = AM_BASE_URL;
		$versionSanitized = Str::sanitize(AM_VERSION);
		$snippets = BlockSnippetArrays::render();
		$editorText = EditorTextModules::render();

		$assets = <<< HTML
			<!-- Automad UI -->
			<link href="{$baseUrl}/automad/dist/libs.min.css?v={$versionSanitized}" rel="stylesheet">
			<link href="{$baseUrl}/automad/dist/automad.min.css?v={$versionSanitized}" rel="stylesheet">
			<script>window.AM_VERSION = "{$version}"</script>
			<script type="text/javascript" src="{$baseUrl}/automad/dist/libs.min.js?v={$versionSanitized}"></script>
			<script type="text/javascript" src="{$baseUrl}/automad/dist/automad.min.js?v={$versionSanitized}"></script>
			<script type="text/javascript">$.noConflict(true);delete window.UIkit;delete window.UIkit2;</script>
			$snippets
			$editorText
			<!-- Automad UI end -->
		HTML;

		// Check if there is already any other script tag and try to prepend all assets as first items.
		if (preg_match('/\<(script|link).*\<\/head\>/is', $str)) {
			return preg_replace('/(\<(script|link).*\<\/head\>)/is', $assets . "\n$1", $str);
		} else {
			return str_replace('</head>', $assets . "\n</head>", $str);
		}
	}

	/**
	 * Inject UI markup like bottom menu and modal dialogs.
	 *
	 * @param string $str
	 * @return string The processed $str
	 */
	private function injectMarkup(string $str) {
		$urlBase = AM_BASE_URL;
		$urlGui = AM_BASE_INDEX . AM_PAGE_DASHBOARD;
		$urlData = $urlGui . '?' . http_build_query(array('view' => 'Page', 'url' => AM_REQUEST)) . '#' . URLHashes::get()->content->data;
		$urlFiles = $urlGui . '?' . http_build_query(array('view' => 'Page', 'url' => AM_REQUEST)) . '#' . URLHashes::get()->content->files;
		$urlSys = $urlGui . '?view=System';
		$attr = 'class="am-inpage-menu-button" data-uk-tooltip';
		$request = AM_REQUEST;
		$logoSvg = file_get_contents(AM_BASE_DIR . '/automad/ui/svg/logo.svg');
		$Text = Text::getObject();

		$modalSelectImage = SelectImage::render();
		$modalLink = Link::render();

		$queryString = '';

		if (!empty($_SERVER['QUERY_STRING'])) {
			$queryString = $_SERVER['QUERY_STRING'];
		}

		$html = <<< HTML
			<div class="am-inpage" data-am-base-url="$urlBase">
				<div class="am-inpage-menubar">
					<div class="uk-button-group">
						<a href="$urlGui" class="am-inpage-menu-button">$logoSvg</a>
						<a href="$urlData" title="$Text->btn_data" $attr><i class="uk-icon-file-text-o"></i></a>
						<a href="$urlFiles" title="$Text->btn_files" $attr><i class="uk-icon-folder-open-o"></i></a>
						<a href="$urlSys" title="$Text->sys_title" $attr><i class="uk-icon-sliders"></i></a>
						<a href="#" class="am-drag-handle am-inpage-menu-button">
							<i class="uk-icon-arrows"></i>
						</a>
					</div>
				</div>
				<div id="am-inpage-edit-modal" class="am-fullscreen-modal uk-modal">
					<div class="uk-modal-dialog uk-modal-dialog-blank">
						<div class="uk-container uk-container-center">
							<form 
							class="uk-form uk-form-stacked" 
							data-am-inpage-controller="${urlGui}?controller=InPage::edit"
							>
								<input type="hidden" name="url" value="$request" />
								<input type="hidden" name="query" value="$queryString" />
							</form>
						</div>
					</div>
				</div>
			</div>
			$modalSelectImage
			$modalLink
		HTML;

		return str_replace('</body>', Prefix::tags($html) . '</body>', $str);
	}

	/**
	 * Process the temporary buttons to edit variable in the page.
	 * All invalid buttons (within tags and in links) will be removed.
	 *
	 * @param string $str
	 * @return string The processed markup
	 */
	private function processTemporaryEditButtons(string $str) {
		// Remove invalid buttons.
		// Within HTML tags.
		// Like <div data-attr="...">
		$str = preg_replace_callback('/\<[^>]+\>/is', function ($matches) {
			return preg_replace('/' . PatternAssembly::inPageEditButton() . '/is', '', $matches[0]);
		}, $str);

		// In head, script, links, buttons etc.
		// Like <head>...</head>
		$str = preg_replace_callback('/\<(a|button|head|script|select|textarea)\b.+?\<\/\1\>/is', function ($matches) {
			return preg_replace('/' . PatternAssembly::inPageEditButton() . '/is', '', $matches[0]);
		}, $str);

		$open = preg_quote(AM_DEL_INPAGE_BUTTON_OPEN);
		$close = preg_quote(AM_DEL_INPAGE_BUTTON_CLOSE);

		$str = preg_replace_callback("/$open(.+?\"key\":\"([^\"]+)\".+?)$close/is", function ($matches) {
			$json = $matches[1];
			$name = ucwords(str_replace('+', '', preg_replace('/([A-Z])/', ' $1', $matches[2])));

			$html = <<< HTML
				<span class="am-inpage">
					<a 
					href="#am-inpage-edit-modal" 
					class="am-inpage-edit-button" 
					data-uk-modal="{modal:false}" 
					data-am-inpage-content='$json'
					>
						<i class="uk-icon-pencil"></i>&nbsp;
						$name 
					</a>
				</span>
			HTML;

			return Prefix::attributes($html);
		}, $str);

		return $str;
	}
}
