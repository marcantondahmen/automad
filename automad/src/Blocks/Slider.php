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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks;

use Automad\Core\Automad;
use Automad\Core\FileUtils;
use Automad\Core\Image;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The slider block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Slider extends AbstractBlock {
	/**
	 * Render a slider block.
	 *
	 * @param object $data
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(object $data, Automad $Automad) {
		if (empty($data->globs) || empty($data->width) || empty($data->height)) {
			return '';
		}

		$files = FileUtils::fileDeclaration($data->globs, $Automad->Context->get());

		$files = array_filter($files, function ($file) {
			return FileUtils::fileIsImage($file);
		});

		if (empty($files)) {
			return '';
		}

		$defaults = array('dots' => true, 'autoplay' => true);
		$options = array_merge($defaults, (array) $data);
		$options = array_intersect_key($options, $defaults);

		$first = 'am-active';
		$html = '<div class="am-slider" data-am-block-slider=\'' . json_encode($options) . '\'>';

		foreach ($files as $file) {
			$Image = new Image($file, $data->width, $data->height, true);
			$caption = FileUtils::caption($file);

			$html .= <<< HTML
						<div class="am-slider-item $first">
							<img src="$Image->file">
							<div class="am-slider-caption">$caption</div>
						</div>
					HTML;

			$first = '';
		}

		$html .= '</div>';
		$class = self::classAttr();

		return "<am-slider $class>$html</am-slider>";
	}
}
