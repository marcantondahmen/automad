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
		
		$masonryRowHeight = 5;
		$defaults = array(
			'globs' => '*.jpg, *.png, *.gif',
			'width' => 250,
			'masonry' => true,
			'stretched' => true
		);

		$data = array_merge($defaults, (array) $data);
		$data = (object) $data;

		if ($files = Parse::fileDeclaration($data->globs, $Automad->Context->get())) {

			$files = array_filter($files, function($file) {
				return Parse::fileIsImage($file);
			});

			$figureAttr = '';

			if ($data->stretched) {
				$figureAttr = 'class="am-stretched" style="width: 100%; max-width: 100%;"';
			}

			$layout = 'grid';

			if ($data->masonry) {
				$layout = 'masonry';
			}

			$html = '<figure ' . $figureAttr . '><div class="am-gallery-' . $layout . '" style="--am-gallery-item-width:' . $data->width . 'px">';

			foreach ($files as $file) {

				$Image = new Image($file, 2 * $data->width);
				$caption = Str::stripTags(Parse::caption($file));
				$file = Str::stripStart($file, AM_BASE_DIR);

				if ($data->masonry) {

					$span = round($Image->height / ($masonryRowHeight * 2) );
					$attr = 'style="--am-gallery-masonry-rows: ' . $span . ';"';

				} else {

					$aspectRatio = $Image->height / $Image->width;
					$class = 'am-gallery-grid-square';

					if ($aspectRatio > 1.5) {
						$class = 'am-gallery-grid-portrait';
					}

					if ($aspectRatio < 0.75) {
						$class = 'am-gallery-grid-landscape';
					}

					$attr = 'class="' . $class . '"';

				}

				$html .= <<< HTML
						<div $attr>
							<a href="$file" class="am-gallery-img-small" data-caption="$caption" data-am-block-lightbox>
								<img src="$Image->file" />
							</a>
						</div>
HTML;

			}

			return $html . '</div></figure>';

		}

	}


}