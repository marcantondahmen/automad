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


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The checkbox component to hide a page. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class CheckboxHidden {


	/**	
	 * 	Create a checkbox to hide a page.
	 * 
	 *	@param string $key
	 *	@param string $hidden
	 *	@return string The HTML for the hidden input field
	 */

	public static function render($key, $hidden = false) {

		$Text = \Automad\GUI\Text::getObject();
		$checked = '';

		if ($hidden) {
			$checked = 'checked';
		}

		return 	<<<HTML
				<div class="uk-form-row">
					<label class="uk-form-label uk-text-truncate">
						$Text->page_visibility
					</label>
					<label 
					class="am-toggle-switch uk-button" 
					data-am-toggle
					>
						$Text->btn_hide_page
						<input 
						id="am-checkbox-hidden" 
						type="checkbox" 
						name="$key"
						$checked 
						/>
					</label>
				</div>
HTML;

	}


}