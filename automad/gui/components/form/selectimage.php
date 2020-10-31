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
	 *	@param array $files
	 *	@param string $title
	 *	@param boolean $basename
	 *	@return string The HTML of the panel
	 */

	public static function render($files, $title, $basename = false) {

		if ($files) {

			$count = count($files);

			$html = <<< HTML
					<p class="uk-margin-top"> 
						$title
						&nbsp;<span class="uk-badge">$count</span>
					</p>
					<div class="am-select-image-grid">
HTML;
			
			foreach ($files as $file) {

				if ($basename) {
					$value = basename($file);
				} else {
					$value = Core\Str::stripStart($file, AM_BASE_DIR);
				}

				$image = new Core\Image($file, 200, 200, true);
				$imageUrl = AM_BASE_URL . $image->file;

				$html .= <<< HTML
				 		 <label class="am-cover-1by1">
							<img src="$imageUrl" title="$value" data-uk-tooltip>
						 	<input type="hidden" name="imageUrl" value="$value">
						 </label>
HTML;

			}
			
			$html .= '</div>';

			return $html;

		}

	}


}