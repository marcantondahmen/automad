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
	 *	Render a gallery block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {
		
		$masonryRowHeight = 20;

		if (!empty($data->globs) && !empty($data->width) && !empty($data->layout)) {

			if ($files = Parse::fileDeclaration($data->globs, $Automad->Context->get())) {

				$files = array_filter($files, function($file) {
					return Parse::fileIsImage($file);
				});

				$html = '<div class="am-gallery-' . strtolower($data->layout) . '" style="--am-gallery-item-width:' . $data->width . 'px">';
	
				foreach ($files as $file) {

					$Image = new Image($file, 2 * $data->width);
					$caption = Str::stripTags(Parse::caption($file));
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
								<a href="$file" class="am-gallery-img-small" data-caption="$caption" data-lightbox>
									<img src="$Image->file" />
								</a>
							</div>
HTML;

				}

				return $html . '</div>';

			}

		}
		
	}


}