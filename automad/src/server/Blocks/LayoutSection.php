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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Blocks;

use Automad\Blocks\Utils\Attr;
use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Models\ComponentCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The layout section block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type BlockData from AbstractBlock
 */
class LayoutSection extends AbstractBlock {
	/**
	 * Render a section editor block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$data = $block['data'];
		$html = '';

		if ($data['content']) {
			$html = Blocks::render($data['content'], $Automad);
		}

		$defaultStyles = array(
			'backgroundColor' => '',
			'backgroundBlendMode' => '',
			'borderWidth' => '',
			'borderRadius' => '',
			'borderStyle' => '',
			'paddingTop' => '',
			'paddingBottom' => ''
		);

		$classes = array();

		/** @var array<non-empty-literal-string, string> */
		$styles = array_intersect_key(
			array_filter(array_merge($defaultStyles, array_filter($data['style'] ?? array()))),
			$defaultStyles
		);

		if (!empty($data['gap'])) {
			$styles['--am-flex-gap'] = $data['gap'];
		}

		if (!empty($data['minBlockWidth'])) {
			$styles['--am-flex-min-block-width'] = $data['minBlockWidth'];
		}

		if (!empty($data['justify'])) {
			$classes[] = "am-justify-{$data['justify']}";
		}

		if (!empty($data['align'])) {
			$classes[] = "am-align-{$data['align']}";
		}

		if (!empty($data['style'])) {
			if (!empty($data['style']['backgroundImage'])) {
				$styles['backgroundImage'] = "url('{$data['style']['backgroundImage']}')";
			}

			if (!empty($data['style']['overflowHidden'])) {
				$styles['overflow'] = 'hidden';
			}

			if (!empty($data['style']['matchRowHeight'])) {
				$styles['height'] = '100%';
			}

			if (!empty($data['style']['shadow'])) {
				$styles['boxShadow'] = 'var(--am-layout-section-shadow)';
			}

			if (!empty($data['style']['color'])) {
				$styles['--am-layout-section-color'] = $data['style']['color'];
			}

			if (!empty($data['style']['borderColor'])) {
				$styles['--am-layout-section-border-color'] = $data['style']['borderColor'];
			}

			if (!empty($data['style']['card'])) {
				$classes[] = 'am-card';
			}
		}

		$attr = Attr::render($block['tunes']);
		$classes = Attr::renderClasses($classes);
		$styles = Attr::renderStyles($styles);

		return <<< HTML
			<section $attr>
				<am-layout-section $classes $styles>
					$html
				</am-layout-section>
			</section>
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
		if (!isset($block['data']['content']['blocks'])) {
			return $block;
		}

		$block['data']['content']['blocks'] = Blocks::replace(
			$block['data']['content']['blocks'],
			$ComponentCollection,
			$searchRegex,
			$replace,
			$replaceInPublishedComponent
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
		$content = $block['data']['content'] ?? array();
		$blocks = $content['blocks'] ?? array();

		return Blocks::toString($blocks, $ComponentCollection);
	}
}
