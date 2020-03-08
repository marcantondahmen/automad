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


namespace Automad\GUI\Components\Grid;
use Automad\GUI\Components\Card as Card;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The page grid component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Pages {


	/**
	 *	Create a grid based page list for the given array of pages.
	 *
	 *	@param array $pages
	 *	@return string The HTML for the grid
	 */
	
	public static function render($pages) {
	
		$cards = '';
		
		foreach ($pages as $Page) {
			$cards .= '<li>' . Card\Page::render($Page) . '</li>';		
		}
			
		return <<< HTML
				<ul 
				class="uk-grid uk-grid-match uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4 uk-margin-top" 
				data-uk-grid-match="{target:'.uk-panel'}" 
				data-uk-grid-margin
				>
					$cards
				</ul>
HTML;
		
	}
	

}