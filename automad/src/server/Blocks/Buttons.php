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
 * Copyright (c) 2020-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Blocks;

use Automad\Blocks\Utils\Attr;
use Automad\Core\Automad;
use Automad\Models\ComponentCollection;
use Automad\Models\Search\Replacement;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The buttons block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
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
	 * Search and replace inside block data.
	 *
	 * @param BlockData $block
	 * @param ComponentCollection $ComponentCollection
	 * @param string $searchRegex
	 * @param string $replace
	 * @param bool $replaceInPublishedComponent
	 * @return BlockData
	 */
	public static function replace(
		array $block,
		ComponentCollection $ComponentCollection,
		string $searchRegex,
		string $replace,
		bool $replaceInPublishedComponent
	): array {
		$block['data'] = Replacement::replaceInBlockFields(
			$block['data'],
			array('primaryText', 'secondaryText'),
			$searchRegex,
			$replace
		);

		return $block;
	}

	/**
	 * Return a searchable string representation of a block.
	 *
	 * @param BlockData $block
	 * @param ComponentCollection $ComponentCollection
	 * @return string
	 */
	public static function toString(array $block, ComponentCollection $ComponentCollection): string {
		return trim(($block['data']['primaryText'] ?? '') . ' ' . ($block['data']['secondaryText'] ?? ''));
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
