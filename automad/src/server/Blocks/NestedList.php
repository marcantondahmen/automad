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
 * The list block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
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
