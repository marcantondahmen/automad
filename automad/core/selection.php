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
 *	(c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
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
	 * 	Gets the collection.
	 *
	 *	Basically $collection means Site::getCollection(), assuming $site is an instance of Site.
	 *	
	 *	@param array $collection (Site::getCollection())
	 */
	
	public function __construct($collection) {
		
		$this->selection = $collection;
		
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
		
		$filtered = array();
		
		foreach ($this->selection as $key => $page) {
			
			if (in_array($tag, $page->tags)) {
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
		
		$keywords = explode(' ', $str);
		
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
			$dataAsString = implode(" ", $page->data);
				
			// search
			if (preg_match($pattern, $dataAsString) == 1) {
				$filtered[$key] = $page;
			}
			
		}
		
		$this->selection = $filtered;
		
	}
	 
	  
	/**
	 *	Sorts the $this->selection based on the file system path.
	 *
	 *	@param string $order (optional: SORT_ASC, SORT_DESC)
	 */ 
	 
	public function sortByPath($order = SORT_ASC) {
		
		$arrayToSortBy = array();
		
		foreach ($this->selection as $key => $page) {
			
			$arrayToSortBy[$key] = $page->relPath;
			
		}
				
		array_multisort($arrayToSortBy, $order, $this->selection);
				
	}
	 	 
	 
	/**
	 *	Sorts the $this->selection based on the title.
	 *
	 *	@param string $order (optional: SORT_ASC, SORT_DESC)
	 */ 
	 
	public function sortByTitle($order = SORT_ASC) {
		
		$arrayToSortBy = array();
		
		foreach ($this->selection as $key => $page) {
			
			$arrayToSortBy[$key] = strtolower($page->data['title']);
			
		}
				
		array_multisort($arrayToSortBy, $order, $this->selection);
				
	} 
	
	
}


?>