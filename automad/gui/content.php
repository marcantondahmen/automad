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
 *	Copyright (c) 2016-2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Image;
use Automad\Core\Parse;
use Automad\Core\Request;
use Automad\Core\Selection;
use Automad\Core\Str;
use Automad\GUI\Utils\FileSystem;
use Automad\GUI\Utils\Text;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Content class provides all methods to add, modify, move or delete content (pages, shared data and files). 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Content {

	
	/**
	 *	The Automad object.
	 */

	private $Automad;


	/**
	 *	Set $this->Automad when creating an instance.
	 *
	 *	@param object $Automad
	 */
	
	public function __construct($Automad) {
		
		$this->Automad = $Automad;
		
	}
	

	/**
	 *	Get results for a search query from $_GET. In case there is only one match, redirect to the edit page for that URL. 
	 *
	 *	@return array The matching pages 
	 */

	public function getSearchResults() {
		
		$pages = array();
	
		if ($query = Request::query('query')) {
		
			$collection = $this->Automad->getCollection();
		
			if (array_key_exists($query, $collection)) {
			
				// If $query matches an actual URL of an existing page, just get that page to be the only match in the $pages array.
				// Since $pages has only one element, the request gets directly redirected to the edit page (see below).
				$pages = array($this->Automad->getPage($query));
							
			} else {
			
				$Selection = new Selection($collection);
				$Selection->filterByKeywords($query);
				$Selection->sortPages(AM_KEY_MTIME . ' desc');
				$pages = $Selection->getSelection(false);
			
			}
	
			// Redirect to edit mode for a single result or in case $query represents an actually existing URL.
			if (count($pages) == 1) {
				$Page = reset($pages);
				header('Location: ' . AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?context=edit_page&url=' . urlencode($Page->origUrl));
				die;	
			}
		
		}
		
		return $pages;
		
	}
	
	
}
