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
	 *	@param array $options - (file: path/to/file (or glob pattern), width: px, height: px, crop: 1, link: url, target: _blank)
	 *	@return HTML for the image output
	 */
	
	public function img($options) {
		
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
		$options = array_merge($defaults, $options);
			
		if ($options['file']) {
			$glob = Modulate::filePath($this->P->path, $options['file']);
			return Html::addImage($glob, $options['width'], $options['height'], $options['crop'], $options['link'], $options['target']);
		}

	}
	
	
	/**
	 *	Place a set of resized images, linking to their original sized version.
	 *	This tool returns the basic HTML for a simple image gallery.
	 *
	 *	@param array $options - (glob: path/to/file (or glob pattern), width: px, height: px, crop: 1)
	 *	@return The HTML of a list of resized images with links to their bigger versions
	 */
	
	public function imgSet($options) {
		
		// Default options
		$defaults = 	array(
					'glob' => '*.jpg',
					'width' => false,
					'height' => false,
					'crop' => false
				);
		
		// Merge options with defaults				
		$options = array_merge($defaults, $options);
			
		if ($options['glob']) {
			$glob = Modulate::filePath($this->P->path, $options['glob']);
			return Html::generateImageSet($glob, $options['width'], $options['height'], $options['crop']);
		}
		
	}	
	
	
	/**
	 *	Load Jquery JS library.
	 *
	 *	@return the script tag to include Jquery
	 */
	
	public function jquery() {
		
		return '<script type="text/javascript" src="/automad/lib/jquery/jquery-2.0.3.min.js"></script>';
		
	}


	/**
	 *	Place a link to the previous sibling.
	 *
	 *	@param array $options - (text: Text to be displayed instead of page title (optional))
	 *	@return the HTML for the link.
	 */

	public function linkPrev($options) {
		
		$selection = new Selection($this->collection);
		$selection->filterPrevAndNextToUrl($this->P->url);
		
		$pages = $selection->getSelection();
		
		if (isset($options['text'])) {
			$text = $options['text'];
		} else {
			$text = false;
		}
		
		// Check if there is a previous page and return HTML
		if (isset($pages['prev'])) {
			return Html::addLink($pages['prev'], AM_HTML_CLASS_PREV, $text);
		}
		
	}
	
	
	/**
	 *	Place a link to the next sibling.
	 *
	 *	@param array $options - (text: Text to be displayed instead of page title (optional))
	 *	@return the HTML for the link.
	 */
	
	public function linkNext($options) {
		
		$selection = new Selection($this->collection);
		$selection->filterPrevAndNextToUrl($this->P->url);
		
		$pages = $selection->getSelection();
		
		if (isset($options['text'])) {
			$text = $options['text'];
		} else {
			$text = false;
		}
		
		// Check if there is a next page and return HTML
		if (isset($pages['next'])) {
			return Html::addLink($pages['next'], AM_HTML_CLASS_NEXT, $text);
		}
		
	}


	/**
	 *	Set up a list of pages. In case of $this->L (the Toolbox's Listing object) is already existing, 
	 *	its existing properties will be used as default values to be merged with the specified options.
	 *	So basically, when using that method with only a few options, the resulting Listing object is an updated version of the previous one.
	 *	That way, for example the sorting menus can update the list by changing the default sorting paramters without modifying any other option.
	 *
	 *	Possible options are:
	 *	- "vars: Vars to display"	(can be passed as string or array)
	 *	- "type: chidren | related" 	(sets the type of listing (default is all pages), "children" (only pages below the current), "related" (all pages with common tags))
	 *	- "template: name" 		(all pages matching that template)
	 *	- "glob: glob-pattern" 		(a glob pattern to match image files in a page's folder, for example "*.jpg" will output always the first JPG found in a page directory)
	 *	- "width: pixels" 		(image width, passed as interger value without unit: "width: 100")
	 *	- "height: pixels" 		(image height, passed as interger value without unit: "width: 100")
	 *	- "crop: 0 | 1"			(crop image or not)
	 *	- "sortType: Var to sort by"	(default sort type, when there is no query string passed)
	 *	- "sortDirection: asc | desc"	(default sort direction, when there is no query string passed)
	 *	
	 *	@param array $options 
	 */

	public function listSetup($options = array()) {
		
		// Default setup
		$defaults = 	array(
					'vars' => AM_KEY_TITLE,
					'type' => false,
					'template' => false,
					'glob' => false,
					'width' => false,
					'height' => false,
					'crop' => false,
					'sortType' => false,
					'sortDirection' => AM_LIST_DEFAULT_SORT_DIR
				);
	
		// If listing exists already, get defaults from current properties.
		// That means basically updating by creating a new object with taken the previous setting for all non-specified paramters.
		if (isset($this->L)) {
			$defaults = array_intersect_key((array)$this->L, $defaults);
		}
		
		// Merge defaults with options
		$options = array_merge($defaults, $options);
			
		// Explode vars in case they get passed as string (template).
		// (must be tested, since vars might be already an array, if listSetup is only called to update the listing)
		if (!is_array($options['vars'])) {
			$options['vars'] = explode(AM_PARSE_STR_SEPARATOR, $options['vars']);
			$options['vars'] = array_map('trim', $options['vars']);
		}
		
		// Create new Listing. 
		$this->L = new Listing($this->S, $options['vars'], $options['type'], $options['template'], $options['glob'], $options['width'], $options['height'], $options['crop'], $options['sortType'], $options['sortDirection']);
		
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
	 *	@param array $options - Example: {desc: "descending", asc: "ascending"} 
	 *	@return the HTML for the sort menu
	 */
	
	public function listSortDirection($options) {
		
		// Provide defaults, since the sort direction is a very simple menu and therefore most of the times specific options are unneeded.
		$defaults = array(
			'desc' => 'Descending',
			'asc' => 'Ascending'
		);
		
		// Merge defaults with options and keep order of keys.
		// Since the order of the keys is important for setting the default value,
		// it is not possible to use array_merge (would always use the default order).
		foreach (array('desc', 'asc') as $key) {
			if (!isset($options[$key])) {
				$options[$key] = $defaults[$key];
			}
		}
		
		// Only keep asc/desc in options
		$options = array_intersect_key($options, $defaults);
		
		// Update Listing with first key of options for default sorting...
		$this->listSetup(array('sortDirection' => key($options)));
		
		return Html::generateSortDirectionMenu($options);
		
	}
		

	/**
	 *	Place a set of sort options. The menu only affects lists of pages created by Toolbox::listPages().
	 *	If the $optionStr is missing, the default options are used.
	 *
	 *	@param array $options - The options array consists of variable: "display text" pairs, where the key is the variable to be sorted by and the value the text to be displayed in the menu. Passing a non-existing variable will sort the list by the basename of the path.  
	 *	@return the HTML for the sort menu
	 */

	public function listSortTypes($options) {
		
		// Update Listing with first key of options for default sorting...
		$this->listSetup(array('sortType' => key($options)));
		
		return Html::generateSortTypeMenu($options);
		
	}

	
	/**
	 *	Generate a list for the navigation below a given URL.
	 *
	 *	@param array $options - (parent: the URL of the parent page of the displayed pages; homepage: add the homepage, if parent is '/' (true/false))
	 *	@return html of the generated list	
	 */
	
	public function navBelow($options) {
		
		$options = array_merge(array('parent' => $this->P->url, 'homepage' => false), $options);
				
		$selection = new Selection($this->collection);
		$selection->filterByParentUrl($options['parent']);
		$selection->sortPagesByBasename();
		
		$pages = $selection->getSelection();
		
		// Add Homepage to first-level navigation if parent is the homepage and the option 'homepage' is true.
		if ($options['parent'] == '/' && $options['homepage']) {
			$pages = array('/' => $this->collection['/']) + $pages;
		}
		
		return Html::generateNav($pages);
		
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
	
		return $this->navBelow(array('parent' => $this->P->url));
		
	}
	
	
	/**
	 *	Generate a seperate navigation menu for each level within the current path.
	 *
	 *	@param array $options - (levels: The maximal level to display, homepage: show the homepage for the 1st level)
	 *	@return the HTML for the seperate navigations
	 */
	
	public function navPerLevel($options) {
		
		$options = array_merge(array('levels' => false), $options);
		$maxLevel = intval($options['levels']);
		
		$selection = new Selection($this->collection);
		$selection->filterBreadcrumbs($this->P->url);
		$pages = $selection->getSelection();
		
		$html = '';
		
		foreach ($pages as $page) {
			
			if (!$maxLevel || $maxLevel > $page->level) {
				
				// Pass current 'parent' along with all other original options.			
				$html .= $this->navBelow(array_merge($options, array('parent' => $page->url)));
			}
			
		}
		
		return $html;

	}
	
	
	/**
	 *	Generate a list for the navigation below the current page's parent.
	 *
	 *	@param array $options - $options['homepage'] = true/false (Show the homepage as well with the 1st level siblings)
	 *	@return html of the generated list	
	 */
	
	public function navSiblings($options) {
		
		// Set parent to current parentUrl and overwrite passed options
		return $this->navBelow(array_merge($options, array('parent' => $this->P->parentUrl)));
		
	}
	
	
	/**
	 *	Generate a list for the navigation at the top level including the home page (level 0 & 1).
	 *
	 *	@param array $options - $options['homepage'] = true/false (Show the homepage as well in the naviagtion)
	 *	@return html of the generated list	
	 */
	
	public function navTop($options) {
		
		// Set parent to '/' and overwrite passed options
		return $this->navBelow(array_merge($options, array('parent' => '/')));
		
	}
	
	
	/**
	 * 	Generate full navigation tree.
	 *
	 *	@param array $options - (all: expand all pages (boolean), parent: "/parenturl")
	 *	@return the HTML of the tree
	 */
	
	public function navTree($options) {
				
		$options = array_merge(array('parent' => '', 'all' => true), $options);
				
		return Html::generateTree($options['parent'], $options['all'], $this->collection);
	
	}
	
		
	/**
	 * 	Place a search field with placeholder text.
	 *
	 *	@param array $options - Only $options['placeholder']
	 *	@return the HTML of the searchfield
	 */
	
	public function search($options) {
		
		$options = array_merge(array('placeholder' => 'Search ...'), $options);
		
		return Html::generateSearchField(AM_PAGE_RESULTS_URL, $options['placeholder']);
		
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
