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

class EditFileInfo {


	/**
	 *  Create the file edit modal dialog.
	 * 
	 *	@param string $title
	 *	@param string $url
	 *	@return string The HTML for the modal dialog
	 */

	public static function render($title, $url = false) {

		$Text = Text::getObject();

		return <<< HTML
				<div 
				id="am-edit-file-info-modal" 
				class="am-fullscreen-modal uk-modal" 
				data-am-url="$url"
				>
					<div class="uk-modal-dialog uk-modal-dialog-blank">
						<div class="am-fullscreen-bar">
							<div class="uk-flex uk-flex-space-between uk-flex-middle uk-height-1-1">
								<div class="uk-flex-item-1 uk-text-truncate uk-margin-small-right">
									<i class="uk-icon-file-text-o"></i>&nbsp;
									$title
								</div>
								<div class="am-icon-buttons">
									<a 
									id="am-edit-file-info-download" 
									class="uk-button" 
									title="$Text->btn_download_file"
									download
									data-uk-tooltip="{pos:'bottom'}"
									>
										<i class="uk-icon-download"></i>
									</a>
									<button 
									id="am-edit-file-info-submit" 
									type="button" 
									class="uk-button uk-button-success"
									title="$Text->btn_save"
									data-uk-tooltip="{pos:'bottom'}"
									>
										<i class="uk-icon-check"></i>	
									</button>
								</div>
								<button type="button" class="am-fullscreen-bar-button uk-modal-close">
									<i class="uk-icon-compress"></i>
								</button>
							</div>
						</div>
						<div class="uk-text-center">
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
								</div>
							</div>
						</div>
					</div>
				</div>
HTML;

	}
	

}