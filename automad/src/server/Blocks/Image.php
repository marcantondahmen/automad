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
use Automad\Blocks\Utils\ImgLoaderSet;
use Automad\Core\Automad;
use Automad\Engine\Document\Minify;
use Automad\Models\ComponentCollection;
use Automad\Models\Search\Replacement;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The image block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type BlockData from AbstractBlock
 */
class Image extends AbstractBlock {
	/**
	 * Render an image block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$data = $block['data'];

		if (empty($data['url'])) {
			return '';
		}

		$src = $data['url'];
		$alt = $data['alt'] ?? '';
		$ImgLoaderSet = new ImgLoaderSet($src, $Automad);

		// Note that the "src" attribute must be included in order to be able
		// to find the original source using Str::findFirstImage.
		$img = <<<HTML
			<am-img-loader 
				src="$src" 
				alt="$alt"
				width="{$ImgLoaderSet->width}" 
				height="{$ImgLoaderSet->height}" 
				image="{$ImgLoaderSet->image}" 
				preload="{$ImgLoaderSet->preload}"
			></am-img-loader>
			HTML;
		$caption = '';

		if (!empty($data['caption'])) {
			$caption = "<figcaption>{$data['caption']}</figcaption>";
		}

		if (!empty($data['link'])) {
			$target = $data['openInNewTab'] ? ' target="_blank"' : '';
			$img = "<a href=\"{$data['link']}\"{$target}>$img</a>";
		}

		$styles = '';
		$classes = array();

		if (
			!empty($data['breakpoints'])
			&& is_array($data['breakpoints'])
		) {
			$uniqueName = "image-{$block['id']}";
			$classes[] = $uniqueName;
			$styles = <<<HTML
				.{$uniqueName} {
					container-type: inline-size;
					container-name: {$uniqueName};
				}

				HTML;

			$focalPoint = array_merge(
				array('x' => '50', 'y' => '50'),
				$data['focalPoint'] ?? array()
			);

			krsort($data['breakpoints'], SORT_NATURAL);

			foreach ($data['breakpoints'] as $maxWidth => $breakpoint) {
				// Note that "& *" is here used as selector for both,
				// the img as well as the am-img-loader tags.
				$styles .= <<<HTML
					@container $uniqueName (max-width: {$maxWidth}px) {
						.{$uniqueName} * {
							aspect-ratio: {$breakpoint['aspectRatio']};
							object-fit: cover;
							object-position: {$focalPoint['x']}% {$focalPoint['y']}%;	
							background-size: cover;
							background-repeat: no-repeat;	
							background-position: {$focalPoint['x']}% {$focalPoint['y']}%;	
						}	
					}

					HTML;
			}

			$styles = Minify::css($styles);
			$styles = "<style>$styles</style>";
		}

		$attr = Attr::render($block['tunes'], $classes);

		return <<< HTML
			<figure $attr>
				$img 
				$styles
				$caption
			</figure>
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
		$block['data'] = Replacement::replaceInBlockFields(
			$block['data'],
			array('url', 'alt', 'caption'),
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
		if (!isset($block['data'])) {
			return '';
		}

		return trim(($block['data']['url'] ?? '') . ' ' . ($block['data']['alt'] ?? '') . ' ' . ($block['data']['caption'] ?? ''));
	}
}
