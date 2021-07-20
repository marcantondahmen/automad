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
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\UI\Controllers;

use Automad\Core\Request;
use Automad\UI\Components\Autocomplete\JumpBar;
use Automad\UI\Components\Autocomplete\Link;
use Automad\UI\Components\Form\Field;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The UI controller class.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class UI {


	/**
	 *	Return the autocomplete values for a link field.
	 *
	 *	@return array the autocomplete data as part of the $output array
	 */

	public static function autocompleteLink() {

		$Automad = UICache::get();
		
		return Link::render($Automad);

	}


	/**
	 *	Return the autocomplete values for a search field.
	 *
	 *	@return array the autocomplete data as part of the $output array
	 */

	public static function autocompleteJump() {

		$Automad = UICache::get();

		return JumpBar::render($Automad);

	}


	/**
	 *	Return the UI component for a variable field based on the name.
	 *
	 *	@return array the component HTML as part of the $output array
	 */

	public static function field() {

		$output = array();

		if ($name = Request::post('name')) {
			$Automad = UICache::get();
			$output['html'] = Field::render($Automad, $name, '', true);
		}

		return $output;

	}


	/**
	 *	Redirect to a given target URL.
	 *
	 *	@return array the output array including the redirect URL
	 */

	public static function jump() {

		$output = array();

		if ($target = Request::post('target')) {
			if (strpos($target, '?view=') !== false) {
				$output['redirect'] = $target;
			}
		}

		return $output;

	}


}