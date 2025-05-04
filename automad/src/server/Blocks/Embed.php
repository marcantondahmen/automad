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
 * Copyright (c) 2020-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks;

use Automad\Blocks\Utils\Attr;
use Automad\Core\Automad;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The embed block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 *
 * @psalm-import-type BlockData from AbstractBlock
 */
class Embed extends AbstractBlock {
	/**
	 * Render a embed block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$data = $block['data'];
		$iframeAttr = <<< HTML
			scrolling="no"
			frameborder="no"
			allowtransparency="true"
			allowfullscreen="true"
		HTML;

		$script = 'script';
		$scriptType = '';
		$iframe = 'iframe';
		$iframeType = '';
		$consentText = '';
		$consentAccept = '';
		$consentDecline = '';

		if (AM_COOKIE_CONSENT_ENABLED) {
			$consentText = AM_COOKIE_CONSENT_TEXT ? ' text="' . rawurlencode(AM_COOKIE_CONSENT_TEXT) . '"' : '';
			$consentAccept = AM_COOKIE_CONSENT_ACCEPT ? ' accept="' . rawurlencode(AM_COOKIE_CONSENT_ACCEPT) . '"' : '';
			$consentDecline = AM_COOKIE_CONSENT_DECLINE ? ' decline="' . rawurlencode(AM_COOKIE_CONSENT_DECLINE) . '"' : '';
			$script = 'am-consent' . $consentText . $consentAccept . $consentDecline;
			$scriptType = 'type="script"';
			$iframe = 'am-consent' . $consentText . $consentAccept . $consentDecline;
			$iframeType = 'type="iframe"';
		}

		if ($data['service'] == 'twitter') {
			$html = <<< HTML
				<blockquote class="twitter-tweet tw-align-center">
					<a href="{$data['embed']}" class="am-consent-placeholder"></a>
				</blockquote>
				<$script $scriptType async src="https://platform.twitter.com/widgets.js" charset="utf-8"></$script>
			HTML;
		} elseif ($data['service'] == 'imgur') {
			/** @var string */
			$id = preg_replace('/^.*\/imgur.com\//', '', $data['embed']);

			$html = <<< HTML
				<blockquote class="imgur-embed-pub" data-id="$id">
					<a href="{$data['embed']}" class="am-consent-placeholder"></a>
				</blockquote>
				<$script $scriptType async src="https://s.imgur.com/min/embed.js" charset="utf-8"></$script>
			HTML;
		} elseif (!empty($data['width'])) {
			$paddingTop = $data['height'] / $data['width'] * 100;

			$html = <<< HTML
				<div style="position: relative; padding-top: $paddingTop%;">
					<$iframe
						$iframeType
						$iframeAttr
						src="{$data['embed']}"
						style="position: absolute; top: 0; width: 100%; height: 100%;"
					></$iframe>
				</div>
			HTML;
		} else {
			$html = <<< HTML
				<$iframe 
					$iframeType
					$iframeAttr
					src="{$data['embed']}"
					height="{$data['height']}"
					style="width: 100%;"
				></$iframe>
			HTML;
		}

		if (!empty($data['caption'])) {
			$html .= "<figcaption>{$data['caption']}</figcaption>";
		}

		$attr = Attr::render($block['tunes'], array('am-embed-' . $data['service']));

		return "<am-embed $attr><figure>$html</figure></am-embed>";
	}
}
