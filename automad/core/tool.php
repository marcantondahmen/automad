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


/**
 * 	The Tool class holds all methods to be used within the template files.
 */


class Tool {
	

	/**
	 * 	Site object.
	 */
	
	private $S;
	
	
	/**
	 *	The full collection of pages.
	 */
	
	private $collection;
	
	
	/**
	 * 	Current Page object.
	 */
	
	private $P;
	
	
	/**
	 * 	The modus defines the way a selection of pages gets sortet.
	 *	
	 *	Default is empty, to make sure the original order is kept when leaving out $this->sortBy().
	 */
	
	private $sortType = '';
	
	
	/**
	 * 	Sort order for selections.
	 *
	 *	Default is SORT_ASC, to make sure the original order is kept when leaving out $this->sortAscending().
	 */
	
	private $sortDirection = SORT_ASC;
	
	
	/**
	 * 	The Site object is passed as an argument. It shouldn't be created again (performance).
	 */
		
	public function __construct($site) {
		
		$this->S = $site;
		$this->collection = $this->S->getCollection();
		$this->P = $this->S->getCurrentPage();
		
	}
	
	
	/**
	 *	Place a set of the current page's tags and link back to the parent page passing each tag as a filter.
	 *
	 *	@return the HTML of the filters
	 */

	public function filterParentByTags() {
		
		return Html::generateFilterMenu($this->P->tags, BASE_URL . $this->P->parentRelUrl);
		
	}
	
	
	/**
	 *	To place the homepage at the same level like all the other pages from the first level,
	 *	includeHome() will modify $this->collection and move the homepage one level down: 0 -> 1
	 */
	
	public function includeHome() {
		
		$selection = new Selection($this->collection);
		$selection->makeHomePageFirstLevel();
		$this->collection = $selection->getSelection();
		
	}


	/**
	 * 	Return the HTML for a list of all pages excluding the current page.
	 *	The variables to be included in the output are set in a comma separated parameter string ($varStr).
	 *
	 *	@param string $varStr
	 *	@return the HTML of the list
	 */
	
	public function listAll($varStr) {
	
		$selection = new Selection($this->collection);	
	
		if (isset($_GET['filter'])) {
			$selection->filterByTag($_GET['filter']);
		}
		
		if (isset($_GET['sort_type'])) {
			$this->sortType = $_GET['sort_type'];
		}
		
		if (isset($_GET['sort_dir'])) {
			$this->sortDirection = constant(strtoupper($_GET['sort_dir']));
		}
	
		$selection->sortPages($this->sortType, $this->sortDirection);
	
		$pages = $selection->getSelection();
	
		// Remove current page from selecion
		unset($pages[$this->P->relUrl]);
	
		return Html::generateList($pages, $varStr);	
		
	}

	
	/**
	 * 	Return the HTML for a list of pages below the current page.
	 *	The variables to be included in the output are set in a comma separated parameter string ($varStr).
	 *
	 *	@param string $varStr
	 *	@return the HTML of the list
	 */
	
	public function listChildren($varStr) {
		
		$selection = new Selection($this->collection);
		$selection->filterByParentUrl($this->P->relUrl);
		
		if (isset($_GET['filter'])) {
			$selection->filterByTag($_GET['filter']);
		}
		
		if (isset($_GET['sort_type'])) {
			$this->sortType = $_GET['sort_type'];
		}
		
		if (isset($_GET['sort_dir'])) {
			$this->sortDirection = constant(strtoupper($_GET['sort_dir']));
		}
		
		$selection->sortPages($this->sortType, $this->sortDirection);
		
		return Html::generateList($selection->getSelection(), $varStr);
		
	}
	

	/**
	 *	Place a set of all tags (sitewide) to filter the full page list.
	 *
	 *	This method should be used together with the listAll() method.
	 *
	 *	@return the HTML of the filters
	 */
	
