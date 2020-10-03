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


namespace Automad\GUI\Components\Fullscreen;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The fullscreen bar component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Bar {


	/**
	 *	Create the fullscreen bar markup.
	 *
	 *	@param string $title
	 *	@param string $label
	 *	@return string The breadcrumb naviagtion markup
	 */

	public static function render($title, $label) {
		
		return <<< HTML
				<button 
				type="button" 
				class="am-fullscreen-bar-button"
				data-am-fullscreen
				>
					<i class="uk-icon-expand"></i>
				</button>
				<div class="am-fullscreen-bar">
					<div class="uk-flex uk-flex-space-between uk-flex-middle uk-height-1-1">
						<div class="uk-flex-item-1 uk-text-truncate uk-margin-small-right">
							<span class="uk-hidden-small"><i class="uk-icon-file-text-o"></i>&nbsp;</span>
							$title
							<span class="uk-form-label">$label</span>
						</div>
						<button 
						type="button" 
						class="am-fullscreen-bar-button"
						data-am-fullscreen
						>
							<i class="uk-icon-compress"></i>
						</button>
					</div>
				</div>
HTML;
	
	}


}