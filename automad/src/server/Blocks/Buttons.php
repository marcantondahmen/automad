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
 * The buttons block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 *
 * @psalm-import-type BlockData from AbstractBlock
 */
class Buttons extends AbstractBlock {
	/**
	 * Render a buttons block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$data = $block['data'];
		$settingsPrimary = array(
			'text' => $data['primaryText'] ?? '',
			'link' => $data['primaryLink'] ?? '',
			'style' =>  $data['primaryStyle'] ?? array(),
			'openInNewTab' => $data['primaryOpenInNewTab'] ?? true,
		);
		$settingsSecondary = array(
			'text' => $data['secondaryText'] ?? '',
			'link' => $data['secondaryLink'] ?? '',
			'style' =>  $data['secondaryStyle'] ?? array(),
			'openInNewTab' => $data['secondaryOpenInNewTab'] ?? true,
		);
		$settings = array(
			'justify' => $data['justify'] ?? 'start',
			'gap' => $data['gap'] ?? '1rem',
		);

		$primary = self::renderButton($settingsPrimary);
		$secondary = self::renderButton($settingsSecondary);

		if (empty($primary) && empty($secondary)) {
			return '';
		}

		$styles = array('--am-button-justify' => $settings['justify'], '--am-button-gap' => $settings['gap']);
		$attr = Attr::render($block['tunes'], array(), $styles);

		return "<am-buttons $attr>$primary$secondary</am-buttons>";
	}

	/**
	 * Render a button markup.
	 *
	 * @param array{text: string, link: string, style: array<string, string>, openInNewTab: bool} $settings
	 * @return string
	 */
	private static function renderButton(array $settings): string {
		if (empty($settings['text'])) {
			return '';
		}

		$style = '';
		$openInNewTab = $settings['openInNewTab'] ? 'target="_blank"' : '';

		foreach ($settings['style'] as $key => $value) {
			$style .= '--am-button-' . strtolower(preg_replace('/([A-Z])/', '-$1', $key) ?? '') . ": $value; ";
		}

		return <<< HTML
			<a href="{$settings['link']}" class="am-button" style="$style" $openInNewTab>
				{$settings['text']}
			</a>
		HTML;
	}
}
