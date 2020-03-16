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
 *	The file edit modal component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class EditFile {


	/**
	 *  Create the file edit modal dialog.
	 * 
	 * 	@param string $url
	 * 	@return string The HTML for the modal dialog
	 */

	public static function render($url = false) {

		$Text = Text::getObject();

		return <<< HTML

				<div id="am-edit-file-info-modal" class="uk-modal" data-am-url="$url">
					<div class="uk-modal-dialog uk-modal-dialog-blank uk-text-center">
						<a href="#" class="uk-modal-close uk-close"></a>
						<div class="am-files-modal-container">
							<a href="#" class="am-files-modal-preview uk-modal-close">
								<img id="am-edit-file-info-img" src="" />
								<div id="am-edit-file-info-icon" data-am-extension=""></div>
							</a>
							<div class="am-files-modal-info">
								<div class="uk-form uk-form-stacked">
									<input id="am-edit-file-info-old-name" type="hidden" name="old-name" />	
									<div class="uk-form-row">
										<label for="am-edit-file-info-new-name" class="uk-form-label uk-margin-top-remove">
											$Text->file_name
										</label>
										<input 
										id="am-edit-file-info-new-name" 
										name="new-name" 
										class="uk-form-controls uk-form-large uk-width-1-1" 
										data-am-watch-exclude 
										/>
									</div>
									<div class="uk-form-row">
										<label for="am-edit-file-info-caption" class="uk-form-label">
											$Text->file_caption
										</label>
										<textarea 
										id="am-edit-file-info-caption" 
										name="caption" 
										class="uk-form-controls uk-width-1-1" 
										data-am-watch-exclude
										></textarea>
									</div>
								</div>
								<div class="uk-margin-top uk-text-right">
									<button type="button" class="uk-modal-close uk-button">
										<span class="uk-hidden-small"><i class="uk-icon-close"></i>&nbsp;</span>
										$Text->btn_close
									</button>
									<a id="am-edit-file-info-download" class="uk-button" download>
										<span class="uk-hidden-small"><i class="uk-icon-download"></i>&nbsp;</span>
										$Text->btn_download_file
									</a>
									<button id="am-edit-file-info-submit" type="button" class="uk-button uk-button-success">
										<span class="uk-hidden-small"><i class="uk-icon-check"></i>&nbsp;</span>
										$Text->btn_save
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
		
HTML;

	}
	

}