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
	 *	Load Twitter's Bootstrap CSS.
	 *
	 *	@return the script tag to include the minified bootstrap CSS
	 */
	
	public function bootstrapCSS() {
		
		return '<link type="text/css" rel="stylesheet" href="/automad/lib/bootstrap/css/bootstrap.min.css" />';
		
	}
	
	
	/**
	 *	Load Twitter's Bootstrap JavaScript.
	 *
	 *	@return the script tag to include the minified bootstrap JS
	 */
	
	public function bootstrapJS() {
		
		return '<script type="text/javascript" src="/automad/lib/bootstrap/js/bootstrap.min.js"></script>';
		
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
	 * 	Return the level of the current page.
	 * 
	 * 	@return level
	 */
	 
	public function level() {
		
		return $this->P->level;
		
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
	 *	- "type: chidren | related" 	(sets the type of listing (default is all pages), "children" (only pages below the current), "related" (all pages with common tags))
	 *	- "template: name" 		(all pages matching that template)
	 *	- "sortItem: Var to sort by"	(default sort item, when there is no query string passed)
	 *	- "sortOrder: asc | desc"	(default sort order, when there is no query string passed)
	 *	
	 *	@param array $options 
	 */

	public function listSetup($options = array()) {
		
		// Default setup
		// It is important, that all keys within the $defaults array match the actual properties of the Listing object to be reused as defaults,
		// when updating an existing Listing object (below).
		$defaults = 	array(
					'type' => false,
					'template' => false,
					'sortItem' => false,
					'sortOrder' => AM_LIST_DEFAULT_SORT_ORDER
				);
	
		// If listing exists already, get defaults from current properties.
		// That means basically updating by creating a new object with taken the previous setting for all non-specified paramters.
		if (isset($this->L)) {
			$defaults = array_intersect_key((array)$this->L, $defaults);
		}
		
		// Merge defaults with options
		$options = array_merge($defaults, $options);
			
		// Create new Listing. 
		$this->L = new Listing($this->S, $options['type'], $options['template'], $options['sortItem'], $options['sortOrder']);
		
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
	 * 	Possible options are:
	 * 	- class: Wrapping class for all list items
	 * 	- variables: Variables to be displayed
	 * 	- glob:	File patter to match thumbnail image
	 * 	- width: The thumbnails' width
	 * 	- height: The thumbnails' height
	 *  	- crop: Cropping parameter for thumbnails
	 *
	 * 	@param array $options
	 *	@return The HTML for a page list.
	 */

	public function listPages($options) {
	
		$defaults = 	array(
					'variables' => AM_KEY_TITLE,
					'glob' => false,
					'width' => false,
					'height' => false,
					'crop' => false,
					'class' => false
				);
	
		$options = array_merge($defaults, $options);

		// Explode list of variables.
		$options['variables'] = explode(AM_PARSE_STR_SEPARATOR, $options['variables']);
		$options['variables'] = array_map('trim', $options['variables']);
	
		return Html::generateList($this->L->pages, $options['variables'], $options['glob'], $options['width'], $options['height'], $options['crop'], $options['class']);	
		
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
	 *	Create a menu of buttons for sorting a page list (item & order combined).
	 * 
	 * 	Example: 
	 * 	t(listSort {
	 * 		"Original": { sortOrder: "asc" },
	 * 		"Title": { sortItem: "title", sortOrder: "asc" },
	 * 		"Tags":	{ sortItem: "tags", sortOrder: "asc" }
	 * 	})
	 *	
	 * 	To have a button to sort the pages by basename, the 'sortItem' just has to be skipped or set to any non-existing variable.
	 *  
	 * 	@param array $options - A multidimensional array of buttons and their sort settings
	 * 	@return The menu's HTML
	 */
	
	public function listSort($options) {
		
		if (is_array($options) && is_array(reset($options))) {
				
			// Sanitize $options
			foreach ($options as $key => $opt) {
			
				// Remove all unneeded array items.
				$opt = array_intersect_key($opt, array_flip(array('sortItem', 'sortOrder')));
			
				// Make sure both required item (sortItem and sortOrder) are existing within $options[$key].
				$options[$key] = array_merge(array('sortItem' => '', 'sortOrder' => false), $opt);
				
				// Set sortOrder to the default order, if its value is invalid.
				if (!in_array($options[$key]['sortOrder'], array('asc', 'desc'))) {
					$options[$key]['sortOrder'] = AM_LIST_DEFAULT_SORT_ORDER;
				}
				
			}
			
			// Set list defaults.
			$this->listSetup(reset($options));
			
			return Html::generateSortMenu($options);
			
		}
	
	}
	
		
	/**
	 *	Generate a list for the navigation below a given URL.
	 *
	 *	@param array $options - (parent: the URL of the parent page of the displayed pages; homepage: add the homepage, if parent is '/' (true/false); class: wrapping class)
	 *	@return html of the generated list	
	 */
	
	public function navBelow($options) {
		
		$defaults = 	array(
					'parent' => $this->P->url, 
					'homepage' => false,
					'class' => false
				);
		
		$options = array_merge($defaults, $options);
				
		$selection = new Selection($this->collection);
		$selection->filterByParentUrl($options['parent']);
		$selection->sortPagesByBasename();
		
		$pages = $selection->getSelection();
		
		// Add Homepage to first-level navigation if parent is the homepage and the option 'homepage' is true.
		if ($options['parent'] == '/' && $options['homepage']) {
			$pages = array('/' => $this->collection['/']) + $pages;
		}
		
		return Html::generateNav($pages, $options['class']);
		
	}
	

	/**
	 * 	Generate breadcrumbs to the current page, if the page's level is > 0 (not homepage / search results / page not found).
	 *
	 * 	@param array $options - (separator: "string")
	 *	@return html of breadcrumb navigation
	 */
	
	public function navBreadcrumbs($options) {
			
		if ($this->P->level > 0) {	
				
			$options = array_merge(array('separator' => AM_HTML_STR_BREADCRUMB_SEPARATOR), $options);
				
			$selection = new Selection($this->collection);
			$selection->filterBreadcrumbs($this->P->url);
			
			return Html::generateBreadcrumbs($selection->getSelection(), $options['separator']);
			
		}
		
	}
	
		
	/**
	 *	Generate a list for the navigation below the current page.
	 *
	 * 	@param array $options - options to be passed to navBelow() (basically only 'class')
	 *	@return html of the generated list	
	 */
	
	public function navChildren($options) {
	
		// Always set 'parent' to the current page's parent URL by merging that parameter with the other specified options.
		return $this->navBelow(array_merge($options, array('parent' => $this->P->url)));
		
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
		
		return AM_DIR_THEMES . '/' . $this->P->theme;
		
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
