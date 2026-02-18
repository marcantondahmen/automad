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
 * The list block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type BlockData from AbstractBlock
 */
class NestedList extends AbstractBlock {
	/**
	 * The list type tag
	 */
	private static string $tag;

	/**
	 * Render a list block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		if ($block['data']['style'] == 'ordered') {
			self::$tag = 'ol';
		} else {
			self::$tag = 'ul';
		}

		$html = self::renderItems((array) $block['data']['items']);
		$attr = Attr::render($block['tunes']);

		return "<am-list $attr>$html</am-list>";
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
		$replaceInItem = function (array $item) use (&$replaceInItem, $searchRegex, $replace): array {
			if (isset($item['content'])) {
				$item['content'] = Replacement::replace($item['content'], $searchRegex, $replace);

				if (!empty($item['items'])) {
					foreach ($item['items'] as $index => $value) {
						$item['items'][$index] = $replaceInItem($value);
					}
				}
			}

			return $item;
		};

		$block['data']['items'] = array_map(fn (array $item) => $replaceInItem($item), $block['data']['items']);

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
		$itemToString = function (array $item) use (&$itemToString): string {
			$strings = array();

			if (isset($item['content']) && strlen($item['content'])) {
				$strings[] = $item['content'];

				if (!empty($item['items'])) {
					foreach ($item['items'] as $child) {
						$strings[] = $itemToString($child);
					}
				}
			}

			return join(' ', $strings);
		};

		$strings = array();

		foreach ($block['data']['items'] as $item) {
			$strings[] = $itemToString($item);
		}

		return join(' ', $strings);
	}

	/**
	 * Render list items.
	 *
	 * @param array $items
	 * @return string the rendered item
	 */
	private static function renderItems(array $items): string {
		if (empty($items)) {
			return '';
		}

		$tag = self::$tag;
		$html = "<$tag>";

		foreach ($items as $item) {
			$content = htmlspecialchars_decode($item['content']);
			$children = self::renderItems($item['items']);
			$html .= "<li><span>$content</span>$children</li>";
		}

		$html .= "</$tag>";

		return $html;
	}
}
