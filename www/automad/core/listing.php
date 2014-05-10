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
	
	private $S;
	
	
	/**
	 *	The current page's object.
	 */
	
	private $P;
	
	
	/**
	 *	The listing's type (all pages, children pages or related pages)
	 */
	
	public $type;
	
	
	/**
	 *	The template to filter by the listing.
	 */
	
	public $template;
	
		
	/**
	 *	The current filter (from possible query string).
	 */
	
	public $filter;
	
	
	/**
	 *	The current sortItem (from possible query string).
	 */
	
	public $sortItem;
	
	
	/**
	 *	The current sortOrder (from possible query string).
	 */
	
	public $sortOrder;
	
	
	/**
	 *	The search string to filter pages (from possible query string).
	 */
	
	public $search;
	
	
	/**
	 *	A set of all tags which occur at least on one of the listing's pages. 
	 *	That includes also the filtered out pages, to keep the filter menu complete.
	 *	So basically the tags are taken from all pages within the constructor variable $listing, 
	 *	before the pages get filtered according to the query filter.
	 */
	
	public $tags;
	
	
	/**
	 *	All pages matching the criteria ($type & $template) after (!) filtering and sorting (query settings).
	 *	So basically the final set of pages to be displayed.
	 */
	
	public $pages;
	
	
	/**
	 *	Initialize the Listing by setting up all properties.
	 */
	
	public function __construct($site, $type, $template, $defaultSortItem, $defaultSortOrder) {
		
		// Set up properties from passed parameters
		$this->S = $site;
		$this->P = $site->getCurrentPage();
		$this->type = $type;
		$this->template = $template;
		
		// Set up sorting by merging the default sorting options with the query string's options.
		// Note: Is is important, not to use Parse::queryKey here, because the 'sortItem' could be empty (false) to sort by basename.
		// Parse::queryKey() can not distinguish between false and not defined. 
		// So merging the array is much safer, since the existing (but false) options will be used instead of the defaults.
		$opt = array_merge(array('sortItem' => $defaultSortItem, 'sortOrder' => $defaultSortOrder), Parse::queryArray());
		$this->sortItem = $opt['sortItem'];
		$this->sortOrder = $opt['sortOrder'];
		
		// Set sortOrder to the default order, if its value is invalid.
		if (!in_array($this->sortOrder, array('asc', 'desc'))) {
			$this->sortOrder = AM_LIST_DEFAULT_SORT_ORDER;
		}
				
		// Set up filter
		$this->filter = Parse::queryKey('filter');
		
		// Set search
		$this->search = Parse::queryKey('search');
		
		// Set up tags and pages
		$listing = $this->setupListing();
		$this->tags = $this->setupTags($listing);
		$this->pages = $this->setupPages($listing);
		
		Debug::log('Listing: Created a Listing object with the following properties:');
		Debug::log($this);
		
	}
	
	
	/**
	 *	Collect all pages matching $type, $template & $search (optional). 
	 *	(Without filtering by tag and sorting!)
	 *	The returned pages have to be used to get all relevant tags.
	 *	It is important, that the pages are not filtered by tag here, because that would also eliminate the non-selected tags itself when filtering.
	 *
	 *	@return An array of all Page objects matching $type & $template excludng the current page. 
	 */
	
	private function setupListing() {
				
		$selection = new Selection($this->S->getCollection());
		
		// Always exclude current page
		$selection->excludePage($this->P->url);
		
		// Filter by type
		switch($this->type){
			case 'children':
				$selection->filterByParentUrl($this->P->url);
				break;
			case 'related':
				$selection->filterRelated($this->P);
				break;
		}
	
		// Filter by template
		$selection->filterByTemplate($this->template);
		
		// Filter by keywords (for search results)
		$selection->filterByKeywords($this->search);
		
		return $selection->getSelection();
			
	}
	
	
	/**
	 *	Collect all tags from tha pages in $listing in an array.
	 *
	 *	@param array $listing (Array of Page objects)
	 *	@return A sorted array with the relevant tags.
	 */
	
	private function setupTags($listing) {
				
		$tags = array();

		foreach ($listing as $page) {
			$tags = array_merge($tags, $page->tags);
		}
					
		$tags = array_unique($tags);
		sort($tags);
		
		return $tags;
			
	}
	
	
	/**
	 *	The final set of Page objects - filtered and sorted.
	 * 
	 *	@param array $listing (unfiltered array of Page objects)
	 *	@return The filtered and sorted array of Page objects
	 */
	
	private function setupPages($listing) {
		
		$selection = new Selection($listing);
		$selection->filterByTag($this->filter);
		$selection->sortPages($this->sortItem, constant(strtoupper('sort_' . $this->sortOrder)));
	
		return $selection->getSelection();
			
	}

	
}


?>