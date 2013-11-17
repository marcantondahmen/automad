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
 * 	The Selection class holds all methods to filter and sort the collection of pages and return them as a new selection.
 *
 *	Every instance can return a filtered and sorted array of pages without hurting the original Site object.
 *	That means the Site class object has to be created only once. 
 *	To get multiple different (sorted and filtered) collections, this class can be used by just passing the collection array.
 *
 *	All the filter function directly modify $this->selection. After all modifications to that selection, 
 *	it can be returned once by $this->getSelection().
 */


class Selection {
	
	
	/**
	 * 	Initially holds the whole collection.
	 *	
	 *	$selection is basically the internal working copy of the collection array.
	 *	It can be sorted and filtered without hurting the Site::siteCollection.
	 */
	
	private $selection = array();
	
	
	/**
	 * 	Gets the collection or a previously created selection (array) of pages.
	 *
	 *	Basically $pages means Site::getCollection(), assuming $site is an instance of Site.
	 *	
	 *	@param array $pages (normally Site::getCollection() or any other selection array)
	 */
	
	public function __construct($pages) {
		
		$this->selection = $pages;
		
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
	 *	@return array $this->selection
	 */
	
	public function getSelection() {
		
		return $this->selection;
		
	}
	
	
	/**
	 *	Filter $this->selection by relative url of the parent page.
	 *
	 *	@param string $parent
	 */
	
	public function filterByParentUrl($parent) {
		
		$filtered = array();
		
		foreach ($this->selection as $key => $page) {
			if ($page->parentRelUrl == $parent) {
				$filtered[$key] = $page;
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
		
			foreach ($this->selection as $key => $page) {
			
				if (in_array($tag, $page->tags)) {
					$filtered[$key] = $page;
				}
			
			}
		
			$this->selection = $filtered;
		
		} 
		
	}

	
	/**
	 *	Filter $this->selection by the template of the parent page.
	 *
	 *	@param string $template
	 */
	
	public function filterByTemplate($template) {
		
		$filtered = array();
		
		foreach ($this->selection as $key => $page) {
			if ($page->template == $template) {
				$filtered[$key] = $page;
			}
		}
		
		$this->selection = $filtered;
		
	}
	
		 
	/**
	 *	Filter $this->selection by multiple keywords (a search string).
	 *
	 *	@param string $str
	 */
	
	public function filterByKeywords($str) {
		
		$filtered = array();
		
		$keywords = explode(' ', strip_tags($str));
		
		// generate pattern
		$pattern = '/^';
		foreach ($keywords as $keyword) {
			$pattern .= '(?=.*' . $keyword . ')';
		}
		// case-insensitive and multiline
		$pattern .= '/is';
		
		// loop elements in $this->selection
		foreach ($this->selection as $key => $page) {
			
			// All the page's data get combined in on single string ($dataAsString), to make sure that a page gets returned, 
			// even if the keywords are distributed over different variables in $page[data]. 
			$dataAsString = strip_tags(implode(" ", $page->data));
								
			// search
			if (preg_match($pattern, $dataAsString) == 1) {
				$filtered[$key] = $page;
			}
			
		}
		
		$this->selection = $filtered;
		
	}
	
	
	/**
	 *	Filter out the neighbors (prevoius and next page) to the passed URL under the same parent URL.
	 *
	 *	$this->selection only holds two pages after completion with the keys ['prev'] and ['next'] instead of the URL-key.
	 *	If there is only one page in the array (has no siblings), the selection will be empty. For two pages, it will only
	 *	contain the ['next'] page. For more than two pages, both neighbors will be set in the selection.
	 *
	 *	@param string $url
	 */	
	
	public function filterPrevAndNextToUrl($url) {
		
		// Narrow down selection to pages with the same parentUrl
		$this->filterByParentUrl($this->selection[$url]->parentRelUrl);
		$this->sortPagesByPath();
		
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
	 * 	Makes the Home Page a neighbor of all level 1 pages. Useful for filtering the top level pages all together.
	 */
	
	public function makeHomePageFirstLevel() {
		
		if (array_key_exists('/', $this->selection)) {
			
			$home = clone $this->selection['/'];
			$home->parentRelUrl = '/';
			$home->level = 1;
			$this->selection['/'] = $home;
			
		}
		
	}
	
	  
	/**
	 *	Sorts the $this->selection based on the file system path.
	 *
	 *	@param string $order (optional: SORT_ASC, SORT_DESC)
	 */ 
	 
	public function sortPagesByPath($order = SORT_ASC) {
		
		$arrayToSortBy = array();
		
		foreach ($this->selection as $key => $page) {
			
			$arrayToSortBy[$key] = $page->relPath;
			
		}
				
		array_multisort($arrayToSortBy, $order, $this->selection);
				
	}

	 
	/**
	 *	Sorts $this->selection based on any variable in the text files.
	 *
	 *	If the $var gets passed empty, $this->sortPagesByPath() will be used as fallback.
	 *
	 *	@param string $var (any variable from a text file)
	 *	@param string $order (optional: SORT_ASC, SORT_DESC)
	 */  
	 
	public function sortPages($var, $order = SORT_ASC) {
		
		if ($var) {
			// If $var is set the selections is sorted by data[$var]
			$arrayToSortBy = array();
		
			foreach ($this->selection as $key => $page) {
			
				if (isset($page->data[$var])) {
					$arrayToSortBy[$key] = strtolower($page->data[$var]);
				} else {
					// If data[$var] doesn't exists, an empty string will be added
					$arrayToSortBy[$key] = '';
				}
			
			}
				
			array_multisort($arrayToSortBy, $order, $this->selection);
		
		} else {
			// else the selection is sorted by the file system path
			$this->sortPagesByPath($order);
		}
		
	} 
	 
	
}


?>