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
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The gallery block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Gallery extends AbstractBlock {
	/**
	 * Render a gallery block.
	 *
	 * @param object $data
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(object $data, Automad $Automad) {
		$defaults = array(
			'globs' => '*.jpg, *.png, *.gif',
			'layout' => 'vertical',
			'width' => '250px',
			'height' => '10rem',
			'gap' => '5px',
			'cleanBottom' => false
		);

		$data = array_merge($defaults, (array) $data);
		$data = (object) $data;

		if ($files = FileUtils::fileDeclaration($data->globs, $Automad->Context->get())) {
			$files = array_filter($files, function ($file) {
				return FileUtils::fileIsImage($file);
			});

			if ($data->layout == 'vertical') {
				$html = self::masonry($files, $data);
			} else {
				$html = self::flexbox($files, $data);
			}

			if (!empty($data->gap)) {
				$style = " style='--am-gallery-gap: {$data->gap};'";
			}

			return '<am-gallery ' . self::classAttr() . $style . '>' . $html . '</am-gallery>';
		}

		return '';
	}

	/**
	 * Render the actual flexbox markup.
	 *
	 * @param array $files
	 * @param object $data
	 * @return string The rendered HTML
	 */
	private static function flexbox($files, $data) {
		// Normalize unit in case unit is missing.
		$data->height = preg_replace('/^([\.\d]*)$/sm', '$1px', trim($data->height));
		$pixelHeight = self::pixelValue($data->height);

		$html = "<div class='am-gallery-flex' style='--am-gallery-flex-item-height: {$data->height}'>";

		foreach ($files as $file) {
			$Image = new Image($file, false, 2 * $pixelHeight);
			$caption = Str::stripTags(FileUtils::caption($file));
			$file = Str::stripStart($file, AM_BASE_DIR);
			$width = round($Image->width / 2);

			$html .= <<< HTML
				<a 
				href="$file" 
				class="am-gallery-flex-item am-gallery-img-small" 
				style="width: {$width}px"
				data-caption="$caption" 
				data-am-block-lightbox
				>
					<img src="$Image->file" />
				</a>
			HTML;
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render the actual masonry markup.
	 *
	 * @param array $files
	 * @param object $data
	 * @return string The rendered HTML
	 */
	private static function masonry($files, $data) {
		// Use a factor of 0.85 to multiply with the row height of the grid to get a good
		// result since the aspect ratio is dependent on the actual row width and not the
		// minimum row width.
		$masonryRowHeight = 20 * 0.85;

		// Normalize unit in case unit is missing.
		$data->width = preg_replace('/^([\.\d]*)$/sm', '$1px', trim($data->width));
		$pixelWidth = self::pixelValue($data->width);

		// Adding styles for devices smaller than width.
		$maxWidth = $pixelWidth * 1.75;
		$style = "<style scoped>@media (max-width: ${maxWidth}px) { .am-gallery-masonry { grid-template-columns: 1fr; } }</style>";

		$cleanBottom = '';

		if ($data->cleanBottom) {
			$cleanBottom = ' am-gallery-masonry-clean-bottom';
		}

		$html = $style .
				'<div class="am-gallery-masonry' . $cleanBottom . '" style="--am-gallery-item-width:' . $data->width . '">';

		foreach ($files as $file) {
			$Image = new Image($file, 2 * $pixelWidth);
			$caption = Str::stripTags(FileUtils::caption($file));
			$file = Str::stripStart($file, AM_BASE_DIR);
			$span = round($Image->height / ($masonryRowHeight * 2));

			$html .= <<< HTML
				<div 
				class="am-gallery-masonry-item"
				style="--am-gallery-masonry-rows: $span;"
				>
					<a href="$file" class="am-gallery-img-small" data-caption="$caption" data-am-block-lightbox>
						<img src="$Image->file" />
					</a>
				</div>
			HTML;
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Convert dimension of any kind to pixels for resizing.
	 * Note that it is of course not possible to actually convert absolute units like px to
	 * relative units like % or vh, but conceptually a sensful approximation is required to resize
	 * images.
	 *
	 * @param string $valueString
	 * @return number The converted pixel value
	 */
	private static function pixelValue($valueString) {
		$pixel = floatval($valueString);

		if (strpos($valueString, 'em') !== false) {
			$pixel = 16 * floatval($valueString);
		}

		if (strpos($valueString, '%') !== false) {
			$pixel = 10 * floatval($valueString);
		}

		if (strpos($valueString, 'vh') !== false) {
			$pixel = 7 * floatval($valueString);
		}

		if (strpos($valueString, 'vw') !== false) {
			$pixel = 14 * floatval($valueString);
		}

		return $pixel;
	}
}
