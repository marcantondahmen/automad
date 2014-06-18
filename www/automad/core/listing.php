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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	A Listing object represents a set of Page objects (matching certain criterias).
 *
 *	The main properties of a Listing object are: 
 *	- A selection of Page objects (filtered)
 *	- An array of tags (not filtered, but only from pages matching $type, $template & $search)
 *
 *	The criterias for the selection of Page objects are:
 *	- $type (false (all pages), "children" or "related")
 *	- $template (if passed, only pages with that template get included)
 *	- the 'search' element from the query string (if existant, the selection gets filtered by these keywords)
 *
 *	Since the selection of pages will also be filtered by the keywords passed as the 'search' element in the query string, 
 *	this object can easily be used on a search results page.
 *	Basically a search results page can just be a normal page with a Listing object, where a search box passes the 'search' value to.
 *
 *	The visibility and order of the pages get influenced by the following elements within a query string:
 *	- filter
 * 	- search
 *	- sortItem
 *	- sortOrder
 */


class Listing {
	

	/**
	 *	The passed Site object.
	 */
	
	private $Site;
	

	/**
	 *	The default set of options.
	 */
	
	private $defaults = 	array(
					'type' => false,
					'template' => false,
					'sortItem' => false,
					'sortOrder' => AM_LIST_DEFAULT_SORT_ORDER
				);
	
	
	/**
	 *	The listing's type (all pages, children pages or related pages)
	 */
	
	private $type;
	
	
	/**
	 *	The template to filter by the listing.
	 */
	
	private $template;
	
	
	/**
	 *	The current sortItem (from possible query string).
	 */
	
	private $sortItem;
	
	
	/**
	 *	The current sortOrder (from possible query string).
	 */
	
	private $sortOrder;
	
	
	/**
	 *	The current filter (from possible query string).
	 */
	
	private $filter = false;
	
	
	/**
	 *	The search string to filter pages (from possible query string).
	 */
	
	private $search = false;
		
	
	/**
	 *	Initialize the Listing.
	 */
	
	public function __construct($Site) {
		
		Debug::log('Listing: New instance created!');
		$this->Site = $Site;
		$this->config($this->defaults);
		
	}
	
	
	/**
	 *	Set or change the configuration of the listing.
	 *	
	 *	@param array $options
	 */
	
	public function config($options) {
		
		// Turn all (but only) array items in $options into class properties.
		// Only items existing in $options will be changed and will override the existings values defined with the first call ($defaults).
		foreach (array_intersect_key($options, $this->defaults) as $key => $value) {
			$this->$key = $value;
		}
		
		// Override settings with current query string options (filter, search and sort)
		$overrides = Parse::queryArray();
		
		Debug::log('Listing: Use overrides from query string:');
		Debug::log($overrides);
		
		foreach (array('filter', 'search', 'sortItem', 'sortOrder') as $key) {
			if (isset($overrides[$key])) {
				$this->$key = $overrides[$key];
			}
		}
		
		// Set sortOrder to the default order, if its value is invalid.
		if (!in_array($this->sortOrder, array('asc', 'desc'))) {
			$this->sortOrder = AM_LIST_DEFAULT_SORT_ORDER;
		}
		
		Debug::log('Listing: Current config:');
		Debug::log(get_object_vars($this));
	
	}

	
	/**
	 *	Collect all pages matching $type, $template & $search (optional). 
	 *	(Without filtering by tag and sorting!)
	 *	The returned pages have to be used to get all relevant tags.
	 *	It is important, that the pages are not filtered by tag here, because that would also eliminate the non-selected tags itself when filtering.
	 *
	 *	@return An array of all Page objects matching $type & $template excludng the current page. 
	 */
	
	private function getRelevant() {
		
		$Page = $this->Site->getCurrentPage();
				
		$Selection = new Selection($this->Site->getCollection());
		
		// Always exclude current page
		$Selection->excludePage($Page->url);
		
		// Filter by type
		switch($this->type){
			case 'children':
				$Selection->filterByParentUrl($Page->url);
				break;
			case 'related':
				$Selection->filterRelated($Page);
				break;
		}
	
		// Filter by template
		$Selection->filterByTemplate($this->template);
		
		// Filter by keywords (for search results)
		$Selection->filterByKeywords($this->search);
		
		return $Selection->getSelection();
			
	}
	
	
	/**
	 *	Return all tags from all pages in $relevant as array.
	 *
	 *	@return A sorted array with the relevant tags.
	 */
	
	public function getTags() {
				
		$tags = array();

		foreach ($this->getRelevant() as $Page) {
			$tags = array_merge($tags, $Page->tags);
		}
					
		$tags = array_unique($tags);
		sort($tags);
		
		return $tags;
			
	}
	
	
	/**
	 *	The final set of Page objects - filtered and sorted.
	 * 
	 *	@return The filtered and sorted array of Page objects
	 */
	
	public function getPages() {
			
		$Selection = new Selection($this->getRelevant());
		$Selection->filterByTag($this->filter);
		$Selection->sortPages($this->sortItem, constant(strtoupper('sort_' . $this->sortOrder)));
	
		return $Selection->getSelection();
			
	}

	
}


?>