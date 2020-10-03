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
 *	Copyright (c) 2013-2020 by Marc Anton Dahmen
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
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2013-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
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
	
	private $defaults = array(
							'context' => false,
							'excludeCurrent' => false,
							'excludeHidden' => true,
							'filter' => false,
							'limit' => NULL,
							'match' => false,
							'offset' => 0,
							'page' => false,
							'search' => false,
							'sort' => false,
							'template' => false,
							'type' => false
						);
	
	
	/**
	 *	In case $type is set to "children", the $context URL can be used as well to change the context from the current page to any page.
	 */
	
	private $context;

	
	/**
	 * 	Defines whether the pagelist excludes the current page or not. 
	 */
	
	private $excludeCurrent;
	
	
	/**
	 * 	Defines whether the pagelist excludes hidden pages or not. 
	 */
	
	private $excludeHidden;
	
	
	/**
	 *	The current filter.
	 */
	
	private $filter;
	
	
	/**
	 *	Defines the maximum number of pages in the array returned by getPages().
	 */
	
	private $limit;
	
	
	/**
	 * 	Defines a JSON string to be used as paramter for the $Selection->match() method.
	 */

	private $match;
	
	
	/**
	 * 	Defines the offset within the array of pages returned by getPages(). 
	 */
	
	private $offset;
	
	
	/**
	 *	The current page of the pagination.
	 */
	
	private $page;
		
	
	/**
	 *	The search string to filter pages.
	 */
	
	private $search;
	
	
	/**
	 *	The sort options string.
	 */
	
	private $sort;
	
	
	/**
	 *	The template to filter by the pagelist.
	 */
	
	private $template;
	
	
	/**
	 *	The pagelist's type (all pages, children pages or related pages)
	 */
	
	private $type;
	
	
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
	 *	Options:   
	 *
	 *	- context: an optionally fixed URL for the context of a pagelist of type breadcrumbs or children. In case this parameter is false, within a loop the context always changes dynamically to the current page.
	 *	- excludeCurrent: default false
	 *	- excludeHidden: default true
	 *	- filter: filter pages by tags
	 *	- limit: limit the object's array of relevant pages
	 *	- match: filter pages by matching one or more key/regex combinations passed as JSON string
	 *	- offset: offset the within the array of all relevant pages
	 *	- page: false (the current page in the pagination - to be used with the limit parameter)
	 *	- search: filter pages by search string
	 *	- sort: sorting options string, like "date desc, title asc"
	 *	- template: include only pages matching that template	
	 *	- type: sets the type of pagelist (default is false) - valid types are false (all), "children", "related", "siblings" and "breadcrumbs"      
	 *      
	 *	@param array $options
	 *	@return array Updated $options
	 */
	
	public function config($options = array()) {
		
		// Turn all (but only) array items also existing in $defaults into class properties.
		// Only items existing in $options will be changed and will override the existings values defined with the first call ($defaults).
		foreach (array_intersect_key($options, $this->defaults) as $key => $value) {
			$this->$key = $value;
		}
			
		$configArray = array_intersect_key((array)get_object_vars($this), $this->defaults);
		
		// Only log debug info in case $options is not empty.
		if (!empty($options)) {
			Debug::log(array('Options' => $options, 'Current Config' => $configArray), json_encode($options, JSON_UNESCAPED_SLASHES));
		}
		
		return $configArray;
	
	}


	/**
	 *	Return the default options array.
	 *      
	 *	@return array Default options
	 */
	
	public function getDefaults() {
		
		return $this->defaults;
		
	}


	/**
	 *	Collect all pages matching $type (& optional $context), $template & $search (optional). 
	 *
	 *	The returned pages have to be used to get all relevant tags.
	 *	It is important, that the pages are not filtered by tag here, because that would also eliminate the non-selected tags itself when filtering.   
	 *
	 *	@return array An array of all Page objects matching $type & $template excludng the current page. 
	 */
	
	private function getRelevant() {
				
		$Selection = new Selection($this->collection);
		
		// In case $this->context is an empty string or false, use the current context. 
		// Therefore it is not possible to have a pagelist only including the homepage (context: "").
		// Since that kind of pagelist would always have only one element, that one can be accessed using the "with" statement instead.
		// Note that $context has to be defined with each call again, to leave $this->context untouched - otherwise it would be defined on a second call and therefore would create
		// an infinite loop on recursive pagelists.
		if ($this->context) {
			$context = $this->context;
		} else {
			$context = $this->Context->get()->origUrl;
		}
		
		// Filter by type.
		// To allow mixed types (o and false) and not only strings, 
		// $this->type has to be casted to a string!
		switch ((string)$this->type) {
			case 'children':
				$Selection->filterByParentUrl($context);
				break;
			case 'related':
				$Selection->filterRelated($this->Context->get());
				break;
			case 'siblings':
				$Selection->filterByParentUrl($this->Context->get()->parentUrl);
				break;
			case 'breadcrumbs':
				$Selection->filterBreadcrumbs($context);
				break;
		}
		
		// Filter only if type is not 'breadcrumbs'.
		// Note that there must be a strict comparison, since the type could be (false or 0).
		if ($this->type !== 'breadcrumbs') {
			$Selection->filterByTemplate($this->template);
			$Selection->filterByKeywords($this->search);
			$Selection->match(json_decode($this->match, true));
		}
	
		return $Selection->getSelection($this->excludeHidden, $this->excludeCurrent);
			
	}
	
	
	/**
	 *	Return all tags from all pages in $relevant as array.
	 *
	 *	@return array A sorted array with the relevant tags.
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
	 *	The final set of Page objects - filtered.    
	 *
	 *	Note that $offset & $limit only reduce the output and not the array of relevant pages! Using the getTags() method will still output all tags, 
	 *	even if pages with such tags are not returned due to the limit. Sorting a pagelist will also sort all pages and therefore the set of returned pages might
	 *	always be different.
	 *
	 *	@param boolean $ignoreLimit
	 *	@return array The filtered and sorted array of Page objects
	 */
	
	public function getPages($ignoreLimit = false) {
		
		$offset = 0;
		$limit = NULL;
		$Selection = new Selection($this->getRelevant());
		
		// Only sort, filter and limit the pagelist output if type is not 'breadcrumbs'.
		// Note the strict comparison to allow other types then strings as well as possible values
		// for $this->type (0 or false).
		if ($this->type !== 'breadcrumbs') {
			
			$Selection->sortPages($this->sort);
			$Selection->filterByTag($this->filter);
			
			// Set limit & offset to the config values if $ignoreLimit is false, $this->limit is not false and $type is not 'breadcrumbs'.
			if (!$ignoreLimit && $this->limit) {
				
				$limit = $this->limit;
				
				// If $this->page is not false, calculate the offset by the current pagination page: (page - 1) * limit.
				if ($this->page) {
					$offset = ($this->page - 1) * $this->limit;
				} else {
					$offset = $this->offset;
				}
				
			}
			
		}
		
		$pages = $Selection->getSelection($this->excludeHidden, $this->excludeCurrent, $offset, $limit);
		
		Debug::log(array_keys($pages));
		
		return $pages;
			
	}
	
	
	/**
	 *	Calculate the number of pages of the pagination.
	 *      
	 *	@return number The number of pages of the current pagelist.
	 */
	
	public function getPaginationCount() {
		
		if ($this->limit) {
			return ceil(count($this->getPages(true)) / $this->limit);
		} else {
			return 1;
		}
			
	}
	

}
