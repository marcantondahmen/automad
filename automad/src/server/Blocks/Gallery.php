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
use Automad\Blocks\Utils\Img;
use Automad\Blocks\Utils\ImgLoaderSet;
use Automad\Core\Automad;
use Automad\Core\FileUtils;
use Automad\Core\Resolve;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The gallery block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
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
			'columnWidthPx' => intval($block['data']['columnWidthPx'] ?? 250),
			'rowHeightPx' => intval($block['data']['rowHeightPx'] ?? 250),
			'gapPx' => intval($block['data']['gapPx'] ?? 5),
			'fillRectangle' => $block['data']['fillRectangle'] ?? false,
		);

		$imageSets = array();
		$first = $block['data']['files'][0];

		$width = $settings['layout'] != 'rows' ? $settings['columnWidthPx'] : 0;
		$height = $settings['layout'] != 'columns' ? $settings['rowHeightPx'] : 0;

		foreach ($block['data']['files'] ?? array() as $file) {
			$imageSets[] = array(
				'thumb' => new ImgLoaderSet($file, $Automad, $width * $pixelDensity, $height * $pixelDensity, false),
				'large' => new Img($file, $Automad, 3000, 3000, false),
				'caption' => strip_tags(FileUtils::caption(Resolve::filePath($Automad->Context->get()->path, $file)))
			);
		}

		$json = rawurlencode(strval(json_encode(array('imageSets' => $imageSets, 'settings' => $settings), JSON_UNESCAPED_SLASHES)));
		$attr = Attr::render($block['tunes']);

		return "<am-gallery first=\"$first\" $attr data=\"$json\"></am-gallery>";
	}
}
