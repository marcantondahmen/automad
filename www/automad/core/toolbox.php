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


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


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
	 *	The Listing object to be used for all list* methods.
	 */
	
	private $L;
	
	
	/**
	 * 	The Site object is passed as an argument. It shouldn't be created again (performance).
	 */
		
	public function __construct($site) {
		
		$this->S = $site;
		$this->collection = $this->S->getCollection();
		$this->P = $this->S->getCurrentPage();
		
		// Set up default Listing object
		$this->listSetup();
		
	}
	
	
	/**
	 *	Place a set of the current page's tags and link back to the parent page passing each tag as a filter.
	 *
	 *	@return the HTML of the filters
	 */

	public function filterParentByTags() {
		
		return Html::generateFilterMenu($this->P->tags, $this->P->parentUrl);
		
	}
	

	/**
	 *	Place an image with an optional link.
	 *
	 *	@param string $optionStr - (file: path/to/file (or glob pattern), width: px, height: px, crop: 1, link: url, target: _blank)
	 *	@return HTML for the image output
	 */
	
	public function img($optionStr) {
		
		// Default options
		$defaults = 	array(
					'file' => '',
					'width' => false,
					'height' => false,
					'crop' => false,
					'link' => false,
					'target' => false
				);
		
		// Merge options with defaults				
		$options = array_merge($defaults, Parse::toolOptions($optionStr));
			
		if ($options['file']) {
			$glob = Modulate::filePath($this->P->path, $options['file']);
			return Html::addImage($glob, $options['width'], $options['height'], $options['crop'], $options['link'], $options['target']);
		}

	}
	
	
	/**
	 *	Place a set of resized images, linking to their original sized version.
	 *	This tool returns the basic HTML for a simple image gallery.
	 *
	 *	@param string $optionStr - (file: path/to/file (or glob pattern), width: px, height: px, crop: 1)
	 *	@return The HTML of a list of resized images with links to their bigger versions
	 */
	
	public function imgSet($optionStr) {
		
		// Default options
		$defaults = 	array(
					'file' => '*.jpg',
					'width' => false,
					'height' => false,
					'crop' => false
				);
		
		// Merge options with defaults				
		$options = array_merge($defaults, Parse::toolOptions($optionStr));
			
		if ($options['file']) {
			$glob = Modulate::filePath($this->P->path, $options['file']);
			return Html::generateImageSet($glob, $options['width'], $options['height'], $options['crop']);
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
	 *	@param string $optionStr - optional link text (for example a simple '<')
	 *	@return the HTML for the link.
	 */

	public function linkPrev($optionStr) {
		
		$selection = new Selection($this->collection);
		$selection->filterPrevAndNextToUrl($this->P->url);
		
		$pages = $selection->getSelection();
		
		// Check if there is a previous page and return HTML
		if (isset($pages['prev'])) {
			return Html::addLink($pages['prev'], AM_HTML_CLASS_PREV, $optionStr);
		}
		
	}
	
	
	/**
	 *	Place a link to the previous sibling.
	 *
	 *	@param string $optionStr - optional link text (for example a simple '>')
	 *	@return the HTML for the link.
	 */
	
	public function linkNext($optionStr) {
		
		$selection = new Selection($this->collection);
		$selection->filterPrevAndNextToUrl($this->P->url);
		
		$pages = $selection->getSelection();
		
		// Check if there is a next page and return HTML
		if (isset($pages['next'])) {
			return Html::addLink($pages['next'], AM_HTML_CLASS_NEXT, $optionStr);
		}
		
	}


	/**
	 *	Setup a new Listing object based on a string of comma separated options and the current Site object.
	 *
	 *	An example for such a string could be ("title, subtitle, type: children, template: page, file: *.jpg, width: 200, crop: 1") 
	 *	and would create a Listing object including all pages below the current page with "page" as their template,
	 *	showing the title and subtitle of each page along with an image, cropped to 200px width.
	 *
	 *	All options in that string are optional.<br>
	 *	All options passed as a simple string (for example "title" without a ":") are interpreted as variables from the page's text file, like the title etc.
	 *	and represent a variable in the Listing's output.<br> 
	 *	So, a string like ("tilte, subtitle") will create a list where each page's title ans subtitle will show up.
	 *	
	 *	All options passed as a "Key: Value" pair are interpreted as special options to format the Listing and specify the included pages. 
	 *	Possible options are:
	 *	- "type: chidren | related" 	(sets the type of listing (default is all pages), "children" (only pages below the current), "related" (all pages with common tags))
	 *	- "template: name" 		(all pages matching that template)
	 *	- "file: glob-pattern" 		(a glob pattern to match image files in a page's folder, for example "*.jpg" will output always the first JPG found in a page directory)
	 *	- "width: pixels" 		(image width, passed as interger value without unit: "width: 100")
	 *	- "height: pixels" 		(image height, passed as interger value without unit: "width: 100")
	 *	- "crop: 0 | 1"			(crop image or not)
	 *	
	 *	@param string $optionStr 
	 */

	public function listSetup($optionStr = '') {
		
		// Default setup
		$defaults = 	array(
					'type' => 'all',
					'template' => false,
					'file' => false,
					'width' => false,
					'height' => false,
					'crop' => false,
					'vars' => array(AM_PARSE_TITLE_KEY)
				);
	
		// Merge defaults with options
		$options = array_merge($defaults, Parse::toolOptions($optionStr));
				
		// Move numeric elements into vars array
		foreach($options as $key => $value) {
			if (is_int($key)) {
				$options['vars'][$key] = $value;
				unset($options[$key]);
			}
		}
		
		// Create new Listing out of $options. 
		$this->L = new Listing($this->S, $options['vars'], $options['type'], $options['template'], $options['file'], $options['width'], $options['height'], $options['crop']);
		
	}


	/**
	 *	Return the number of pages in the Listing object.
	 *
	 *	@return count($this->L->pages)
	 */
	
	public function listCount() {
		
		return count($this->L->pages);
		
	}


	/**
	 *	Return a page list from Listing object created by Toolbox::listSetup().
	 *
	 *	@return The HTML for a page list.
	 */

	public function listPages() {
	
		$L = $this->L;
	
		return Html::generateList($L->pages, $L->vars, $L->glob, $L->width, $L->height, $L->crop);	
		
	}


	/**
	 *	Create a filter menu to filter a page list created by Toolbox::listPages() regarding the options defined by Toolbox::listOptions().
	 *
	 *	@return The HTML for the filter menu.
	 */

	public function listFilters() {
		
		return Html::generateFilterMenu($this->L->tags);
		
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
		$selection->sortPagesByBasename();
		
		return Html::generateNav($selection->getSelection());
		
	}
	

	/**
	 * 	Generate breadcrumbs to current page.
	 *
	 *	@return html of breadcrumb navigation
	 */
	
	public function navBreadcrumbs() {
			
		$selection = new Selection($this->collection);
		$selection->filterBreadcrumbs($this->P->url);
		
		return Html::generateBreadcrumbs($selection->getSelection());
		
	}
	
		
	/**
	 *	Generate a list for the navigation below the current page.
	 *
	 *	@return html of the generated list	
	 */
	
	public function navChildren() {
	
		return $this->navBelow($this->P->url);
		
	}
	
	
	/**
	 *	Generate a seperate navigation menu for each level within the current path.
	 *
	 *	@param string $optionStr (optional) - The maximal level to display.
	 *	@return the HTML for the seperate navigations
	 */
	
	public function navPerLevel($optionStr) {
		
		$maxLevel = intval(trim($optionStr));
		$level = 0;
		
		$selection = new Selection($this->collection);
		$selection->filterBreadcrumbs($this->P->url);
		$pages = $selection->getSelection();
		
		$html = '';
		
		foreach ($pages as $page) {
			
			// Since the homepage's level might be changed by $selection->makeHomePageFirstLevel(),
			// a separate counter has to be used to be independend from the page's level and to avoid problems
			// when setting $maxLevel to 1.
			// If the page's level would be used and the homepage got shifted to the first level before, 
			// navPerLevel(1) wouldn't output anything (1 > 1 = false), not even the first level. 
			if (!$maxLevel || $maxLevel > $level) {
				$html .= $this->navBelow($page->url);
			}
			
			$level++;
			
		}
		
		return $html;

	}
	
	
	/**
	 *	Generate a list for the navigation below the current page's parent.
	 *
	 *	@return html of the generated list	
	 */
	
	public function navSiblings() {
		
		return $this->navBelow($this->P->parentUrl);
		
	}
	
	
	/**
	 *	Generate a list for the navigation at the top level including the home page (level 0 & 1).
	 *
	 *	@return html of the generated list	
	 */
	
	public function navTop() {
	
		$selection = new Selection($this->collection);
		$selection->filterByParentUrl('/');
		$selection->sortPagesByBasename();
		
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
	
	public function search($optionStr) {
		
		// Don't parse $optionStr, since it can be only a string.
		if (!$optionStr) {
			$optionStr = AM_TOOL_OPTIONS_SEARCH;
		}
		
		return Html::generateSearchField(AM_PAGE_RESULTS_URL, $optionStr);
		
	}
		
	
	/**
	 * 	Return the URL of the page theme.
	 *
	 *	@return page theme URL
	 */
	
	public function themeURL() {
		
		return $this->P->getTheme();
		
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
