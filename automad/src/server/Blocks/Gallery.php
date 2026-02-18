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
use Automad\Blocks\Utils\Img;
use Automad\Blocks\Utils\ImgLoaderSet;
use Automad\Core\Automad;
use Automad\Core\FileUtils;
use Automad\Core\Resolve;
use Automad\Core\Str;
use Automad\Models\ComponentCollection;
use Automad\Models\Search\Replacement;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The gallery block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type BlockData from AbstractBlock
 */
class Gallery extends AbstractBlock {
	/**
	 * Render a gallery block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		if (empty($block['data']['files'])) {
			return '';
		}

		$pixelDensity = 2.5;
		$settings = array(
			'layout' => $block['data']['layout'] ?? 'columns',
			'columnWidthPx' => floatval($block['data']['columnWidthPx'] ?? 250),
			'rowHeightPx' => floatval($block['data']['rowHeightPx'] ?? 250),
			'gapPx' => intval($block['data']['gapPx'] ?? 5),
			'fillRectangle' => $block['data']['fillRectangle'] ?? false,
		);

		$imageSets = array();

		// The $first image is used for the Str::findFirstImage() method.
		$first = $block['data']['files'][0];

		$width = $settings['layout'] != 'rows' ? $settings['columnWidthPx'] : 0.0;
		$height = $settings['layout'] != 'columns' ? $settings['rowHeightPx'] : 0.0;

		foreach ($block['data']['files'] ?? array() as $file) {
			$imageSets[] = array(
				'thumb' => new ImgLoaderSet($file, $Automad, $width * $pixelDensity, $height * $pixelDensity, false),
				'large' => new Img($file, $Automad, 3000, 3000, false),
				'caption' => trim(Str::markdown(FileUtils::caption(Resolve::filePath($Automad->Context->get()->path, $file))))
			);
		}

		$json = rawurlencode(strval(json_encode(array('imageSets' => $imageSets, 'settings' => $settings), JSON_UNESCAPED_SLASHES)));
		$attr = Attr::render($block['tunes']);

		return "<am-gallery first=\"$first\" $attr data=\"$json\"></am-gallery>";
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
