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
	 *	List options
	 */
	
	private $listOptionArray;
	
	
	/**
	 *	Selection of pages to be listed
	 */
	
	private $listSelection;
	
	
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
			$this->sortDirection = constant(strtoupper(AM_TOOL_DEFAULT_SORT_DIR));
		}
		
		// Set default list options
		$this->listOptions();
		
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
	 *	@param string $optionStr - (file: path/to/file (or glob pattern), width: px, height: px, crop: 1, link: url, target: _blank)
	 *	@return HTML for the image output
	 */
	
	public function img($optionStr) {
		
		$options = Parse::toolOptions($optionStr);	
		$defaults = Parse::toolOptions(AM_TOOL_OPTIONS_IMG);
		$options = array_merge($defaults, $options);
		$glob = $options[AM_TOOL_OPTION_KEY_FILE_GLOB];
				
		if ($glob) {
			
			if (strpos($glob, '/') === 0) {
				// Relative to root
				$glob = AM_BASE_DIR . $glob;
			} else {
				// Relative to page
				$glob = AM_BASE_DIR . AM_DIR_PAGES . $this->P->relPath . $glob;
			}
		
			$w = intval($options[AM_TOOL_OPTION_KEY_WIDTH]);
			$h = intval($options[AM_TOOL_OPTION_KEY_HEIGHT]);
			$crop = (boolean)intval($options[AM_TOOL_OPTION_KEY_CROP]);
			$link = $options[AM_TOOL_OPTION_KEY_LINK];
			$target = $options[AM_TOOL_OPTION_KEY_TARGET];
		
			return Html::addImage($glob, $w, $h, $crop, $link, $target);
			
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
			return Html::addLink($pages['prev'], AM_HTML_CLASS_PREV);
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
			return Html::addLink($pages['next'], AM_HTML_CLASS_NEXT);
		}
		
	}


	/**
	 *	Define the options for a following page list.
	 *	The options are defined in one comma separated string.
	 *	Key:Pair item define optional parameters (filter by template or display only children pages) while all single items (title, tags ...)
	 *	define the set of variables from the listed page's txt file to be displayed.
	 *	Example:
	 *	("children_only: 1, template: page, title, subtitle, tags") 
	 *	will set up a list which only shows its children (children_only: 1), which use the "page" template (template: page).
	 *	The data displayed for every page are the title, subtitle and the tags (title, subtitle, tags). 
	 *
	 *	The method doesn't return any variable, but it will define the $listSelection and the $listOptionArray.
	 *	The $listOptionArray is a multidimensional array with the following elements:
	 *	"children_only", "template", "image" (array with image options) and "vars" (array with the txt variables to display).
	 *	
	 *	@param string $optionStr ("children_only: 0, template: page, title, subtitle, tags")
	 */

	public function listOptions($optionStr = '') {
		
		// Parse options and defaults.
		$options = Parse::toolOptions($optionStr);
		$defaults = Parse::toolOptions(AM_TOOL_OPTIONS_LIST);
		$options = array_merge($defaults, $options);
		
		// Make the boolean options boolean.
		if (array_key_exists(AM_TOOL_OPTION_KEY_CHILDREN_ONLY, $options)) {
			$options[AM_TOOL_OPTION_KEY_CHILDREN_ONLY] = (boolean)intval($options[AM_TOOL_OPTION_KEY_CHILDREN_ONLY]);
		} else {
			$options[AM_TOOL_OPTION_KEY_CHILDREN_ONLY] = false;
		}
		
		// Set up image options.
		$options['image'] = array();
	
		if (array_key_exists(AM_TOOL_OPTION_KEY_FILE_GLOB, $options)) {
			
			$options['image']['glob'] = $options[AM_TOOL_OPTION_KEY_FILE_GLOB];
			unset($options[AM_TOOL_OPTION_KEY_FILE_GLOB]);
			
			if (array_key_exists(AM_TOOL_OPTION_KEY_WIDTH, $options)) {
				$options['image']['width'] = intval($options[AM_TOOL_OPTION_KEY_WIDTH]);
				unset($options[AM_TOOL_OPTION_KEY_WIDTH]);
			} else {
				$options['image']['width'] = false;
			}
			
			if (array_key_exists(AM_TOOL_OPTION_KEY_HEIGHT, $options)) {
				$options['image']['height'] = intval($options[AM_TOOL_OPTION_KEY_HEIGHT]);
				unset($options[AM_TOOL_OPTION_KEY_HEIGHT]);
			} else {
				$options['image']['height'] = false;
			}
			
			if (array_key_exists(AM_TOOL_OPTION_KEY_CROP, $options)) {
				$options['image']['crop'] = (boolean)intval($options[AM_TOOL_OPTION_KEY_CROP]);
				unset($options[AM_TOOL_OPTION_KEY_CROP]);
			} else {
				$options['image']['crop'] = false;
			}
			
		}
		
		// Set up an empty array for all displayed variables.
		$options['vars'] = array();
		foreach($options as $key => $value) {
			if (is_int($key)) {
				$options['vars'][] = $value;
				unset($options[$key]);
			}
		}
	
		// Create a selection.
		// Filtering by tag and sorting has to be handled by listPages() since that filters should not
		// influence listFilters menu itself.
		$selection = new Selection($this->collection);	
		
		// Exclude curretn page.
		$selection->excludePage($this->P->relUrl);
		
		// Filters which influence both (listPages & listFilters) can be handled here:
		// Filter the selection optionally by the current page as parent (children_only = 1).
		if ($options[AM_TOOL_OPTION_KEY_CHILDREN_ONLY]) {
			$selection->filterByParentUrl($this->P->relUrl);
		}
		// Filter the selection optionally by a template name.
		if (isset($options[AM_TOOL_OPTION_KEY_TEMPLATE])) {
			$selection->filterByTemplate($options[AM_TOOL_OPTION_KEY_TEMPLATE]);
		}
		
		$this->listSelection = $selection;
		$this->listOptionArray = $options;
		
		Debug::log('Toolbox: List options:');
		Debug::log($options);
		Debug::log('Toolbox: List pages:');
		Debug::log($selection->getSelection());

	}


	/**
	 *	Create a page list from the given options defined in Toolbox::listOptions().
	 *
	 *	@return The HTML of the page list.
	 */

	public function listPages() {	
	
		$options = $this->listOptionArray;
		$selection = $this->listSelection;
		
		// Filter by tag.
		$selection->filterByTag($this->filter);
		
		// Sort selection.
		$selection->sortPages($this->sortType, $this->sortDirection);
		
		// Get pages.
		$pages = $selection->getSelection();
		
		return Html::generateList($pages, $options['vars'], $options['image']);	
		
	}


	/**
	 *	Create a filter menu to filter a page list created by Toolbox::listPages() regarding the options defined by Toolbox::listOptions().
	 *
	 *	@return The HTML for the filter menu.
	 */

	public function listFilters() {
		
		// Get relevant pages to determine the relevant tags.
		$pages = $this->listSelection->getSelection();
		
		if ($pages) {
		
			$tags = array();
			foreach ($pages as $page) {
				$tags = array_merge($tags, $page->tags);
			}
		
			$tags = array_unique($tags);
			sort($tags);
		
			return Html::generateFilterMenu($tags);
		
		}
		
	}
	
	
	/**
	 * 	Generate a list of pages having at least one tag in common with the current page regarding the options defined by Toolbox::listOptions().
	 *
	 *	@return html of the generated list
	 */
	
	public function listRelated() {
		
		$options = $this->listOptionArray;
		
		$pages = array();
		$tags = $this->P->tags;
		
		// Get pages
		foreach ($tags as $tag) {
			
			$selection = new Selection($this->collection);
			$selection->filterByTag($tag);			
			$pages = array_merge($pages, $selection->getSelection());
						
		}
		
		// Sort pages & remove current page from selecion
		$selection = new Selection($pages);
		$selection->excludePage($this->P->relUrl);
		$selection->sortPagesByPath();
		
		return Html::generateList($selection->getSelection(), $options['vars'], $options['image']);
				
	}

	
	/**
	 *	Place a menu to select the sort direction. The menu only affects lists of pages created by Toolbox::listPages()
	 *
	 *	@param string $optionStr (optional) - Example: $[menuSortDirection(SORT_ASC: Up, SORT_DESC: Down)] 
	 *	@return the HTML for the sort menu
	 */
	
	public function listSortDirection($optionStr) {
		
		$options = Parse::toolOptions($optionStr);
		$defaults = Parse::toolOptions(AM_TOOL_OPTIONS_SORT_DIR);
		$options = array_merge($defaults, $options);
		
		return Html::generateSortDirectionMenu($options);
		
	}
		

	/**
	 *	Place a set of sort options. The menu only affects lists of pages created by Toolbox::listPages().
	 *	If the $optionStr is missing, the default options are used.
	 *
	 *	@param string $optionStr (optional) - Example: $[menuSortType(Original, title: Title, date: Date, variablename: Title ...)]  
	 *	@return the HTML for the sort menu
	 */

	public function listSortTypes($optionStr) {
		
		$options = Parse::toolOptions($optionStr);
		$defaults = Parse::toolOptions(AM_TOOL_OPTIONS_SORT_TYPE);		
		$options = array_merge($defaults, $options);
		
		return Html::generateSortTypeMenu($options);
		
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
	 * 	Place a search field with placeholder text.
	 *
	 *	@param string $optionStr (optional) - placeholder text, just a simple string
	 *	@return the HTML of the searchfield
	 */
	
	public function searchField($optionStr) {
		
		// Don't parse $optionStr, since it can be only a string.
		if (!$optionStr) {
			$optionStr = AM_TOOL_OPTIONS_SEARCH;
		}
		
		return Html::generateSearchField(AM_PAGE_RESULTS_URL, $optionStr);
		
	}

	
	/**
	 * 	Generate a list of search results.
	 *
	 *	@param string $optionStr (optional) - Variables from the text files to be included in the output. Example: $[searchResults(title, date)]
	 *	@return the HTML for the results list
	 */
	
	public function searchResults($optionStr) {
		
		if (isset($_GET["search"])) {
			
			$vars = Parse::toolOptions($optionStr);
		
			if (empty($vars)) {
				$vars = array('title');
			}
			
			$search = $_GET["search"];
			
			$selection = new Selection($this->collection);
			$selection->filterByKeywords($search);
			$selection->sortPagesByPath();
			
			$pages = $selection->getSelection();
			
			return Html::generateList($pages, $vars);
			
		}
		
	}
		

	/**
	 * 	Return any item from the site settings file (/shared/site.txt).
	 *
	 *	@param string $optionStr - Any variable key from the site settings file (/shared/site.txt)
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
