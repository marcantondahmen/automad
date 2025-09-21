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
use Automad\Blocks\Utils\ImgLoaderSet;
use Automad\Core\Automad;
use Automad\Core\FileUtils;
use Automad\Core\Resolve;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The slider block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
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
			'gapPx' => $data['gapPx'] ?? 30,
			'slidesPerView' => $data['slidesPerView'] ?? 1,
			'loop' => $data['loop'] ?? true,
			'autoplay' => $data['autoplay'] ?? false,
			'effect' => $data['effect'] ?? 'slide',
			'breakpoints' => $data['breakpoints'] ?? array()
		);

		$imageSets = array();
		$first = $block['data']['files'][0];

		foreach ($data['files'] ?? array() as $file) {
			$imageSets[] = array(
				'imageSet' => new ImgLoaderSet($file, $Automad, $settings['imageWidthPx'], $settings['imageHeightPx'], true),
				'caption' => strip_tags(FileUtils::caption(Resolve::filePath($Automad->Context->get()->path, $file)))
			);
		}

		$json = rawurlencode(strval(json_encode(array('imageSets' => $imageSets, 'settings' => $settings), JSON_UNESCAPED_SLASHES)));
		$attr = Attr::render($block['tunes']);

		return "<am-image-slideshow first=\"$first\" $attr data=\"$json\"></am-image-slideshow>";
	}
}
