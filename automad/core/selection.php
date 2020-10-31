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
 * 	The Selection class holds all methods to filter and sort the collection of pages and return them as a new selection.
 *
 *	Every instance can return a filtered and sorted array of pages without hurting the original Automad object.
 *	That means the Automad class object has to be created only once. 
 *	To get multiple different (sorted and filtered) collections, this class can be used by just passing the collection array.
 *
 *	All the filter function directly modify $this->selection. After all modifications to that selection, 
 *	it can be returned once by $this->getSelection().
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2013-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Selection {
	
	
	/**
	 * 	Initially holds the whole collection.
	 *	
	 *	$selection is basically the internal working copy of the collection array.
	 *	It can be sorted and filtered without hurting the original collection.
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
	 *	Exclude the current page from the selection.
	 */
	
	public function excludeCurrent() {
		
		$this->excludePage(AM_REQUEST);
		
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
		
		if ($url && array_key_exists($url, $this->selection)) {
			unset($this->selection[$url]);
		} 
		
	}
	
	
	/**
	 * 	Return the array with the selected (filtered and sorted) pages.
	 *
	 *	@param boolean $excludeHidden
	 *	@param boolean $excludeCurrent
	 *	@param integer $offset
	 *	@param integer $limit
	 *	@return array $this->selection
	 */
	
	public function getSelection($excludeHidden = true, $excludeCurrent = false, $offset = 0, $limit = NULL) {
		
		if ($excludeHidden) {
			$this->excludeHidden();
		}
		
		if ($excludeCurrent) {
			$this->excludeCurrent();
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
			// Use identical comparison operator (===) here to avoid getting all pages in case $parent is set true.
			if ($Page->parentUrl === $parent) {
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
	 *	Filter $this->selection by template. A regex can be used as filter string.    
	 *	For example passing 'page|home' as parameter will include all pages with a template that 
	 *	contains 'page' or 'home' as substrings.
	 *
	 *	@param string $regex
	 */
	
	public function filterByTemplate($regex) {
		
		if ($regex) {
		
			$filtered = array();
		
			foreach ($this->selection as $key => $Page) {
				if (preg_match('/(' . $regex . ')/i', $Page->template)) {
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
			$keywords = explode(' ', str_replace('/', ' ', Str::stripTags($str)));
		
			// generate pattern
			$pattern = '/^';
			foreach ($keywords as $keyword) {
				$pattern .= '(?=.*' . preg_quote(trim($keyword)) . ')';
			}
			// case-insensitive and multiline
			$pattern .= '/is';
		
			// loop elements in $this->selection
			foreach ($this->selection as $key => $Page) {
			
				// All the page's data get combined in on single string ($dataAsString), to make sure that a page gets returned, 
				// even if the keywords are distributed over different variables in $Page[data]. 
				$dataAsString = Str::stripTags(implode(' ', $Page->data));
								
				// search
				if (preg_match($pattern, $dataAsString) == 1) {
					$filtered[$key] = $Page;
				}
			
			}
		
			$this->selection = $filtered;
			
		}
		
	}
	
	
	/**
	 *	Filter out the non-hidden neighbors (previous and next page) to the passed URL.
	 *
	 *	$this->selection only holds two pages after completion with the keys ['prev'] and ['next'] instead of the URL-key.
	 *	If there is only one page in the array (has no siblings), the selection will be empty. For two pages, it will only
	 *	contain the ['next'] page. For more than two pages, both neighbors will be set in the selection.
	 *
	 *	@param string $url
	 */	
	
	public function filterPrevAndNextToUrl($url) {
		
		if (array_key_exists($url, $this->selection)) {
		
			// To be able to hide the hidden pages as neighbors and jump directly to the closest non-hidden pages (both sides),
			// in case one or both neigbors is/are hidden, $this->excludeHidden() has to be called here already, because only excluding the hidden pages
			// later, when calling getSelection(), will cause a "gap" in the neighbors-array, which will lead to a missing link, for a hidden neighbor.
			// To keep the correct position of the current page within the selection, even if the current page itself is hidden, 
			// $Page-hidden has to be set temporary to false. 
			$Page = $this->selection[$url];
			// Cache the original value for $Page->hidden.
			$hiddenCache = $Page->hidden;
			$Page->hidden = false;
			$this->excludeHidden();
			// Restore the original value for $Page->hidden.
			$Page->hidden = $hiddenCache;
		
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
	 *	While iterating a set of variable/regex combinations in $options, all pages where
	 *	a given variable is not matching its assigned regex are removed from the selection.
	 *
	 *	@param array $options
	 */

	public function match($options) {

		if (empty($options)) {
			return false;
		}

		if (is_array($options)) {

			foreach ($options as $key => $regex) {

				if (@preg_match($regex, null) !== false) {

					$this->selection = array_filter(
						$this->selection, 
						function($Page) use ($key, $regex) {
							return preg_match($regex, $Page->get($key));
						}
					);
					
				}

			}

		}

	}


	/**
	 *	Sorts $this->selection based on a sorting options string.    
	 *	
	 *	The option string consists of multiple pairs of 
	 *	a data key and a sort order, separated by a comma like this:    
	 *	$Selection->sortPages('date desc, title asc')
	 * 	The above example will sort first all pages in the selection by 'date' (descending) and then by 'title' (ascending).   
	 *           
	 * 	Valid values for the order are 'asc' and 'desc'.      
	 * 	In case a sort order is missing in a key/order combination, the 'asc' is used as a fallback.   
	 *
	 *	@param string $options (comma separated list of keys and order)
	 */  
	 
	public function sortPages($options = false) {
		
		$sort = array();
		$parameters = array();
		
		// Define default option in case an empty string gets passed.
		if (!$options) {
			$options = AM_KEY_BASENAME . ' asc';
		}
		
		// Parse options string.
		
		// First create an array out of single key/order combinations (separated by comma).
		$pairs = Parse::csv($options);
		
		// Append the default sorting order to each pair and create subarrays out of the first two space-separated items.  
		foreach ($pairs as $pair) {
			
			// Add default order to avoid having a string without a given order
			// and convert the first two separate strings into variables ($key and $order).
			// If there is already an order, the default will simply be ignored as the third parameter. 
			list($key, $order) = explode(' ', $pair . ' asc');
			
			// Set order to the default order, if its value is invalid.
			if (!in_array($order, array('asc', 'desc'))) {
				$order = 'asc';
			}
			
			// Create the actual subarray and convert the order into the real constant value.
			$sort[] = array('key' => $key, 'order' => constant(strtoupper('sort_' . $order)));
			
		}
		
		// Add the values to sort by to each sort array.
		foreach ($sort as $i => $sortItem) {
			
			$sort[$i]['values'] = array();
			
			foreach ($this->selection as $url => $Page) {
				$sort[$i]['values'][] = trim(strtolower(Str::stripTags($Page->get($sortItem['key']))));
			}
			
		}
		
		// Build parameters and call array_multisort function.
		foreach ($sort as $sortItem) {
			$parameters[] = $sortItem['values'];
			$parameters[] = $sortItem['order'];
			$parameters[] = SORT_NATURAL;
		}
		
		Debug::log($parameters, 'Parameters');
		
		$parameters[] = &$this->selection;
		call_user_func_array('array_multisort', $parameters);
			
	} 
	 
	
}
