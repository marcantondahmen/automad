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
 * Copyright (c) 2017-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin;

use Automad\API\RequestHandler;
use Automad\Core\Automad;
use Automad\Core\Error;
use Automad\Core\Session;
use Automad\Core\Text;
use Automad\Engine\Document\Body;
use Automad\Engine\Document\Head;
use Automad\Models\Context;
use Automad\Models\Page;
use Automad\System\Asset;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The InPage class provides all methods related to edit content directly in the page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2017-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class InPage {
	/**
	 * This regex matches the "{{@open:$data@}}$value{{@close:$data@}}" string that temporary
	 * wraps values in encoded fields that are later converted into webcomponents.
	 *
	 * @see injectTemporaryEditButton()
	 */
	const TEMP_REGEX = '/\{\{@open:([\w=]+)@\}\}(.*?)\{\{@close:\1@\}\}/s';

	/**
	 * The Automad instance.
	 */
	private Automad $Automad;

	/**
	 * The incremental button id.
	 */
	private static int $buttonId = 0;

	/**
	 * The constructor.
	 *
	 * @param Automad $Automad
	 */
	public function __construct(Automad $Automad) {
		$this->Automad = $Automad;
	}

	/**
	 * Process the page markup and inject all needed UI markup if an user is logged in.
	 *
	 * @param string $str
	 * @return string The processed $str
	 */
	public function createUI(string $str): string {
		if (AM_MAINTENANCE_MODE_ENABLED) {
			return $str;
		}

		if (Session::getUsername()) {
			$this->validateTemplate($str);

			$str = $this->injectAssets($str);
			$str = $this->injectDock($str);
			$str = $this->processEditButtons($str);
		}

		return $str;
	}

	/**
	 * Inject a temporary markup for an edit button.
	 *
	 * @param string $value
	 * @param string $field
	 * @param Context $Context
	 * @return string The processed $value
	 */
	public function injectTemporaryEditButton(string $value, string $field, Context $Context): string {
		if (AM_MAINTENANCE_MODE_ENABLED) {
			return $value;
		}

		if ($Context->get()->get(Fields::TEMPLATE) === Page::TEMPLATE_NAME_404) {
			return $value;
		}

		// Only inject button if $key is no runtime var and a user is logged in.
		if (preg_match('/^(\+|\w)/', $field) && Session::getUsername()) {
			$data = base64_encode(
				strval(
					json_encode(array(
						'id' => self::$buttonId++,
						'context' => $Context->get()->origUrl,
						'field' => $field,
						'page' => AM_REQUEST,
						'dashboard' => AM_BASE_INDEX . AM_PAGE_DASHBOARD
					))
				)
			);

			return "{{@open:$data@}}$value{{@close:$data@}}";
		}

		return $value;
	}

	/**
	 * Add all needed assets for inpage-editing to the <head> element.
	 *
	 * @param string $str
	 * @return string The processed markup
	 */
	private function injectAssets(string $str): string {
		$fn = function (mixed $expression): string {
			return $expression;
		};

		$assets = Asset::css('dist/build/inpage/index.css') . Asset::js('dist/build/inpage/index.js');

		return Head::append($str, $assets);
	}

	/**
	 * Inject main InPage component that provides the bottom menu and modal dialog.
	 *
	 * @param string $str
	 * @return string The processed $str
	 */
	private function injectDock(string $str): string {
		$state = $this->Automad->getPage(AM_REQUEST)?->get(Fields::PUBLICATION_STATE) ?? '';
		$urlDashboard = AM_BASE_INDEX . AM_PAGE_DASHBOARD;
		$urlApi = AM_BASE_INDEX . RequestHandler::API_BASE;
		$urlPage = AM_REQUEST;
		$labelKeys = array(
			'fieldsSettings',
			'fieldsContent',
			'uploadedFiles',
			'publish'
		);

		$labels = urlencode(
			strval(
				json_encode(
					array_reduce($labelKeys, function ($result, $key): array {
						$result[$key] = Text::get($key);

						return $result;
					}, array())
				)
			)
		);

		$csrf = Session::getCsrfToken();

		$html = <<< HTML
			<am-inpage-dock
				csrf="$csrf"
				api="$urlApi"
				dashboard="$urlDashboard"
				url="$urlPage"
				state="$state"
				labels="$labels"
			></am-inpage-dock>
			HTML;

		return Body::append($str, $html);
	}

	/**
	 * Process the temporary buttons to edit variable in the page.
	 * All invalid buttons (within tags and in links) will be removed.
	 *
	 * @param string $str
	 * @return string The processed markup
	 */
	private function processEditButtons(string $str): string {
		// Remove invalid buttons.
		// Within HTML tags.
		// Like <div data-attr="...">
		$str = preg_replace_callback('/\<[^>]+\>/is', function ($matches): string {
			return preg_replace(InPage::TEMP_REGEX, '$2', $matches[0]) ?? '';
		}, $str) ?? '';

		// In head, script, links, buttons etc.
		// Like <head>...</head>
		$str = preg_replace_callback('/\<(a|button|head|script|select|textarea)\b.+?\<\/\1\>/is', function ($matches): string {
			return preg_replace(InPage::TEMP_REGEX, '$2', $matches[0]) ?? '';
		}, $str) ?? '';

		$str = preg_replace_callback(InPage::TEMP_REGEX, function ($matches) {
			$base64Data = $matches[1];
			$value = $this->processEditButtons($matches[2]);
			$data = json_decode(base64_decode($base64Data));
			$label = Text::get('edit');
			$placeholder = !$value ? 'placeholder="' . Text::get('inPagePlaceholder') . '"' : '';

			return <<< HTML
				<am-inpage-edit
					dashboard="{$data->dashboard}"
					context="{$data->context}"
					field="{$data->field}"
					page="{$data->page}"
					label="$label"
					$placeholder
				>$value</am-inpage-edit>
				HTML;
		}, $str) ?? '';

		return $str;
	}

	/**
	 * Check if the provided template qualifies for in-page editing.
	 *
	 * @param string $str
	 */
	private function validateTemplate(string $str): void {
		if (!preg_match('#<html[^>]*?>.*?<head[^>]*?>.*?</head>.*?<body[^>]*?>.*?</body>.*?</html>#mis', $str)) {
			Error::exit(
				'Invalid Template Structure',
				<<< HTML
					A template must have a valid HTML structure including an outer 
					<code>html</code> tag with one <code>head</code> and 
					one <code>body</code> tag as direct child elements.
				HTML
			);
		}
	}
}
