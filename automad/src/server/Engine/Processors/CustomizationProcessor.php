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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine\Processors;

use Automad\Core\Automad;
use Automad\Engine\Document\Body;
use Automad\Engine\Document\Head;
use Automad\Engine\Document\Minify;
use Automad\Models\Page;
use Automad\Models\Shared;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The customization processor class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class CustomizationProcessor {
	/**
	 * The current page object.
	 */
	private Page $Page;

	/**
	 * The shared object.
	 */
	private Shared $Shared;

	/**
	 * The constructor.
	 *
	 * @param Automad $Automad
	 */
	public function __construct(Automad $Automad) {
		$this->Page = $Automad->Context->get();
		$this->Shared = $Automad->Shared;
	}

	/**
	 * Add custom JS and CSS customizations.
	 *
	 * @param string $str
	 * @return string The rendered output
	 */
	public function addCustomizations(string $str): string {
		$str = Head::append($str, $this->merge(Fields::CUSTOM_HTML_HEAD));
		$str = Body::append($str, $this->merge(Fields::CUSTOM_HTML_BODY_END));

		$str = Head::append($str, $this->merge(
			Fields::CUSTOM_JS_HEAD,
			null,
			'script'
		));

		$str = Body::append($str, $this->merge(
			Fields::CUSTOM_JS_BODY_END,
			null,
			'script'
		));

		$str = Head::append($str, $this->merge(
			Fields::CUSTOM_CSS,
			function (string $value) { return Minify::css($value); },
			'style'
		));

		return $str;
	}

	/**
	 * Merge shared and page content.
	 *
	 * @param string $field
	 * @param callable|null $minify
	 * @param string|null $tag
	 * @return string
	 */
	private function merge(string $field, ?callable $minify = null, ?string $tag = null): string {
		$minify ??= function (string $value): string { return trim($value); };

		$shared = $minify($this->Shared->get($field));
		$page = $minify($this->Page->data[$field] ?? '');
		$merged = trim($shared . PHP_EOL . $page);

		return (!empty($tag) && !empty($merged)) ? "<{$tag}>{$merged}</{$tag}>" : $merged;
	}
}
