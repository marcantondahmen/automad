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


namespace Automad\GUI\Components\Modal;
use Automad\GUI\Text as Text;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The copy resized modal component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class CopyResized {


	/**
	 *  Create the copy resized modal dialog.
	 * 
	 *	@param string $url
	 *	@return string The HTML for the modal dialog
	 */

	public static function render($url = false) {

		$Text = Text::getObject();
		
		return <<< HTML

				<div id="am-copy-resized-modal" class="uk-modal" data-am-url="$url">
					<div class="uk-modal-dialog">
						<div class="uk-modal-header">
							$Text->btn_copy_resized
							<a href="#" class="uk-modal-close uk-close"></a>
						</div>
						<div class="uk-form uk-form-stacked">
							<input 
							id="am-copy-resized-filename"
							class="uk-form-controls uk-form-large uk-width-1-1" 
							type="text" 
							value="" 
							disabled 
							readonly 
							data-am-watch-exclude
							/>
							<ul class="uk-grid uk-grid-width-1-2">
								<li>
									<label 
									for="am-copy-resized-width" 
									class="uk-form-label uk-margin-small-top"
									>
										$Text->image_width_px
									</label>
									<input 
									id="am-copy-resized-width" 
									class="uk-form-controls uk-width-1-1"
									type="number" 
									step="10"
									name="width" 
									value=""
									data-am-watch-exclude
									>
								</li>
								<li>
									<label 
									for="am-copy-resized-height" 
									class="uk-form-label uk-margin-small-top"
									>
										$Text->image_height_px
									</label>
									<input 
									id="am-copy-resized-height" 
									class="uk-form-controls uk-width-1-1"
									type="number" 
									step="10" 
									name="height" 
									value=""
									data-am-watch-exclude
									>
								</li>
							</ul>
							<div class="uk-form-row uk-margin-small-top">
								<label class="am-toggle-switch uk-button" data-am-toggle>
									$Text->image_crop
									<input 
									id="am-copy-resized-crop"
									type="checkbox" 
									name="crop" 
									value="" 
									data-am-watch-exclude
									>
								</label>
							</div>
						</div>
						<div class="uk-modal-footer uk-text-right">
							<button type="button" class="uk-modal-close uk-button">
								<i class="uk-icon-close"></i>&nbsp;
								$Text->btn_close
							</button>
							<button id="am-copy-resized-submit" type="button" class="uk-button uk-button-success">
								<i class="uk-icon-check"></i>&nbsp;
								$Text->btn_ok
							</button>
						</div>
					</div>
				</div>
	
HTML;

	}
	

}