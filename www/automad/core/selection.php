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
 * 	The Selection class holds all methods to filter and sort the collection of pages and return them as a new selection.
 *
 *	Every instance can return a filtered and sorted array of pages without hurting the original Automad object.
 *	That means the Automad class object has to be created only once. 
 *	To get multiple different (sorted and filtered) collections, this class can be used by just passing the collection array.
 *
 *	All the filter function directly modify $this->selection. After all modifications to that selection, 
 *	it can be returned once by $this->getSelection().
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Selection {
	
	
	/**
	 * 	Initially holds the whole collection.
	 *	
	 *	$selection is basically the internal working copy of the collection array.
	 *	It can be sorted and filtered without hurting the Automad::siteCollection.
	 */
	
	private $selection = array();
	
	
	/**
	 * 	Pass a set of pages to $this->selection excluding all hidden pages.
	 *	
	 *	@param array $pages (normally Automad::getCollection() or any other selection array)
	 */
	
	public function __construct($pages) {
		
		$this->selection = $pages;
		
	}
	
	
	/**
	 *	Exclude all hidden pages from the selection.
	 */
	
	private function excludeHidden() {
		
		foreach ($this->selection as $url => $Page) {
			
			if ($Page->hidden) {
				unset($this->selection[$url]);
			}
			
		}
		
	}
	
	
	/**
	 *	Remove a page from the selection.
	 *
	 *	@param string $url
	 */
	
	public function excludePage($url) {
		
		if (array_key_exists($url, $this->selection)) {
			unset($this->selection[$url]);
		} 
		
	}
	
	
	/**
	 * 	Return the array with the selected (filtered and sorted) pages.
	 *
	 *	@param boolean $excludeHidden
	 *	@param integer $offset
	 *	@param integer $limit
	 *	@return array $this->selection
	 */
	
	public function getSelection($excludeHidden = true, $offset = 0, $limit = NULL) {
		
		if ($excludeHidden) {
			$this->excludeHidden();
		}
		
		return array_slice($this->selection, $offset, $limit);
		
	}
	
	
	/**
	 *	Collect all pages along a given URL.
	 *
	 *	@param string $url
	 */
	
	public function filterBreadcrumbs($url) {
			
		// Test wheter $url is the URL of a real page.
		// "Real" pages have a URL (not like search or error pages) and they exist in the selection array (not hidden).
		// For all other $url, just the home page will be returned.	
		if (strpos($url, '/') === 0 && array_key_exists($url, $this->selection)) {
		
			$pages = array();
			
			// While $url is not the home page, strip each segement one by one and
			// add the corresponding Page object to $pages.
			while ($url != '/') {
				
				$pages[$url] = $this->selection[$url];
				$url = '/' . trim(substr($url, 0, strrpos($url, '/')), '/');
				
			}
			
			// Add home page
			$pages['/'] = $this->selection['/'];
			
			// Reverse the $pages array and pass it to $this->selection.
			$this->selection = array_reverse($pages);
		
		} else {
			
			// If $url is not a valid URL, only add the home page to the selection.
			// This might be the case for "virtual pages", like the "error" or "search results" pages, 
			// which don't have a $page->url.
			$this->selection = array($this->selection['/']);
			
		}
		
	}
	
	
	/**
	 *	Filter $this->selection by relative url of the parent page.
	 *
	 *	@param string $parent
	 */
	
	public function filterByParentUrl($parent) {
		
		$filtered = array();
		
		foreach ($this->selection as $key => $Page) {
			if ($Page->parentUrl == $parent) {
				$filtered[$key] = $Page;
			}
		}
		
		$this->selection = $filtered;
		
	}
	
	
	/**
	 *	Filter $this->selection by tag.
	 *
	 *	@param string $tag
	 */
	
	public function filterByTag($tag) {
		
		if ($tag) {
		
			$filtered = array();
		
			foreach ($this->selection as $key => $Page) {
			
				if (in_array($tag, $Page->tags)) {
					$filtered[$key] = $Page;
				}
			
			}
		
			$this->selection = $filtered;
		
		} 
		
	}
	
		
	/**
	 *	Filter $this->selection by a template, if $template is not empty.
	 *
	 *	@param string $template
	 */
	
	public function filterByTemplate($template) {
		
		if ($template) {
		
			$filtered = array();
		
			foreach ($this->selection as $key => $Page) {
				if ($Page->template == $template) {
					$filtered[$key] = $Page;
				}
			}
		
			$this->selection = $filtered;
		
		}
		
	}
	
		 
	/**
	 *	Filter $this->selection by multiple keywords (a search string), if $str is not empty.
	 *
	 *	@param string $str
	 */
	
	public function filterByKeywords($str) {
		
		if ($str) {
			
			$filtered = array();

			// Explode keywords and also remove any tags and - most important - all "/", since they will be used as regex delimiters!
			$keywords = explode(' ', str_replace('/', ' ', strip_tags($str)));
		
			// generate pattern
			$pattern = '/^';
			foreach ($keywords as $keyword) {
				$pattern .= '(?=.*' . preg_quote($keyword) . ')';
			}
			// case-insensitive and multiline
			$pattern .= '/is';
		
			// loop elements in $this->selection
			foreach ($this->selection as $key => $Page) {
			
				// All the page's data get combined in on single string ($dataAsString), to make sure that a page gets returned, 
				// even if the keywords are distributed over different variables in $Page[data]. 
				$dataAsString = strip_tags(implode(' ', $Page->data));
								
				// search
				if (preg_match($pattern, $dataAsString) == 1) {
					$filtered[$key] = $Page;
				}
			
			}
		
			$this->selection = $filtered;
			
		}
		
	}
	
	
	/**
	 *	Filter out the neighbors (previous and next page) to the passed URL under the same parent URL.
	 *
	 *	$this->selection only holds two pages after completion with the keys ['prev'] and ['next'] instead of the URL-key.
	 *	If there is only one page in the array (has no siblings), the selection will be empty. For two pages, it will only
	 *	contain the ['next'] page. For more than two pages, both neighbors will be set in the selection.
	 *
	 *	@param string $url
	 */	
	
	public function filterPrevAndNextToUrl($url) {
		
		// To be able to hide the hidden pages as neighbors and jump directly to the closest non-hidden pages (both sides),
		// in case one or both neigbors is/are hidden, $this->excludeHidden() has to be called here already, because only excluding the hidden pages
		// later, when calling getSelection(), will cause a "gap" in the neighbors-array, which will lead to a missing link, for a hidden neighbor.
		// To handle hidden pages correctly, the current page has to be temporary stored in $current, in case the current page itself is hidden, because the 
		// curretn page is needed, even when hidden, to determine the closest neighbors.
		$current = $this->selection[$url];
		$this->excludeHidden();
		$this->selection[$url] = $current;
		
		// Narrow down selection to pages with the same parentUrl
		$this->filterByParentUrl($this->selection[$url]->parentUrl);
		$this->sortPagesByBasename();
		
		$keys = array_keys($this->selection);
		$keyIndexes = array_flip($keys);
		
		$neighbors = array();
		
		// Check number of pages
		if (sizeof($keys) > 1) {
	
			if (sizeof($keys) > 2) {
		
				// Previous
				if (isset($keys[$keyIndexes[$url]-1])) {
					$neighbors['prev'] = $this->selection[$keys[$keyIndexes[$url]-1]];
				} else {
					$neighbors['prev'] = $this->selection[$keys[sizeof($keys)-1]];
				}
			
			}
			
  		       	// Next
		  	if (isset($keys[$keyIndexes[$url]+1])) {
				$neighbors['next'] = $this->selection[$keys[$keyIndexes[$url]+1]];
			} else {
				$neighbors['next'] = $this->selection[$keys[0]];
			}
		
		}
		
		$this->selection = $neighbors;
		
	}
	
	
	/**
	 *	Filter all pages having one or more tag in common with $Page. If there are not tags defined for the passed page,
	 *	the selection will be an empty array. (no tags = no related pages)
	 *
	 *	@param object $Page
	 */
	
	public function filterRelated($Page) {
		
		$tags = $Page->tags;
		
		$filtered = array();
		
		if ($tags) {
		
			foreach ($tags as $tag) {
			
				foreach($this->selection as $key => $p) {
		
					if (in_array($tag, $p->tags)) {
						$filtered[$key] = $p;
					}			
					
				}		
						
			}
	
		}
		
		$this->selection = $filtered;
		$this->excludePage($Page->url);
		
	}
	
	  
	/**
	 *	Sorts the $this->selection based on the file system path's basename.
	 *
	 *	@param string $order (optional: SORT_ASC, SORT_DESC)
	 */ 
	 
	public function sortPagesByBasename($order = SORT_ASC) {
		
		$arrayToSortBy = array();
		
		foreach ($this->selection as $key => $Page) {
			
			$arrayToSortBy[$key] = basename($Page->path);
			
		}
				
		array_multisort($arrayToSortBy, $order, $this->selection);
				
	}

	 
	/**
	 *	Sorts $this->selection based on any variable in the text files.
	 *	If the $var gets passed empty, $this->sortPagesByBasename() will be used as fallback.
	 *	If a variable doesn't exist for page, that page's value in $arrayToSortBy will be set to its basename.
	 *	That allows for simply sorting a selection by original order from the HTML class by passing an invalid var like "-" or "orig" etc.
	 *
	 *	@param string $var (any variable from a text file)
	 *	@param string $order (optional: SORT_ASC, SORT_DESC)
	 */  
	 
	public function sortPages($var, $order = SORT_ASC) {
		
		if ($var) {
			
			// If $var is set, the selections is sorted by data[$var]
			$arrayToSortBy = array();
		
			foreach ($this->selection as $key => $Page) {
			
				if (isset($Page->data[$var])) {
					$arrayToSortBy[$key] = strtolower(strip_tags($Page->data[$var]));
				} else {
					// If data[$var] doesn't exists, the page's path's basename will be used.
					// That way it is possible to order by basename with simply passing a non-existing var (for example "orig" or something else).
					$arrayToSortBy[$key] = basename($Page->path);
				}
			
			}
	
			array_multisort($arrayToSortBy, $order, $this->selection);
					
		} else {
			
			// else the selection is sorted by the file system path's basename
			$this->sortPagesByBasename($order);
			
		}
		
	} 
	 
	
}


?>