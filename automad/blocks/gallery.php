<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Blocks;
use Automad\Core\Parse as Parse;
use Automad\Core\Image as Image;
use Automad\Core\Str as Str;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The gallery block.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Gallery {


	/**	
	 *	Create unique ids in case multiple galleries are used in one page.
	 */

	private static $idNumber = 0;


	/**	
	 *	Render a gallery block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {
		
		$masonryRowHeight = 20;
		self::$idNumber++;
		$idPrefix = 'am-gallery-' . self::$idNumber . '-image-';

		if (!empty($data->globs) && !empty($data->width) && !empty($data->layout)) {

			if ($files = Parse::fileDeclaration($data->globs, $Automad->Context->get())) {

				$files = array_filter($files, function($file) {
					return Parse::fileIsImage($file);
				});

				$count = count($files);
				$i = 1;

				$html = '<div class="am-gallery-' . strtolower($data->layout) . '" style="--am-gallery-item-width:' . $data->width . 'px">';

				foreach ($files as $file) {

					$next = $i + 1;
					$prev = $i - 1;

					if ($next > $count) {
						$next = 1;
					}

					if ($prev < 1) {
						$prev = $count;
					}

					$Image = new Image($file, 2 * $data->width);
					$caption = Parse::caption($file);

					$file = Str::stripStart($file, AM_BASE_DIR);

					if ($data->layout == 'Masonry') {

						$span = round($Image->height / ($masonryRowHeight * 2) );
						$class = "am-gallery-masonry-rows-$span";

					} else {

						$aspectRatio = $Image->height / $Image->width;
						$class = 'am-gallery-grid-square';

						if ($aspectRatio > 1.5) {
							$class = 'am-gallery-grid-portrait';
						}

						if ($aspectRatio < 0.75) {
							$class = 'am-gallery-grid-landscape';
						}

					}

					$html .= <<< HTML
							<div class="$class">
								<a id="$idPrefix$i" href="#$idPrefix$i">
									<img src="$Image->file" class="am-gallery-img-small" />
									<span class="am-gallery-fullscreen">
										<img src="$file" />
									</span>
								</a>
								<div class="am-gallery-caption">$caption</div>
								<a class="am-gallery-close" href="#-"></a>
								<a class="am-gallery-prev" href="#$idPrefix$prev"></a>
								<a class="am-gallery-next" href="#$idPrefix$next"></a>
							</div>
HTML;

					$i++;					

				}

				$html .= '</div>';

				return $html;

			}

		}
		
	}


}