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

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The image block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
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
		$attr = Attr::render($block['tunes']);
		$data = $block['data'];

		if (empty($data['url'])) {
			return '';
		}

		$src = $data['url'];
		$ImgLoaderSet = new ImgLoaderSet($src, $Automad);

		// Note that the "src" attribute must be included in order to be able
		// to find the original source using Str::findFirstImage.
		$img = "<am-img-loader src=\"$src\" width=\"{$ImgLoaderSet->width}\" height=\"{$ImgLoaderSet->height}\" image=\"{$ImgLoaderSet->image}\" preload=\"{$ImgLoaderSet->preload}\"></am-img-loader>";
		$caption = '';

		if (!empty($data['caption'])) {
			$caption = "<figcaption>{$data['caption']}</figcaption>";
		}

		if (!empty($data['link'])) {
			$target = $data['openInNewTab'] ? ' target="_blank"' : '';
			$img = "<a href=\"{$data['link']}\"{$target}>$img</a>";
		}

		return <<< HTML
			<figure $attr>
				$img
				$caption
			</figure>
		HTML;
	}
}
