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
		$settings = array(
			'layout' => $block['data']['layout'] ?? 'columns',
			'columnWidthPx' => $block['data']['columnWidthPx'] ?? 250,
			'rowHeightPx' => $block['data']['rowHeightPx'] ?? 250,
			'gapPx' => $block['data']['gapPx'] ?? 5,
			'cleanBottom' => $block['data']['cleanBottom'] ?? false
		);

		$imageSets = array();

		$width = $settings['layout'] === 'columns' ? $settings['columnWidthPx'] : 0;
		$height = $settings['layout'] === 'rows' ? $settings['rowHeightPx'] : 0;

		foreach ($block['data']['files'] ?? array() as $file) {
			$imageSets[] = array(
				'thumb' => new ImgLoaderSet($file, $Automad, $width, $height, false),
				'large' => new Img($file, $Automad, 3000, 3000, false),
				'caption' => strip_tags(FileUtils::caption(Resolve::filePath($Automad->Context->get()->path, $file)))
			);
		}

		$json = rawurlencode(strval(json_encode(array('imageSets' => $imageSets, 'settings' => $settings), JSON_UNESCAPED_SLASHES)));
		$attr = Attr::render($block['tunes']);

		return "<am-gallery $attr data=\"$json\"></am-gallery>";
	}
}
