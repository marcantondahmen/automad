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


namespace Automad\GUI\Components\Form;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The select image input field component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class SelectImage {


	/**
	 *	Create an image selection panel.
	 *
	 * 	@param array $files
	 * 	@param string $title
	 * 	@param boolean $basename
	 *  @return string The HTML of the panel
	 */

	public static function render($files, $title, $basename = false) {

		if ($files) {

			$html = '<p class="uk-margin-top">' . 
						$title . '&nbsp;&nbsp;<span class="uk-badge">' . count($files) . '</span>
					</p>' .
					'<div class="uk-panel uk-panel-box uk-flex uk-flex-wrap uk-flex-wrap-top">';
			
			foreach ($files as $file) {

				if ($basename) {
					$value = basename($file);
				} else {
					$value = Core\Str::stripStart($file, AM_BASE_DIR);
				}

				$image = new Core\Image($file, 200, 200, true);

				// Make sure the resized image fits the aspect ratio of 1/1.
				if ($image->width != $image->height) {
					$min = min(array($image->width, $image->height));
					$image = new Core\Image($file, $min, $min, true);
				}

				$html .= '<label class="uk-width-1-3 uk-width-medium-1-5">' .
						 	'<img src="' . AM_BASE_URL . $image->file . '" title="' . $value . '" data-uk-tooltip>' .
						 	'<input type="hidden" name="imageUrl" value="' . $value . '">' .
						 '</label>';

			}
			
			$html .= '</div>';

			return $html;

		}

	}


}