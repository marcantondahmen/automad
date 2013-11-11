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
 * 	The Toolbox class holds all methods to be used within the template files.
 */


class Toolbox {
	

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
	 *	Filter for the page lists.
	 */
	
	private $filter;
	
	
	/**
	 * 	The type defines the way the pages within the lists gets sortet.
	 */
	
	private $sortType;
	
	
	/**
	 * 	Sort order for lists.
	 */
	
	private $sortDirection;
	
	
	/**
	 * 	The Site object is passed as an argument. It shouldn't be created again (performance).
	 */
		
	public function __construct($site) {
		
		$this->S = $site;
		$this->collection = $this->S->getCollection();
		$this->P = $this->S->getCurrentPage();
		
		// Set up filter and sort
		if (isset($_GET['filter'])) {
			$this->filter = $_GET['filter'];
		} else {
			$this->filter = '';
		}
		
		if (isset($_GET['sort_type'])) {
			$this->sortType = $_GET['sort_type'];
		} else {
			$this->sortType = '';
		}
		
		if (isset($_GET['sort_dir'])) {
			$this->sortDirection = constant(strtoupper($_GET['sort_dir']));
		} else {
			$this->sortDirection = constant(strtoupper(TOOL_DEFAULT_SORT_DIR));
		}
		
	}
	
	
	/**
	 *	Place a set of the current page's tags and link back to the parent page passing each tag as a filter.
	 *
	 *	@return the HTML of the filters
	 */

	public function filterParentByTags() {
		
		return Html::generateFilterMenu($this->P->tags, $this->P->parentRelUrl);
		
	}
	

	/**
	 *	Place an image with an optional link.
	 *
	 *	@param string $optionStr - (file: path/to/file, width: px, height: px, crop: 1, link: url, target: _blank)
	 *	@return HTML for the image output
	 */
	
