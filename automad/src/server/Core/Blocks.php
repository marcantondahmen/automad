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

namespace Automad\Core;

use Automad\Blocks\Utils\Attr;
use Automad\Engine\Document\Head;
use Automad\System\Asset;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Blocks class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 *
 * @psalm-import-type BlockData from \Automad\Blocks\AbstractBlock
 */
class Blocks {
	const BASE_CLASS = 'am-block';

	/**
	 * A static state property that is true when rendering is in process.
	 */
	private static bool $isRendering = false;

	/**
	 * Inject block assets into the header of a page.
	 *
	 * @param string $str
	 * @return string the processed HTML
	 */
	public static function injectAssets(string $str): string {
		if (!preg_match('/\sclass="[^"]*' . Blocks::BASE_CLASS . '[^"]*"/', $str)) {
			return $str;
		}

		$assets = Asset::css('dist/blocks/main.bundle.css', false) .
				  Asset::js('dist/blocks/main.bundle.js', false);

		return Head::prepend($str, $assets);
	}

	/**
	 * Render blocks created by the EditorJS block editor.
	 *
	 * @param array{blocks: array<int, BlockData>} $data
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $data, Automad $Automad): string {
		if (!isset($data['blocks'])) {
			return '';
		}

		$isSection = self::$isRendering;

		if (!$isSection) {
			self::$isRendering = true;
			Attr::resetUniqueIds();
		}

		$flexOpen = false;
		$html = '';

		foreach ($data['blocks'] as $block) {
			$blockIsFlexItem = false;
			$stretched = false;
			$width = '';

			if (isset($block['tunes']['layout'])) {
				/** @var bool */
				$stretched = $block['tunes']['layout']['stretched'] ?? false;
				$width = $block['tunes']['layout']['width'] ?? '';

				$blockIsFlexItem = ($width != '' && !$stretched);
			}

			if (!$flexOpen && $blockIsFlexItem) {
				$html .= '<am-flex>';
				$flexOpen = true;
			}

			if ($flexOpen && !$blockIsFlexItem) {
				$html .= '</am-flex>';
				$flexOpen = false;
			}

			$blockHtml = self::generateBlockHtml($block, $Automad);

			// Stretch block.
			if ($stretched) {
				$blockHtml = "<am-stretched>$blockHtml</am-stretched>";
			} elseif ($width != '') {
				/** @var string */
				$w = str_replace('/', '-', $width);
				$blockHtml = "<am-$w>$blockHtml</am-$w>";
			}

			$html .= $blockHtml;
		}

		if ($flexOpen) {
			$html .= '</am-flex>';
		}

		if (!$isSection) {
			self::$isRendering = false;
		}

		return $html;
	}

	/**
	 * Generate HTML for a single block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string
	 */
	private static function generateBlockHtml(array $block, Automad $Automad): string {
		try {
			return call_user_func_array(
				'\\Automad\\Blocks\\' . ucfirst($block['type']) . '::render',
				array($block, $Automad)
			);
		} catch (\TypeError $e) {
			$block = self::unknownBlockHandler($block);

			try {
				return call_user_func_array(
					'\\Automad\\Blocks\\' . ucfirst($block['type']) . '::render',
					array($block, $Automad)
				);
			} catch (\Throwable $e) {
				return '';
			}
		} catch (\Throwable $e) {
			return '';
		}
	}

	/**
	 * Handle unknown blocks.
	 *
	 * @param BlockData $block
	 * @return BlockData
	 */
	private static function unknownBlockHandler(array $block): array {
		if ($block['type'] == 'section') {
			$block['type'] = 'layoutSection';
		}

		return $block;
	}
}
