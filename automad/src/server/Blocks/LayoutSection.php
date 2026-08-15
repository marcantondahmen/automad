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
use Automad\Engine\Document\Minify;
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
		$wrapperClasses = array();

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
			$style = $data['style'];

			if (!empty($style['backgroundImage'])) {
				$styles['backgroundImage'] = "url('{$style['backgroundImage']}')";
			}

			if (!empty($style['backgroundImageFocalPoint'])) {
				$x = $style['backgroundImageFocalPoint']['x'] ?? '50';
				$y = $style['backgroundImageFocalPoint']['y'] ?? '50';

				$styles['backgroundPosition'] = "{$x}% {$y}%";
			}

			if (!empty($style['aspectRatio'])) {
				$styles['aspectRatio'] = $style['aspectRatio'];
			}

			if (!empty($style['aspectRatioBreakpoints'])) {
				$breakpoints = $style['aspectRatioBreakpoints'];

				$uniqueName = "section-{$block['id']}";
				$wrapperClasses[] = $uniqueName;

				$css = <<<HTML
					.{$uniqueName} {
						container-type: inline-size;
						container-name: {$uniqueName};
					}

					HTML;

				krsort($breakpoints, SORT_NATURAL);

				foreach ($breakpoints as $maxWidth => $breakpoint) {
					$css .= <<<HTML
						@container $uniqueName (max-width: {$maxWidth}px) {
							.{$uniqueName} am-layout-section {
								aspect-ratio: {$breakpoint['aspectRatio']} !important;
							}	
						}

						HTML;
				}

				$css = Minify::css($css);
				$html .= "<style>$css</style>";
			}

			if (!empty($style['overflowHidden'])) {
				$styles['overflow'] = 'hidden';
			}

			if (!empty($style['matchRowHeight'])) {
				$styles['height'] = '100%';
			}

			if (!empty($style['shadow'])) {
				$styles['boxShadow'] = 'var(--am-layout-section-shadow)';
			}

			if (!empty($style['color'])) {
				$styles['--am-layout-section-color'] = $style['color'];
			}

			if (!empty($style['borderColor'])) {
				$styles['--am-layout-section-border-color'] = $style['borderColor'];
			}

			if (!empty($style['card'])) {
				$classes[] = 'am-card';
			}
		}

		$attr = Attr::render($block['tunes'], $wrapperClasses);
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
