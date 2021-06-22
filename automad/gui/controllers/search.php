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


namespace Automad\GUI\Controllers;

use Automad\Core\Request;
use Automad\Core\Selection;
use Automad\GUI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Search controller.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Search { 


	/**
	 *	Get search results.
	 * 
	 *	@return array the results array
	 */

	public static function results() {

		$Automad = UICache::get();
		$pages = array();

		if ($query = Request::query('query')) {

			$collection = $Automad->getCollection();

			if (array_key_exists($query, $collection)) {

				// If $query matches an actual URL of an existing page, just get that page to be the only match in the $pages array.
				// Since $pages has only one element, the request gets directly redirected to the edit page (see below).
				$pages = array($Automad->getPage($query));

			} else {

				$Selection = new Selection($collection);
				$Selection->filterByKeywords($query);
				$Selection->sortPages(AM_KEY_MTIME . ' desc');
				$pages = $Selection->getSelection(false);
			}

			// Redirect to edit mode for a single result or in case $query represents an actually existing URL.
			if (count($pages) == 1) {
				$Page = reset($pages);
				header('Location: ' . AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=Page&url=' . urlencode($Page->origUrl));
				die();
			}
		}

		return $pages;

	}


}