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
use Automad\Core\FileUtils;
use Automad\Core\Resolve;
use Automad\Core\Str;
use Automad\Models\ComponentCollection;
use Automad\Models\Search\Replacement;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The slider block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type BlockData from AbstractBlock
 */
class ImageSlideshow extends AbstractBlock {
	/**
	 * Render a slider block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		if (empty($block['data']['files'])) {
			return '';
		}

		$data = $block['data'];

		$settings = array(
			'imageWidthPx' => $data['imageWidthPx'] ?? 1200,
			'imageHeightPx' => $data['imageHeightPx'] ?? 780,
			'gapPx' => $data['gapPx'] ?? 0,
			'slidesPerView' => $data['slidesPerView'] ?? 1,
			'loop' => $data['loop'] ?? true,
			'autoplay' => $data['autoplay'] ?? false,
			'effect' => $data['effect'] ?? 'slide',
			'delay' => $data['delay'] ?? 3000,
			'hideControls' => $data['hideControls'] ?? false,
			'breakpoints' => $data['breakpoints'] ?? array()
		);

		$imageSets = array();

		// The $first image is used for the Str::findFirstImage() method.
		$first = $block['data']['files'][0];

		foreach ($data['files'] ?? array() as $file) {
			$imageSets[] = array(
				'imageSet' => new ImgLoaderSet($file, $Automad, $settings['imageWidthPx'], $settings['imageHeightPx'], true),
				'caption' => Str::markdown(FileUtils::caption(Resolve::filePath($Automad->Context->get()->path, $file)))
			);
		}

		$json = rawurlencode(strval(json_encode(array('imageSets' => $imageSets, 'settings' => $settings), JSON_UNESCAPED_SLASHES)));
		$attr = Attr::render($block['tunes']);

		return "<am-image-slideshow first=\"$first\" $attr data=\"$json\"></am-image-slideshow>";
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
		if (empty($block['data']['files'])) {
			return $block;
		}

		$block['data']['files'] = array_map(fn (string $file) => Replacement::replace($file, $searchRegex, $replace), $block['data']['files']);

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
		return join(' ', $block['data']['files'] ?? array());
	}
}
