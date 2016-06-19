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
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	A Pagelist object represents a set of Page objects (matching certain criterias).
 *
 *	The main properties of a Pagelist object are: 
 *	- A selection of Page objects (filtered)
 *	- An array of tags (not filtered, but only from pages matching $type, $template & $search)
 *
 *	The criterias for the selection of Page objects are:
 *	- $type (false (all pages), "children", "related", "siblings" or "breadcrumbs")
 *	- $parent (is only used, when $type is "children" - default is the current page)
 *	- $template (if passed, only pages with that template get included)
 *	- the 'search' element from the query string (if existant, the selection gets filtered by these keywords)
 *
 *	Since the selection of pages will also be filtered by the keywords passed as the 'search' element in the query string, 
 *	this object can easily be used on a search results page.
 *	Basically a search results page can just be a normal page with a Pagelist object, where a search box passes the 'search' value to.
 *
 *	The visibility and order of the pages get influenced by the following elements within a query string:
 *	- filter
 *	- search
 *	- sortItem
 *	- sortOrder
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Pagelist {
	
	
	/**
	 *	The collection of all existing pages.
	 */
	
	private $collection;
	
	
	/**
	 *	The context.
	 */
	
	private $Context;
	
	
	/**
	 *	The default set of options.
	 */
	
	private $defaults = 	array(
					'type' => false,
					'parent' => false,
					'template' => false,
					'sortItem' => false,
					'sortOrder' => AM_LIST_DEFAULT_SORT_ORDER,
					'excludeHidden' => true,
					'offset' => 0,
					'limit' => NULL
				);
	
	
	/**
	 *	The pagelist's type (all pages, children pages or related pages)
	 */
	
	private $type;
	
	
	/**
	 *	In case $type is set to "children", the $parent URL can be used as well to change the parent from the current page to any page.
	 */
	
	private $parent;
	
	
	/**
	 *	The template to filter by the pagelist.
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
	 * 	Defines whether the pagelist excludes hidden pages or not. 
	 */
	
	private $excludeHidden;
	
	
	/**
	 * 	Defines the offset within the array of relevant pages. Note that this offest reduces the content of the pagelist and not its output. 
	 * 	To only reduce the output without reducing the pagelist itself, the $this->getPages() method provides also offset and limit parameters.  
	 */
	
	private $offset;
	
	
	/**
	 *	Defines the maximum number of pages in the pagelist. Also that limit reduces the pagelist content and not the returned array. 
	 *	To limit the number in the returned array only while keeping all relevant pages in the pagelist object, the $this->getPages() 
	 *	method provides its own set of offset and limit parameters.
	 */
	
	private $limit;
	
	
	/**
	 *	The current filter (from possible query string).
	 */
	
	private $filter = false;
	
	
	/**
	 *	The search string to filter pages (from possible query string).
	 */
	
	private $search = false;
		
	
	/**
	 *	Initialize the Pagelist.
	 *
	 *	@param array $collection
	 *	@param object $Context
	 */
	
	public function __construct($collection, $Context) {
		
		$this->collection = $collection;
		$this->Context = $Context;
		$this->config($this->defaults);
		
	}
	
	
	/**
	 *	Set or change the configuration of the pagelist and return the current configuration as array.    
	 *	To just get the config, call the method without passing $options.
	 *	
	 *	@param array $options
	 *	@return Updated $options
	 */
	
	public function config($options = array()) {
		
		// Turn all (but only) array items also existing in $defaults into class properties.
		// Only items existing in $options will be changed and will override the existings values defined with the first call ($defaults).
		foreach (array_intersect_key($options, $this->defaults) as $key => $value) {
			$this->$key = $value;
		}
		
		// Override settings with current query string options (filter, search and sort)
		$overrides = Parse::queryArray();
		
		foreach (array('filter', 'search', 'sortItem', 'sortOrder') as $key) {
			if (isset($overrides[$key])) {
				$this->$key = $overrides[$key];
			}
		}
		
		// Set sortOrder to the default order, if its value is invalid.
		if (!in_array($this->sortOrder, array('asc', 'desc'))) {
			$this->sortOrder = AM_LIST_DEFAULT_SORT_ORDER;
		}
			
		$configArray = array_intersect_key((array)get_object_vars($this), $this->defaults);
		
		// Only log debug info in case $options is not empty.
		if (!empty($options)) {
			Debug::log(array('Options' => $options, 'Current Config' => $configArray), json_encode($options, JSON_UNESCAPED_SLASHES));
		}
		
		return $configArray;
	
	}

	
	/**
	 *	Collect all pages matching $type (& optional $parent), $template & $search (optional). 
	 *	(Without filtering by tag and sorting!)
	 *	The returned pages have to be used to get all relevant tags.
	 *	It is important, that the pages are not filtered by tag here, because that would also eliminate the non-selected tags itself when filtering.   
	 *	
	 *	Also note that $this->offset & $this->limit reduces the set of all relevant pages and tags of the pagelist object while using the $offset or $limit parameters of
	 *	$this->getPages() only reduces the output and will not affect the relevant pages and the collected tags.    
	 *
	 *	@return An array of all Page objects matching $type & $template excludng the current page. 
	 */
	
	private function getRelevant() {
				
		$Selection = new Selection($this->collection);
		
		// In case $this->parent is an empty string or false, use the current context. 
		// Therefore it is not possible to have a pagelist only including the homepage (parent: "").
		// Since that kind of pagelist would always have only one element, that one can be accessed using the "with" statement instead.
		// Note that $parent has to be defined with each call again, to leave $this->parent untouched - otherwise it would be defined on a second call and therefore would create
		// an infinite loop on recursive pagelists.
		if ($this->parent) {
			$parent = $this->parent;
		} else {
			$parent = $this->Context->get()->url;
		}
		
		// Filter by type
		switch ($this->type) {
			case 'children':
				$Selection->filterByParentUrl($parent);
				break;
			case 'related':
				$Selection->filterRelated($this->Context->get());
				break;
			case 'siblings':
				$Selection->filterByParentUrl($this->Context->get()->parentUrl);
				break;
			case 'breadcrumbs':
				$Selection->filterBreadcrumbs($this->Context->get()->url);
				break;
		}
	
		// Filter by template
		$Selection->filterByTemplate($this->template);
		
		// Filter by keywords (for search results)
		$Selection->filterByKeywords($this->search);
		
		return $Selection->getSelection($this->excludeHidden, $this->offset, $this->limit);
			
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
	 *	Note that $offset & $limit only reduce the output and not the array of relevant pages! Using the getTags() method will still output all tags, 
	 *	even if pages with such tags are not returned due to the limit. Sorting a pagelist will also sort all pages and therefore the set of returned pages might
	 *	always be different.
	 *
	 *	@param integer $offset
	 *	@param integer $limit
	 *	@return The filtered and sorted array of Page objects
	 */
	
	public function getPages($offset = 0, $limit = NULL) {
			
		$Selection = new Selection($this->getRelevant());
		$Selection->filterByTag($this->filter);
		$Selection->sortPages($this->sortItem, constant(strtoupper('sort_' . $this->sortOrder)));
	
		$pages = $Selection->getSelection($this->excludeHidden, $offset, $limit);
		
		Debug::log(array_keys($pages));
		
		return $pages;
			
	}

	
}


?>