	public function menuFilterAll() {
		
		$selection = new Selection($this->collection);
		$pages = $selection->getSelection();
		
		// Remove current page from selecion
		unset($pages[$this->P->relUrl]);
		
		$tags = array();
		
		foreach ($pages as $page) {
			
			$tags = array_merge($tags, $page->tags);
			
		}
		
		$tags = array_unique($tags);
		sort($tags);
		
		return Html::generateFilterMenu($tags);
		
	}


	/**
	 *	Place a set of all tags included in the children pages to filter the children page list.
	 *
	 *	This method should be used together with the listChildren() method.
	 *
	 *	@return the HTML of the filters
	 */
	
	public function menuFilterChildren() {
		
		$selection = new Selection($this->collection);
		$selection->filterByParentUrl($this->P->relUrl);
		$pages = $selection->getSelection();
		
		$tags = array();
		
		foreach ($pages as $page) {
			
			$tags = array_merge($tags, $page->tags);
			
		}
		
		$tags = array_unique($tags);
		sort($tags);
		
		return Html::generateFilterMenu($tags);
		
	}

	
	/**
	 *	Place a menu to select the sort direction.
	 *
	 *	@param string $optionStr (optional) example: $[menuSortDirection(SORT_ASC: Up, SORT_DESC: Down)]  
	 *	@return the HTML for the sort menu
	 */
	
	public function menuSortDirection($optionStr) {
		
		return Html::generateSortDirectionMenu($optionStr);
		
	}
		

	/**
	 *	Place a set of sort options for all existing lists on the current page.
	 *
	 *	@param string $optionStr / example: $[menuSortType(Original, title: Title, date: Date, variablename: Title ...)]  
	 *	@return the HTML for the sort menu
	 */

	public function menuSortType($optionStr) {
		
		return Html::generateSortTypeMenu($optionStr);
		
	}

	
	/**
	 *	Generate a list for the navigation below a given URL.
	 *
	 *	@param string $parentRelUrl
	 *	@return html of the generated list	
	 */
	
	public function navBelow($parentUrl) {
				
		$selection = new Selection($this->collection);
		$selection->filterByParentUrl($parentUrl);
		$selection->sortPages($this->sortType, $this->sortDirection);
		
		return Html::generateNav($selection->getSelection());
		
	}
	

	/**
	 * 	Generate breadcrumbs to current page.
	 *
	 *	@return html of breadcrumb navigation
	 */
	
	public function navBreadcrumbs() {
		
		$pages = array();
		$urlSegments = explode('/', $this->P->relUrl);
		$urlSegments = array_unique($urlSegments);
		$tempUrl = '';
		
		foreach ($urlSegments as $urlSegment) {
			
			$tempUrl = '/' . trim($tempUrl . '/' . $urlSegment, '/');
			$pages[] = $this->S->getPageByUrl($tempUrl); 
			
		}
		
		return Html::generateBreadcrumbs($pages);
		
	}
	
		
	/**
	 *	Generate a list for the navigation below the current page.
	 *
	 *	@return html of the generated list	
	 */
	
	public function navChildren() {
	
		return $this->navBelow($this->P->relUrl);
		
	}
	
	
	/**
	 *	Generate a seperate navigation menu for each level within the current path.
	 *
	 *	@return the HTML for the seperate navigations
	 */
	
	public function navPerLevel() {
		
		$urlSegments = explode('/', $this->P->relUrl);
		$urlSegments = array_unique($urlSegments);
		$tempUrl = '';
		$html = '';
				
		foreach ($urlSegments as $urlSegment) {
			
			$tempUrl = '/' . trim($tempUrl . '/' . $urlSegment, '/');	
			$html .= $this->navBelow($tempUrl);
					
		}
		
		return $html;
		
	}
	
	
	/**
	 *	Generate a list for the navigation below the current page's parent.
	 *
	 *	@return html of the generated list	
	 */
	
	public function navSiblings() {
		
		return $this->navBelow($this->P->parentRelUrl);
		
	}
	
	
	/**
	 *	Generate a list for the navigation at the top level including the home page (level 0 & 1).
	 *
	 *	@return html of the generated list	
	 */
	
