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
 *	Copyright (c) 2019-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI\Components\Status;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The status button component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2019-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Button {

	
	/**
	 *  Create a status button for an AJAX status request with loading animation.
	 *      
	 *  @param string $status
	 *  @param string $tab
	 *  @return string The HTML for the status button
	 */

	public static function render($status, $tab) {
		
		return	'<a '.
		 		'href="?context=system_settings#' . $tab . '" ' .
				'class="uk-button uk-button-large uk-width-1-1 uk-text-left" ' .
				'data-am-status="' . $status . '"' .
		 		'>' .
					'<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-justify"></i>&nbsp;&nbsp;' . 
					\Automad\GUI\Text::get('btn_getting_data') .
				'</a>';
				
	}


}