	public function img($optionStr) {
		
		$options = Parse::toolOptions($optionStr);	
		$defaults = Parse::toolOptions(TOOL_IMG_DEFAULTS);
		$options = array_merge($defaults, $options);
		$file = $options[TOOL_FILE_KEY];
				
		if ($file) {
			
			if (strpos($file, '/') === 0) {
				// Relative to root
				$file = BASE_DIR . $file;
			} else {
				// Relative to page
				$path = ltrim($this->P->relPath . '/', '/');
				$file = BASE_DIR . SITE_PAGES_DIR . '/' . $path . $file;
			}
		
			$w = intval($options[TOOL_WIDTH_KEY]);
			$h = intval($options[TOOL_HEIGHT_KEY]);
			$crop = (boolean)intval($options[TOOL_CROP_KEY]);
			$link = $options[TOOL_LINK_KEY];
			$target = $options[TOOL_TARGET_KEY];
		
			return Html::addImage($file, $w, $h, $crop, $link, $target);
			
		}

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
	 *	Place a link to the previous sibling.
	 *
	 *	@return the HTML for the link.
	 */

	public function linkPrev() {
		
		$selection = new Selection($this->collection);
		$selection->filterPrevAndNextToUrl($this->P->relUrl);
		
		$pages = $selection->getSelection();
		
		// Check if there is a previous page and return HTML
		if (isset($pages['prev'])) {
			return Html::addLink($pages['prev'], HTML_CLASS_PREV);
		}
		
	}
	
	
	/**
	 *	Place a link to the previous sibling.
	 *
	 *	@return the HTML for the link.
	 */
	
	public function linkNext() {
		
		$selection = new Selection($this->collection);
		$selection->filterPrevAndNextToUrl($this->P->relUrl);
		
		$pages = $selection->getSelection();
		
		// Check if there is a next page and return HTML
		if (isset($pages['next'])) {
			return Html::addLink($pages['next'], HTML_CLASS_NEXT);
		}
		
	}


	/**
	 * 	Return the HTML for a list of all pages excluding the current page.
	 *	The variables to be included in the output are set in a comma separated parameter string ($optionStr).
	 *
	 *	@param string $optionStr - All variables from the page's text file which should be included in the output. Expample: $[listAll(title, subtitle, date)]
	 *	@return the HTML of the list
	 */
	
	public function listAll($optionStr) {
	
		$selection = new Selection($this->collection);	
		$selection->filterByTag($this->filter);
		$selection->sortPages($this->sortType, $this->sortDirection);
		$pages = $selection->getSelection();
	
		// Remove current page from selecion
		unset($pages[$this->P->relUrl]);
	
		return Html::generateList($pages, $optionStr);	
		
	}

	
	/**
	 * 	Return the HTML for a list of pages below the current page.
	 *	The variables to be included in the output are set in a comma separated parameter string ($optionStr).
	 *
	 *	@param string $optionStr - All variables from the page's text file which should be included in the output. Expample: $[listAll(title, subtitle, date)]
	 *	@return the HTML of the list
	 */
	
	public function listChildren($optionStr) {
		
		$selection = new Selection($this->collection);
		$selection->filterByParentUrl($this->P->relUrl);
		$selection->filterByTag($this->filter);
		$selection->sortPages($this->sortType, $this->sortDirection);
		
		return Html::generateList($selection->getSelection(), $optionStr);
		
	}
	

	/**
	 *	Place a set of all tags (sitewide) to filter the full page list. The filter only affects lists of pages created by Toolbox::listAll()
	 *
	 *	This method should be used together with the listAll() method.
	 *
	 *	@return the HTML of the filter menu
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
	 *	Place a set of all tags included in the children pages to filter the list of children pages. The filter only affects lists of pages created by Toolbox::listChildren()
	 *
	 *	This method should be used together with the listChildren() method.
	 *
	 *	@return the HTML of the filter menu
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
	 *	Place a menu to select the sort direction. The menu only affects lists of pages created by Toolbox::listChildren() and Toolbox::listAll()
	 *
	 *	@param string $optionStr (optional) - Example: $[menuSortDirection(SORT_ASC: Up, SORT_DESC: Down)] 
	 *	@return the HTML for the sort menu
	 */
	
	public function menuSortDirection($optionStr) {
		
		// $this->sortDirection gets passed as well to let Html know what flag is set to apply the correct "current" class to the HTML tag
		return Html::generateSortDirectionMenu($this->sortDirection, $optionStr);
		
	}
		

	/**
	 *	Place a set of sort options. The menu only affects lists of pages created by Toolbox::listChildren() and Toolbox::listAll()
	 *
	 *	@param string $optionStr (optional) - Example: $[menuSortType(Original, title: Title, date: Date, variablename: Title ...)]  
	 *	@return the HTML for the sort menu
	 */

	public function menuSortType($optionStr) {
		
		return Html::generateSortTypeMenu($optionStr);
		
	}

	
	/**
	 *	Generate a list for the navigation below a given URL.
	 *
	 *	@param string $optionStr - the URL of the parent page of the displayed pages.
	 *	@return html of the generated list	
	 */
	
	public function navBelow($optionStr) {
				
		$selection = new Selection($this->collection);
		$selection->filterByParentUrl($optionStr);
		$selection->sortPagesByPath();
		
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
	 *	@param string $optionStr (optional) - The maximal level to display.
	 *	@return the HTML for the seperate navigations
	 */
	
	public function navPerLevel($optionStr) {
		
		$maxLevel = (int)trim($optionStr);
		$urlSegments = explode('/', $this->P->relUrl);
		$urlSegments = array_unique($urlSegments);
		$tempUrl = '';
		$html = '';
				
		foreach ($urlSegments as $urlSegment) {
			
			$tempUrl = '/' . trim($tempUrl . '/' . $urlSegment, '/');	
			
			if ((int)$this->S->getPageByUrl($tempUrl)->level < $maxLevel || !$maxLevel) {
				$html .= $this->navBelow($tempUrl);
			}
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
		$selection->sortPagesByPath();
		
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
	 *	@param string $optionStr - Variables from the text files to be included in the output. Example: $[relatedPages(title, date)]
	 *	@return html of the generated list
	 */
	
	public function relatedPages($optionStr) {
		
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
		$selection->sortPagesByPath();
		
		return Html::generateList($selection->getSelection(), $optionStr);
				
	}
	
		
	/**
	 * 	Place a search field with placeholder text.
	 *
	 *	@param string $optionStr (optional) - placeholder text
	 *	@return the HTML of the searchfield
	 */
	
	public function searchField($optionStr) {
		
		return Html::generateSearchField(SITE_RESULTS_PAGE_URL, $optionStr);
		
	}

	
	/**
	 * 	Generate a list of search results.
	 *
	 *	@param string $optionStr (optional) - Variables from the text files to be included in the output. Example: $[searchResults(title, date)]
	 *	@return the HTML for the results list
	 */
	
	public function searchResults($optionStr) {
		
		if (isset($_GET["search"])) {
			
			$search = $_GET["search"];
			
			$selection = new Selection($this->collection);
			$selection->filterByKeywords($search);
			$selection->sortPagesByPath();
			
			$pages = $selection->getSelection();
			
			return Html::generateList($pages, $optionStr);
			
		}
		
	}
		

	/**
	 * 	Return any item from the site settings file (/shared/site.txt).
	 *
	 *	@param string $optionStr
	 *	@return site data item
	 */
	
	public function siteData($optionStr) {
		
		return $this->S->getSiteData($optionStr);
		
	}


	/**
	 * 	Return the site name.
	 *
	 *	@return site name
	 */
	
	public function siteName() {
		
		return $this->S->getSiteName();
		
	}
	
	
	/**
	 * 	Return the URL of the page theme.
	 *
	 *	@return page theme URL
	 */
	
	public function themeURL() {
		
		return $this->S->getThemePath();
		
	}
	
	
	/**
	 *	Return the current year.
	 *
	 *	@return current year
	 */
	
	public function year() {
		
		return date('Y');
		
	}
	
	
}


?>