	public function navTop() {
	
		$selection = new Selection($this->collection);
		$selection->filterByParentUrl('/');
		$selection->sortPages($this->sortType, $this->sortDirection);
		
		return Html::generateNav($selection->getSelection());
		
	}
	
	
	/**
	 * 	Generate full navigation tree.
	 *
	 *	@return the HTML of the tree
	 */
	
	public function navTree() {
				
		return Html::generateTree($this->collection);
	
	}
	
	
	/**
	 * 	Generate navigation tree expanded only along the current page's path.
	 *
	 *	@return the HTML of the tree
	 */
	
	public function navTreeCurrent() {
				
		return Html::generateTree($this->collection, false);
	
	}

	
	/**
	 * 	Generate a list of pages having at least one tag in common with the current page.
	 *
	 *	@param string $varString
	 *	@return html of the generated list
	 */
	
	public function relatedPages($varStr) {
		
		$pages = array();
		$tags = $this->P->tags;
		
		// Get pages
		foreach ($tags as $tag) {
			
			$selection = new Selection($this->collection);
			$selection->filterByTag($tag);			
			$pages = array_merge($pages, $selection->getSelection());
						
		}
		
		// Remove current page from selecion
		unset($pages[$this->P->relUrl]);
		
		// Sort pages
		$selection = new Selection($pages);
		$selection->sortPages($this->sortType, $this->sortDirection);
		
		return Html::generateList($selection->getSelection(), $varStr);
				
	}
	
		
	/**
	 * 	Place a search field with placeholder text.
	 *
	 *	@param string $varStr (placeholder text)
	 *	@return the HTML of the searchfield
	 */
	
	public function searchField($varStr) {
		
		$url = BASE_URL . $this->S->getSiteData('resultsPageUrl');
		
		return Html::generateSearchField($url, $varStr);
		
	}

	
	/**
	 * 	Generate a list of search results.
	 *
	 *	@param string $varString
	 *	@return html of the generated list
	 */
	
	public function searchResults($varStr) {
		
		if (isset($_GET["search"])) {
			
			$search = $_GET["search"];
			
			$selection = new Selection($this->collection);
			$selection->filterByKeywords($search);
			$selection->sortPages($this->sortType, $this->sortDirection);
			
			$pages = $selection->getSelection();
			
			return Html::generateList($pages, $varStr);
			
		}
		
	}
	
	
	/**
	 * 	Resets the sort mode to the original file system order.
	 *	
	 *	If Selection::sortPages() gets passed an empty variable as mode, it will fall back to Selection::sortPagesByPath().
	 */
	
	public function sortOriginal() {
		
		// Check if query string is empty to prevent overriding user actions.
		if (!isset($_GET['sort_type'])) {
			$this->sortType = NULL;
		}
		
	}
	
	
	/**
	 * 	Sets the $key in Page::data[$key] as the sort mode for all following lists and navigations.
	 *
	 *	@param string $var (any variable set in the text file of the page)
	 */
	
	public function sortBy($var) {
		
		// Check if query string is empty to prevent overriding user actions.
		if (!isset($_GET['sort_type'])) {	
			$this->sortType = $var;
		}
		
	}
	
	
	/**
	 * 	Sets the sort order to ascending for all following lists and navigations.
	 */
	
	public function sortAscending() {
		
		// Check if query string is empty to prevent overriding user actions.
		if (!isset($_GET['sort_dir'])) {
			$this->sortDirection = SORT_ASC;
		}
		
	}
	
	
	/**
	 * 	Sets the sort order to descending for all following lists and navigations.
	 */
	
	public function sortDescending() {
		
		// Check if query string is empty to prevent overriding user actions.
		if (!isset($_GET['sort_dir'])) {
			$this->sortDirection = SORT_DESC;
		}
		
	}
	

	/**
	 * 	Return the site name
	 *
	 *	@return site name
	 */
	
	public function siteName() {
		
		return $this->S->getSiteName();
		
	}
	
	
	/**
	 * 	Return the URL of the page theme
	 *
	 *	@return page theme URL
	 */
	
	public function themeURL() {
		
		return str_replace(BASE_DIR, BASE_URL, $this->S->getThemePath());
		
	}
	
	
}


?>
