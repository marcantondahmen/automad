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


namespace Automad\GUI\Components\Nav;
use Automad\GUI\Text as Text;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The switcher component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Switcher {

/**
	 *  Create a sticky switcher menu with an optional dropdown menu.
	 *
	 *	@param string $target
	 *	@param array $items Main menu items
	 *	@param array $dropdown Dropdown menu items
	 *	@param boolean $private
	 *	@return string The rendered menu HTML
	 */
	
	public static function render($target, $items = array(), $dropdown = array(), $private = false) {
		
		$html = '<div class="am-switcher am-sticky">' .
				'<div class="am-switcher-buttons uk-flex">' .
		 		'<div class="uk-flex uk-flex-nowrap uk-flex-item-1" data-uk-switcher="{connect:\'' . $target . '\',animation:\'uk-animation-fade\',swiping:false}">';
	
		foreach ($items as $item) {
			
			// Clean up text to be used as id (also remove possible count badges).
			$tab = \Automad\Core\Str::sanitize(preg_replace('/&\w+;/', '', strip_tags($item['text'])), true);
			
			$html .= '<button class="uk-button uk-button-large" data-am-tab="' . $tab . '">' . 
				 	 '<span class="uk-visible-small">' . $item['icon'] . '</span>' .
				 	 '<span class="uk-hidden-small">' . $item['text'] . '</span>' .
				  	 '</button>';
				 
		}
	
		$html .= '</div>';
	
		// Private badge.
		if ($private) {
			$html .= '<i class="am-switcher-icon uk-icon-lock" title="' . Text::get('page_private') . '" data-uk-tooltip></i>';
		}

		// Dropdown.
		if ($dropdown) {
			$html .= '<div data-uk-dropdown="{mode:\'click\',pos:\'bottom-right\'}">' . 
	        		 '<a href="#" class="uk-button uk-button-large">' .
					 '<span class="uk-visible-large">' . Text::get('btn_more') . '&nbsp;&nbsp;<i class="uk-icon-caret-down"></i></span>' .
					 '<i class="uk-hidden-large uk-icon-ellipsis-v uk-icon-justify"></i>' .
				 	 '</a>' .
	        		 '<div class="uk-dropdown uk-dropdown-small">' .
	            	 '<ul class="uk-nav uk-nav-dropdown">';
			
		    	foreach ($dropdown as $item) {
		    		$html .= '<li>' . $item . '</li>';
		    	}
			
		    	$html .= '</ul>' . 
				 		 '</div>' . 
				 	 	 '</div>';
		}
	
		$html .= '</div>' .
			 	 '</div>';
	
		return $html;
			
	}
	

}