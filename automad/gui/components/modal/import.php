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
 *	The import file form URL modal component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Import {


	/**
	 *  Create the import modal dialog.
	 * 
	 *	@param string $url
	 *	@return string The HTML for the modal dialog
	 */

	public static function render($url = false) {

		$Text = Text::getObject();

		return <<< HTML

				<div 
				id="am-import-modal" 
				class="uk-modal" 
				data-am-url="$url"
				>
					<div class="uk-modal-dialog">
						<div class="uk-modal-header">
							$Text->btn_import
							<button type="button" class="uk-modal-close uk-close"></button>
						</div>
						<div class="am-form-input-button uk-form uk-flex">
							<input class="uk-form-controls uk-width-1-1" type="text" name="importUrl" placeholder="URL">
							<button type="button" class="uk-button uk-button-success uk-button-large">
								<i class="uk-icon-cloud-download"></i>
							</button>
						</div>
					</div>
				</div>

HTML;

	}
	

}