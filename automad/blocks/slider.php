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


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The slider block.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Slider {


	/**	
	 *	Render a slider block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {

		if (!empty($data->globs) && !empty($data->width) && !empty($data->height)) {

			if ($files = Parse::fileDeclaration($data->globs, $Automad->Context->get())) {

				$files = array_filter($files, function($file) {
					return Parse::fileIsImage($file);
				});

				$figureAttr = '';

				if (!empty($data->stretched)) {
					$figureAttr = 'class="am-stretched" style="width: 100%; max-width: 100%;"';
				}

				$defaults = array('dots' => true, 'autoplay' => true);
				$options = array_merge($defaults, (array) $data);
				$options = array_intersect_key($options, $defaults);

				$first = 'am-active';
				$html = '<figure ' . $figureAttr . '><div class="am-slider" data-am-block-slider=\'' . json_encode($options) . '\'>';
				
				foreach ($files as $file) {

					$Image = new Image($file, $data->width, $data->height, true);
					$caption = Parse::caption($file);

					$html .= <<< HTML
							<div class="am-slider-item $first">
								<img src="$Image->file">
								<div class="am-slider-caption">$caption</div>
							</div>
HTML;

					$first = '';

				}
				
				return $html . '</div></figure>';

			}

		}

	}


}