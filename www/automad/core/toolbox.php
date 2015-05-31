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
 * 	The Toolbox class holds all methods to be used within the template files.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Toolbox {
	

	/**
	 * 	Automad object.
	 */
	
	private $Automad;
	
	
	/**
	 *	The full collection of pages.
	 */
	
	private $collection;
	
	
	/**
	 * 	Current Page object.
	 */
	
	private $Page;
	
	
	/**
	 * 	The Automad object is passed as an argument. It shouldn't be created again (performance).
	 */
		
	public function __construct($Automad) {
		
		$this->Automad = $Automad;
		$this->collection = $this->Automad->getCollection();
		$this->Page = $this->Automad->getCurrentPage();
				
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
		
		return Html::generateFilterMenu($this->Page->tags, $this->Page->parentUrl);
		
	}
	

	/**
	 *	Place an image with an optional link.
	 *
	 *	@param array $options - (file: path/to/file (or glob pattern), width: px, height: px, crop: 1, link: url, target: _blank)
	 *	@return HTML for the image output
	 */
	
	public function img($options = array()) {
		
		// Default options
		$defaults = 	array(
					'file' => '',
					'width' => false,
					'height' => false,
					'crop' => false,
					'link' => false,
					'target' => false,
					'class' => false
				);
		
		// Merge options with defaults				
		$options = array_merge($defaults, $options);
			
		if ($options['file']) {
			
			$glob = Resolve::filePath($this->Page->path, $options['file']);
			return Html::addImage(
					$glob, 
					$options['width'], 
					$options['height'], 
					$options['crop'], 
					$options['link'], 
					$options['target'], 
					$options['class']
				);
				
		}

	}
	
	
	/**
	 *	Place a set of resized images, linking to their original sized version.
	 *	This tool returns the basic HTML for a simple image gallery.
	 *
	 * 	Possible options:
	 * 	- files: filepath/glob (multiple separated by space)
	 * 	- width: pixels 
	 * 	- height: pixels
	 * 	- crop: false
	 * 	- order: "asc", "desc" or false 
	 * 	- class: wrapping class
	 *	- firstWidth: width of first image
	 *	- firstHeight: height of firste image
	 *	- firstClass: wrapping class of first image
	 *	- enlargedWidth: width of enlarged image
	 *	- enlargedHeight: height of enlarged image
	 *
	 *	@param array $options
	 *	@return The HTML of a list of resized images with links to their bigger versions
	 */
	
	public function imgSet($options = array()) {
		
		// Default options
		$defaults = 	array(
					'files' => '*.jpg',
					'width' => false,
					'height' => false,
					'crop' => false,
					'order' => false,
					'class' => false,
					'firstWidth' => false,
					'firstHeight' => false,
					'firstClass' => false,
					'enlargedWidth' => false,
					'enlargedHeight' => false
				);
		
		// Merge options with defaults				
		$options = array_merge($defaults, $options);
				
		$files = Parse::fileDeclaration($options['files'], $this->Page);
		
		// Sort images.
		if ($options['order'] == 'asc') {
			sort($files, SORT_NATURAL);
		}
		
		if ($options['order'] == 'desc') {
			rsort($files, SORT_NATURAL);
		}	
			
		return Html::generateImageSet(
					$files, 
					$options['width'], 
					$options['height'], 
					$options['crop'], 
					$options['class'],
					$options['firstWidth'],
					$options['firstHeight'],
					$options['firstClass'],
					$options['enlargedWidth'],
					$options['enlargedHeight']
				);
		
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
		
		return $this->Page->level;
		
	}


	/**
	 *	Place a link to the previous sibling.
	 *
	 *	@param array $options - (text: Text to be displayed instead of page title (optional))
	 *	@return the HTML for the link.
	 */

	public function linkPrev($options = array()) {
		
		$Selection = new Selection($this->collection);
		$Selection->filterPrevAndNextToUrl($this->Page->url);
		
		$pages = $Selection->getSelection();
		
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
	
	public function linkNext($options = array()) {
		
		$Selection = new Selection($this->collection);
		$Selection->filterPrevAndNextToUrl($this->Page->url);
		
		$pages = $Selection->getSelection();
		
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
	 *	Change of configuration for Automad's Listing object.
	 *
	 *	Possible options are:
	 *	- type: sets the type of listing (default is all pages), "children" (only pages below the current), "related" (all pages with common tags)
	 *	- parent: optional URL of parent page, if type is set to children - default is always the current page
	 *	- template: include only pages matching that template
	 *	- sortItem: Variable to sort by - default sort item, when there is no query string passed
	 *	- sortOrder: "asc" or "desc" - default sort order, when there is no query string passed
	 *	- offset: offset the within the array of all relevant pages
	 *	- limit: limit the object's array of relevant pages
	 *	
	 *	@param array $options 
	 */

	public function listConfig($options = array()) {
			
		$Listing = $this->Automad->getListing();
		$Listing->config($options);
		
	}


	/**
	 *	Return the number of pages in the Listing object.
	 *
	 *	@return count($this->Listing->pages)
	 */
	
	public function listCount() {
		
		$Listing = $this->Automad->getListing();
		return count($Listing->getPages());
		
	}


	/**
	 *	Return a page list from Listing object.
	 * 
	 * 	Possible options are:
	 * 	- class: Wrapping class for all list items
	 * 	- variables: Variables to be displayed
	 * 	- glob:	File patter to match thumbnail image
	 * 	- width: The thumbnails' width
	 * 	- height: The thumbnails' height
	 * 	- crop: Cropping parameter for thumbnails
	 *	- maxChars: Maximum number of characters for each variable
	 *	- header: The list's header text
	 *	- style: An array of inline styles of each item, where the key is the property and the value is the name of the page variable - for example: { color: "color_var", background-color: "bg_color_var"}
	 *	- firstClass: special class for the first item of the list
	 *	- firstWidth: width for the image of the first list item
	 *	- firstHeight: height for the image of the first list item
	 *	- offset: offset within the array of filtered/sorted pages
	 *	- limit: limit the output of listed pages
	 *
	 * 	@param array $options
	 *	@return The HTML for a page list.
	 */

	public function listPages($options = array()) {
		
		$defaults = 	array(
					'variables' => AM_KEY_TITLE,
					'glob' => false,
					'width' => false,
					'height' => false,
					'crop' => false,
					'class' => false,
					'maxChars' => false,
					'header' => false,
					'style' => false,
					'firstWidth' => false,
					'firstHeight' => false,
					'firstClass' => false,
					'offset' => 0,
					'limit' => NULL
				);
	
		$options = array_merge($defaults, $options);

		// Explode list of variables.
		$options['variables'] = explode(AM_PARSE_STR_SEPARATOR, $options['variables']);
		$options['variables'] = array_map('trim', $options['variables']);
		
		$Listing = $this->Automad->getListing();
	
		return 		Html::generateList(
					$Listing->getPages($options['offset'], $options['limit']), 
					$options['variables'], 
					$options['glob'], 
					$options['width'], 
					$options['height'], 
					$options['crop'], 
					$options['class'], 
					$options['maxChars'], 
					$options['header'],
					$options['style'],
					$options['firstWidth'],
					$options['firstHeight'],
					$options['firstClass']
				);	
		
	}


	/**
	 *	Create a filter menu for the pages in Automad's Listing object.
	 *
	 *	@return The HTML for the filter menu.
	 */

	public function listFilters() {
		
		$Listing = $this->Automad->getListing();	
		return Html::generateFilterMenu($Listing->getTags());
		
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
	
	public function listSort($options = array()) {
		
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
			$this->listConfig(reset($options));
			
			return Html::generateSortMenu($options);
			
		}
	
	}
	
	
	/**
	 * 	Create the meta title tag form the site name and the page title. 
	 * 	If the 'title' option is defined, use that title instead to override the default site/page combination.
	 * 
	 * 	@param array $options ('title')
	 * 	@return The meta title tag
	 */
	
	public function metaTitle($options = array()) {
		
		$defaults = 	array(
					'title' => $this->Automad->getSiteName() . ' / ' . $this->Page->data['title']
				);
		
		$options = array_merge($defaults, $options);
				
		return '<title>' . strip_tags($options['title']) . '</title>';
					
	}
	
		
	/**
	 *	Generate a list for the navigation below a given URL.
	 *
	 *	@param array $options - (parent: the URL of the parent page of the displayed pages; homepage: add the homepage, if parent is '/' (true/false); class: wrapping class)
	 *	@return html of the generated list	
	 */
	
	public function navBelow($options = array()) {
		
		$defaults = 	array(
					'parent' => $this->Page->url, 
					'homepage' => false,
					'class' => false
				);
		
		$options = array_merge($defaults, $options);
				
		$Selection = new Selection($this->collection);
		$Selection->filterByParentUrl($options['parent']);
		$Selection->sortPagesByBasename();
		
		$pages = $Selection->getSelection();
		
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
	
	public function navBreadcrumbs($options = array()) {
			
		if ($this->Page->level > 0) {	
				
			$options = array_merge(array('separator' => AM_HTML_STR_BREADCRUMB_SEPARATOR), $options);
				
			$Selection = new Selection($this->collection);
			$Selection->filterBreadcrumbs($this->Page->url);
			
			return Html::generateBreadcrumbs($Selection->getSelection(), $options['separator']);
			
		}
		
	}
	
		
	/**
	 *	Generate a list for the navigation below the current page.
	 *
	 * 	@param array $options - options to be passed to navBelow() (basically only 'class')
	 *	@return html of the generated list	
	 */
	
	public function navChildren($options = array()) {
	
		// Always set 'parent' to the current page's parent URL by merging that parameter with the other specified options.
		return $this->navBelow(array_merge($options, array('parent' => $this->Page->url)));
		
	}
	
	
	/**
	 *	Generate a seperate navigation menu for each level within the current path.
	 *
	 *	@param array $options - (levels: The maximal level to display, homepage: show the homepage for the 1st level)
	 *	@return the HTML for the seperate navigations
	 */
	
	public function navPerLevel($options = array()) {
		
		$options = array_merge(array('levels' => false), $options);
		$maxLevel = intval($options['levels']);
		
		$Selection = new Selection($this->collection);
		$Selection->filterBreadcrumbs($this->Page->url);
		$pages = $Selection->getSelection();
		
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
	
	public function navSiblings($options = array()) {
		
		// Set parent to current parentUrl and overwrite passed options
		return $this->navBelow(array_merge($options, array('parent' => $this->Page->parentUrl)));
		
	}
	
	
	/**
	 *	Generate a list for the navigation at the top level including the home page (level 0 & 1).
	 *
	 *	@param array $options - $options['homepage'] = true/false (Show the homepage as well in the naviagtion)
	 *	@return html of the generated list	
	 */
	
	public function navTop($options = array()) {
		
		// Set parent to '/' and overwrite passed options
		return $this->navBelow(array_merge($options, array('parent' => '/')));
		
	}
	
	
	/**
	 * 	Generate full navigation tree.
	 *
	 *	@param array $options - (all: expand all pages (boolean), parent: "/parenturl", rootLevel: integer)
	 *	@return the HTML of the tree
	 */
	
	public function navTree($options = array()) {
				
		$defaults = 	array( 
					'all' => true,
					'parent' => '',
					'rootLevel' => false
				);
				
		$options = array_merge($defaults, $options);
		
		// If 'rootLevel' is not false (!==, can be 0), 
		// the tree always starts below the given level within the breadcrumb trail to the current page.
		// So, $parent gets dynamically determined in contrast to defining 'parent' within the options.
		// When 'rootLevel' is defined, the 'parent' option will be ignored.
		if ($options['rootLevel'] !== false) {
			
			$Selection = new Selection($this->collection);
			$Selection->filterBreadcrumbs($this->Page->url);
			
			foreach ($Selection->getSelection() as $breadcrumb) {
				if ($breadcrumb->level == $options['rootLevel']) {
					$parent = $breadcrumb->url;
				}
			}
				
		} else {
			// If the 'rootLevel' option is set to false, the 'parent' option will be used.
			$parent = $options['parent'];
		}
		
		// The tree only gets generated, if $parent is defined, because in case the 'rootLevel' option is 
		// defined and greater than the actual level of the current page, $parent won't be defined.
		if (isset($parent)) {	
			return Html::generateTree($parent, $options['all'], $this->collection);
		}
	
	}
	
		
	/**
	 * 	Place a search field with placeholder text.
	 *
	 *	@param array $options
	 *	@return the HTML of the searchfield
	 */
	
	public function search($options = array()) {
		
		$defaults = 	array(
					'placeholder' => 'Search', 
					'formClass' => AM_HTML_CLASS_SEARCH,
					'inputClass' => AM_HTML_CLASS_SEARCH_INPUT,
					'button' => false,
					'buttonClass' => AM_HTML_CLASS_SEARCH_BUTTON
				);
		
		$options = array_merge($defaults, $options);
		
		return Html::generateSearchField(
					AM_PAGE_RESULTS_URL, 
					$options['placeholder'], 
					$options['formClass'],
					$options['inputClass'],
					$options['button'],
					$options['buttonClass']
				);
		
	}
	
	
	/**
	 *	Return the template name used by the  current page.
	 *
	 *	@return template name
	 */
	
	public function template() {
		
		return $this->Page->template;
		
	}
	
	
	/**
	 * 	Return the URL of the page theme.
	 *
	 *	@return page theme URL
	 */
	
	public function themeURL() {
		
		return AM_DIR_THEMES . '/' . $this->Page->theme;
		
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
