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


namespace Automad\GUI\Components\Autocomplete;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The autocomplete JSON data for search component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Search {


	/**
	 *	Return a JSON formatted string to be used as autocomplete infomation in a search field.
	 *	
	 *	The collected data consists of all page titles, URLs and all available tags.
	 *
	 *	@param object $Automad
	 *	@return string The JSON encoded autocomplete data
	 */
	
	public static function render($Automad) {
		
		$titles = array();
		$urls = array();
		$tags = array();
		$values = array();
		
		foreach ($Automad->getCollection() as $Page) {
			$titles[] = $Page->get(AM_KEY_TITLE);
			$urls[] = $Page->origUrl;
			$tags = array_merge($tags, $Page->tags);
		}
		
		$titles = array_unique($titles);
		$tags = array_unique($tags);
		
		// Sort arrays separately to keep titles, urls and tags grouped.
		sort($titles);
		sort($tags);
		sort($urls);
		
		foreach (array_merge($titles, $tags, $urls) as $value) {
			$values[]['value'] = $value;
		}
		
		return json_encode($values, JSON_UNESCAPED_SLASHES);
		
	}
	

}