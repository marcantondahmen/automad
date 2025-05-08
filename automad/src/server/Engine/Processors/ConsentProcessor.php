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
use Automad\Engine\Document\Head;
use Automad\System\Asset;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The consent processor class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ConsentProcessor {
	/**
	 * The Automad instance.
	 */
	private Automad $Automad;

	/**
	 * The constructor.
	 *
	 * @param Automad $Automad
	 */
	public function __construct(Automad $Automad) {
		$this->Automad = $Automad;
	}

	/**
	 * Inject meta tags.
	 *
	 * @param string $str
	 * @return string
	 */
	public function addMetaTags(string $str): string {
		if (!AM_CONSENT_CHECK_ENABLED) {
			return $str;
		}

		$str = Head::prepend($str, Asset::js('dist/consent/main.bundle.js', false));
		$str = Head::prepend($str, Asset::css('dist/consent/main.bundle.css', false));

		$Page = $this->Automad->Context->get();

		$text = rawurlencode($Page->get(Fields::CUSTOM_CONSENT_TEXT));
		$accept = rawurlencode($Page->get(Fields::CUSTOM_CONSENT_ACCEPT));
		$decline = rawurlencode($Page->get(Fields::CUSTOM_CONSENT_DECLINE));

		$script = $text ? "window.amCookieConsentText = '$text';" : '';
		$script .= $accept ? "window.amCookieConsentAccept = '$accept';" : '';
		$script .= $decline ? "window.amCookieConsentDecline = '$decline';" : '';
		$script = $script ? "<script>$script</script>" : '';

		$str = Head::append($str, $script);

		$colorText = $Page->get(Fields::CUSTOM_CONSENT_COLOR_TEXT);
		$colorBackground = $Page->get(Fields::CUSTOM_CONSENT_COLOR_BACKGROUND);
		$colorBorder = $Page->get(Fields::CUSTOM_CONSENT_COLOR_BORDER);
		$colorPlaceholderText = $Page->get(Fields::CUSTOM_CONSENT_PLACEHOLDER_COLOR_TEXT);
		$colorPlaceholderBackground = $Page->get(Fields::CUSTOM_CONSENT_PLACEHOLDER_COLOR_BACKGROUND);

		$colors = $colorText ? "--am-consent-banner-color: $colorText;" : '';
		$colors .= $colorBackground ? "--am-consent-banner-background: $colorBackground;" : '';
		$colors .= $colorBorder ? "--am-consent-banner-border: $colorBorder;" : '';
		$colors .= $colorPlaceholderText ? "--am-consent-placeholder-color: $colorPlaceholderText;" : '';
		$colors .= $colorPlaceholderBackground ? "--am-consent-placeholder-background: $colorPlaceholderBackground;" : '';
		$colors = $colors ? "<style>:root { $colors }</style>" : '';

		$str = Head::append($str, $colors);

		return $str;
	}

	/**
	 * Encode scripts.
	 *
	 * @param string $str
	 */
	public function encodeScript(string $str): string {
		/** @var string */
		$str = preg_replace_callback(
			'/<am-consent\s((?:[^>]+\s)?type="script"[^>]*)>(.*?)<\/am-consent>/s',
			function ($matches) {
				return '<am-consent ' . $matches[1] . '>' . base64_encode(trim($matches[2])) . '</am-consent>';
			},
			$str
		);

		return $str;
	}
}
