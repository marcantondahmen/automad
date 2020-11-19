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


namespace Automad\GUI\Components\System;
use Automad\GUI\Text as Text;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The headless system setting component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Headless {


	/**
	 * 	Renders the headless component.
	 * 
	 *	@return string The rendered HTML
	 */

	public static function render() {

		$Text = Text::getObject();

		if (AM_HEADLESS_ENABLED) { 
			$enabled = 'checked'; 
		} else {
			$enabled = '';
		}

		return <<< HTML
				<p>$Text->sys_headless_info</p>
				<!-- Headless Mode Enable -->
				<form 
				class="uk-form uk-form-stacked" 
				data-am-handler="update_config" 
				data-am-auto-submit
				>
					<!-- Headless Mode Enable -->
					<input type="hidden" name="type" value="headless" />		
					<label 
					class="am-toggle-switch-large" 
					data-am-toggle="#am-headless-template"
					>
						$Text->sys_headless_enable
						<input 
						type="checkbox" 
						name="headless" 
						value="on"
						$enabled 
						/>
					</label>
				</form>
				<!-- Headless Template -->
				<div id="am-headless-template" class="am-toggle-container uk-margin-large-top">
					<p>
						$Text->sys_headless_edit_info
					</p>
					<a 
					href="#am-headless-modal" 
					class="uk-button uk-button-large uk-button-success"
					data-uk-modal
					>
						<i class="uk-icon-pencil"></i>&nbsp;
						$Text->btn_edit_headless_template
					</a>
				</div>
				<div id="am-headless-modal" class="uk-modal">
					<div class="am-modal-dialog-code uk-modal-dialog uk-modal-dialog-large">
						<div class="uk-margin-small-bottom uk-grid uk-flex uk-flex-middle" data-uk-grid-margin>
							<div class="uk-width-small-1-1 uk-flex-item-1">
								<span 
								class="uk-text-truncate uk-hidden-small" 
								data-am-status="headless_template">
								</span>
							</div>
							<div class="uk-flex">
								<a href="#" class="uk-button uk-modal-close">
									<i class="uk-icon-close"></i>&nbsp;
									$Text->btn_close
								</a>
								<a 
								href="#"
								class="uk-button"
								data-am-submit="reset_headless_template"
								>
									<i class="uk-icon-refresh"></i>&nbsp;
									$Text->btn_reset
								</a>
								<button 
								class="uk-button uk-button-success"
								data-am-submit="edit_headless_template"
								>
									<i class="uk-icon-check"></i>&nbsp;
									$Text->btn_save
								</button>
							</div>
						</div>
						<form 
						class="uk-form" 
						data-am-handler="edit_headless_template" 
						data-am-init-on="resetHeadlessTemplate"
						data-am-init
						></form>
					</div>
				</div>
				<form 
				data-am-handler="reset_headless_template"
				data-am-confirm="$Text->confirm_reset_headless"
				>
					<input type="hidden" name="reset" value="1" />
				</form>
HTML;

	}


}