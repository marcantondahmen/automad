<?php defined('AUTOMAD') or die('Direct access not permitted!');
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


/**
 *	An object of the Listing class represents a bundle of all information describing a filterable and sortable page listing.
 *
 *	That includes: 
 *	- A selection of $pages (filtered)
 *	- An array of $tags (not filtered, but only from pages matching $type)
 *	- The set of variables to be displayed
 *	- Image settings
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
	 *	The current filter (from possible query settings)
	 */
	
	public $filter;
	
	
	/**
	 *	The current sortType (from possible query settings)
	 */
	
	public $sortType;
	
	
	/**
	 *	The current sortDirection (from possible query settings)
	 */
	
	public $sortDirection;
	
	
	/**
	 *	The listing's type (all pages, children pages or related pages)
	 */
	
	private $type;
	
	
	/**
	 *	The template to filter by the listing.
	 */
	
	private $template;
	
	
	/**
	 *	The array of variables from the page's text file to display along with each page.
	 */
	
	public $vars;
	
	
	/**
	 *	The glob pattern to match an image within a page's folder to display together with that page.
	 */
	
	public $glob;
	
	
	/**
	 *	The width of a possibly displayed image.
	 */
	
	public $width;
	
	
	/**
	 *	The height of a possibly displayed image.
	 */
	
	public $height;
	
	
	/**
	 *	The cropping setting of a possibly displayed image.
	 */
	
	public $crop;
	
	
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
	
	public function __construct($site, $vars = array('title'), $type = 'all', $template = false, $glob = false, $width = false, $height = false, $crop = false) {
		
		// Set up properties from passed parameters
		$this->S = $site;
		$this->P = $site->getCurrentPage();
		$this->vars = $vars;
		$this->type = $type;
		$this->template = $template;
		$this->glob = $glob;
		$this->width = $width;
		$this->height = $height;
		$this->crop = $crop;
		
		// Set up filter and sort
		$this->filter = Parse::queryKey('filter');
		$this->sortType = Parse::queryKey('sort_type');
		
		if (Parse::queryKey('sort_dir')) {
			$this->sortDirection = constant(strtoupper(Parse::queryKey('sort_dir')));
		} else {
			$this->sortDirection = constant(strtoupper(AM_LIST_DEFAULT_SORT_DIR));
		}
		
		// Set up tags and pages
		$listing = $this->setupListing();
		$this->tags = $this->setupTags($listing);
		$this->pages = $this->setupPages($listing);
		
		Debug::log('Listing:');
		Debug::log($this);
		
	}
	
	
	/**
	 *	Collect all pages matching $type & $template. 
	 *	(Without filtering and sorting!)
	 *	The returned pages have to be used to get all relevant tags.
	 *	It is important, that the pages are not filtered here, because that would also eliminate the non-selected tags itself when filtering.
	 *
	 *	@return An array of all Page objects matching $type & $template excludng the current page. 
	 */
	
	private function setupListing() {
				
		$selection = new Selection($this->S->getCollection());
		
		// Always exclude current page
		$selection->excludePage($this->P->relUrl);
		
		// Filter by type
		switch($this->type){
			case 'children':
				$selection->filterByParentUrl($this->P->relUrl);
				break;
			case 'related':
				$selection->filterRelated($this->P);
				break;
		}
	
		// Filter by template
		$selection->filterByTemplate($this->template);
		
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
		$selection->sortPages($this->sortType, $this->sortDirection);
	
		return $selection->getSelection();
			
	}

	
}


?>