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
 * Copyright (c) 2025-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Blocks;

use Automad\Blocks\Utils\Attr;
use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Models\ComponentCollection;
use Automad\Models\Search\Replacement;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The collapsible editor block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type BlockData from AbstractBlock
 */
class CollapsibleSection extends AbstractBlock {
	/**
	 * Render a collapsible section block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$data = $block['data'];
		$attr = Attr::render($block['tunes'], array('am-collapsible'));

		$title = $data['title'] ?? '';
		$content = '';

		if (empty($data['collapsed'])) {
			$attr .= ' open';
		}

		if (!empty($data['group'])) {
			$attr .= ' name="' . $data['group'] . '"';
		}

		if ($data['content']) {
			$content = Blocks::render($data['content'], $Automad);
		}

		return <<< HTML
			<details $attr>
				<summary>$title</summary>
				<section>$content</section>
			</details>
		HTML;
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
		if (isset($block['data']['content']['blocks'])) {
			$block['data']['content']['blocks'] = Blocks::replace(
				$block['data']['content']['blocks'],
				$ComponentCollection,
				$searchRegex,
				$replace,
				$replaceInPublishedComponent
			);
		}

		$block['data'] = Replacement::replaceInBlockFields(
			$block['data'],
			array('title'),
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
		$title = '';
		$blocks = array();

		if (isset($block['data']['content']['blocks']) && is_array($block['data']['content']['blocks'])) {
			$blocks = $block['data']['content']['blocks'];
		}

		if (isset($block['data']['title']) && is_string($block['data']['title'])) {
			$title = $block['data']['title'];
		}

		return trim($title . ' ' . Blocks::toString($blocks, $ComponentCollection));
	}
}
