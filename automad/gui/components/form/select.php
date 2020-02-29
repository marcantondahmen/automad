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
 *	The select button component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Select {


	/**
	 *	Create a select button.
	 *
	 *  @param string $name
	 *  @param array $values
	 *  @param string $selected
	 *  @param string $prefix
	 *  @return string The HTML for the button
	 */
	
	public static function render($name, $values, $selected, $prefix = '') {
		
		// Set checked value, if $checked is not in $values, to prevent submitting an empty value.
		if (!in_array($selected, $values)) {
			$selected = reset($values);
		}
		
		$html = '<div class="uk-button uk-form-select" data-uk-form-select="{activeClass:\'\'}">' . 
				ltrim($prefix . ' ') . 
				'<span></span>&nbsp;&nbsp;' .
				'<i class="uk-icon-caret-down"></i>' . 
				'<select name="' . $name . '">';
		
		foreach ($values as $text => $value) {
			
			if ($value === $selected) {
				$attr = ' selected';
			} else {
				$attr = '';
			}
			
			$html .= '<option value="' . $value . '"' . $attr . '>' . $text . '</option>'; 	
			
		}
		
		$html .= '</select>' . 
			 	 '</div>';
		
		return $html;
		
	}
	

}