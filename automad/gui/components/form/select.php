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
	 *	@param string $name
	 *	@param array $values
	 *	@param string $selected
	 *	@param string $prefix
	 *	@param string $class
	 *	@return string The HTML for the button
	 */
	
	public static function render($name, $values, $selected, $prefix = '', $class = '') {
		
		// Set checked value, if $checked is not in $values, to prevent submitting an empty value.
		if (!in_array($selected, $values)) {
			$selected = reset($values);
		}
		
		$prefix = ltrim($prefix . ' ');

		$html = <<< HTML
				<div class="uk-button $class uk-form-select" data-uk-form-select> 
					$prefix
					<span></span>&nbsp;
					<i class="uk-icon-caret-down"></i> 
					<select name="$name">
HTML;
		
		foreach ($values as $text => $value) {
			
			if ($value === $selected) {
				$attr = ' selected';
			} else {
				$attr = '';
			}
			
			$html .= '<option value="' . $value . '"' . $attr . '>' . $text . '</option>'; 	
			
		}
		
		$html .= <<< HTML
					</select> 
				</div>
HTML;
		
		return $html;
		
	}
	

}