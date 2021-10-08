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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks;

use Automad\Core\Automad;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The list block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Lists extends AbstractBlock {
	/**
	 * The list type tag
	 */
	private static $tag;

	/**
	 * Render a list block.
	 *
	 * @param object $data
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(object $data, Automad $Automad) {
		if ($data->style == 'ordered') {
			self::$tag = 'ol';
		} else {
			self::$tag = 'ul';
		}

		$html = self::renderItems((array) $data->items);
		$class = self::classAttr();

		return "<am-list $class>$html</am-list>";
	}

	/**
	 * Render list items.
	 *
	 * @param array $items
	 * @return string the rendered item
	 */
	private static function renderItems(array $items) {
		$tag = self::$tag;
		$html = "<$tag>";

		foreach ($items as $item) {
			$content = htmlspecialchars_decode($item->content);
			$children = self::renderItems((array) $item->items);
			$html .= "<li><span>$content</span>$children</li>";
		}

		$html .= "</$tag>";

		return $html;
	}
}
