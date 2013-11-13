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
 * 	The Html class holds all methods to generate html.
 */


class Html {
	
	
	/**
	 *	Add an image with an optional link.
	 *
	 *	The requested image can be optionally resized and cropped. 
	 *	If only a file is specified, the placed image keeps its original size and has no link.
	 *	If the image is a JPG and the description field in its EXIF data is defined, that description is used for the title attribute.
	 *
	 *	@param string $file
	 *	@param string $w
	 *	@param string $h
	 *	@param boolean $crop
	 *	@param string $link
	 *	@param string $target
	 *	@return the HTML of an img tag (optionally wrapped by the given link)
	 */
	
	public static function addImage($file, $w = false, $h = false, $crop = false, $link = '', $target = '') {
		
		if ($file) {
							
			$img = new Image($file, $w, $h, $crop);
			
			if ($target) {
				$target = ' target="' . $target . '"';
			}
			
			$html = '';
		
			if ($link) {
				$html .= '<a href="' . $link . '"' . $target . '>';
			}
			
			$html .= '<img src="' . $img->file . '" title="' . $img->description . '" width="' . $img->width . '" height="' . $img->height . '">';
			
			if ($link) {
				$html .= '</a>';
			}
			
			return $html;
		
		}
		
	}
	
	
	/**
	 *	Add link to $page and check, if $page is the current page or within the current path.
	 *
	 *	@param object $page
	 *	@param string $classes - additional classes to add to the link (separated by space as one string)
	 *	@return the HTML tag for the link to the given page
	 */

	public static function addLink($page, $classes = '') {
	
		if ($page->isHome()) {	
			$classes .= ' ' . AM_HTML_CLASS_HOME;	
		} 
		
		if ($page->isCurrent()) {	
			$classes .= ' ' . AM_HTML_CLASS_CURRENT;
		} 
		
		if ($page->isInCurrentPath() && !$page->isHome()) {
			$classes .= ' ' . AM_HTML_CLASS_CURRENT_PATH;	
		} 
		
		$classes = trim($classes);
		
		if ($classes) {
			$classes = ' class="' . $classes . '"';
		} 
				
		return '<a' . $classes . ' href="' . $page->relUrl . '">' . strip_tags($page->data['title']) . '</a>';
		
	}


	/**
	 *	Branch out recursively below a certain relative URL.
	 *
	 *	@param string $parentRelUrl
	 *	@param boolean $expandAll
	 *	@param array $collection (all pages)
	 *	@return the HTML for the branch/tree (recursive)
	 */

	private static function branch($parentRelUrl, $expandAll, $collection) {
		
		$selection = new Selection($collection);
		$selection->filterByParentUrl($parentRelUrl);
		$selection->sortPagesByPath();
		
		$pages = $selection->getSelection();
		
		if ($pages) {
				
			// Use first element in $pages to determine the current level.
			$pagesKeys = array_keys($pages);
			$level = ' level-' . $pages[array_shift($pagesKeys)]->level;
		
			$html = '<ul class="' . AM_HTML_CLASS_TREE . $level . '">';	
		
			foreach ($pages as $page) {
			
				$html .= '<li>' . self::addLink($page) . '</li>';
			
				// There would be an infinite loop if the parentRelUrl equals the relUlr.
				// That is the case if the current page is the homepage and the homepage moved to the first level. 
				if ($page->parentRelUrl != $page->relUrl) {			
					if ($expandAll || $page->isCurrent() || $page->isInCurrentPath()) {			
						$html .= self::branch($page->relUrl, $expandAll, $collection);
					}
				}
			
			}

			$html .= '</ul>';
		
			return $html;
		
		}
		
	}
	
		
	/**
	 * 	Generate the HTML for a breadcrumb navigation out of a selection of pages.
	 *	
	 *	@param array $pages
	 *	@return the HTML of the breadcrumbs
	 */
	
	public static function generateBreadcrumbs($pages) {
		
		$html = '<div class="' . AM_HTML_CLASS_BREADCRUMBS . '">';
		
		foreach ($pages as $page) {
			
			$html .= '<a href="' . $page->relUrl . '">' . strip_tags($page->data['title']) . '</a>' . AM_HTML_STR_BREADCRUMB_SEPARATOR;
			
		}
		
		// Remove last separator again
		$html = rtrim($html, AM_HTML_STR_BREADCRUMB_SEPARATOR);
		
		$html .= '</div>';
		
		return $html;
		
	}
	

	/**
	 *	Generate the HTML for filter menu out of $tags.
	 *
	 *	@param array $tags
	 *	@param string $targetPage (default is empty, stay on same page)
	 *	@return the HTML of the filter menu
	 */
		
	public static function generateFilterMenu($tags, $targetPage = '') {

		if ($tags) {

			$query = self::getQueryArray();
			$current = self::getQueryKey('filter');
		
			$html = '<ul class="' . AM_HTML_CLASS_FILTER . '">';			
		
			// If there is no $tagetPage in the options, the filters will be used to filter a page list 
			// on the current page without leaving the page after selecting a tag.
			// In that case, a visitor stays on the page while using the filters and therefore needs
			// the option to "reset" the filters again to an "unfiltered" mode.
			// The "All" button gets added for that purpose.
			if (!$targetPage) {
			
				// Check if current query is empty. 
				// No query means no filter - in that case the AM_HTML_CLASS_CURRENT gets applied to the "All" button.
				if (!$current) {
					$class = ' class="' . AM_HTML_CLASS_CURRENT . '" ';
				} else {
					$class = ' ';
				}
			
				// Only change the ['filter'] key
				$query['filter'] = '';
				
				ksort($query);
					
				$html .= '<li><a' . $class . 'href="?' . http_build_query($query) . '">' . AM_HTML_TEXT_FILTER_ALL . '</a></li>';
			
			}
		
			foreach ($tags as $tag) {
			
				// Check if $tag equals current filter in query
				if ($current == $tag) {
					$class = ' class="' . AM_HTML_CLASS_CURRENT . '" ';
				} else {
					$class = ' ';
				}
			
				// Only change the ['filter'] key
				$query['filter'] = $tag;
				
				ksort($query);
		
				$html .= '<li><a' . $class . 'href="' . $targetPage . '?' . http_build_query($query) . '">' . $tag . '</a></li>';
		
			}
		
			$html .= '</ul>';
		
			return $html;
			
		}
		
	}
		
	
	/**
	 * 	Generate the HTML for a page list out of a selection of pages and a string of variables.
	 *
	 *	The string of variables represents the variables used in the page's 
	 *	text file which should be included in the HTML of the list.
	 * 
	 *	The function is private and is not supposed to be included in a template.
	 *
	 *	@param array $pages (selected pages)
	 *	@param array $vars (variables to output in the list)
	 *	@return the HTML of the list
	 */
	
	public static function generateList($pages, $vars) {
		
		if ($pages) {			
						
			$html = '<ul class="' . AM_HTML_CLASS_LIST . '">';
		
			foreach ($pages as $page) {
			
				$html .= '<li><a href="' . $page->relUrl . '">';
			
				foreach ($vars as $var) {
				
					if (isset($page->data[$var])) {
						
						$text = strip_tags($page->data[$var]);
						
						// Shorten $text to maximal characters (full words).
						if (strlen($text) > AM_HTML_LIST_MAX_CHARS) {
							// Cut $text to max chars
							$text = substr($text, 0, AM_HTML_LIST_MAX_CHARS);
							// Find last space and get position
							$pos = strrpos($text, ' ');
							// Cut $text again at last space's position (< AM_HTML_LIST_MAX_CHARS)
							$text = substr($text, 0, $pos) . ' ...';
						}
					
						// Variable key is used to define the html class.
						// That makes styling with CSS very customizable.
						$html .= '<div class="' . $var . '">' . $text . '</div>';
						
					}
				
				}
			
				$html .= '</a></li>';
			
			}
		
			$html .= '</ul>';
		
			return $html;
			
		}
		
	}


	/**
	 * 	Generate the HTML of a navigation list for the passed pages.
	 *
	 *	Each page gets checked against the current URL. 
	 *	If the page is the current page or the page is a parent of the current page, 
	 *	additional classe will be added to the representing element.
	 *	
	 *	@param array $pages
	 *	@return the HTML of the navigation
	 */
	
	public static function generateNav($pages) {
		
		if ($pages) {
		
			$html = '<ul class="' . AM_HTML_CLASS_NAV . '">';
		
			foreach($pages as $page) {
			
				$html .= '<li>' . self::addLink($page) . '</li>'; 
			
			}
		
			$html .= '</ul>';
		
			return $html;
			
		}
		
	}
	
	
	/**
	 * 	Generate search field.
	 *	
	 *	@param string $url (URL of the results page)
	 *	@param string $placeholderText (placeholder text)
	 *	@return the HTML for the search field
	 */
	
	public static function generateSearchField($url, $placeholderText) {
		
		return '<form class="' . AM_HTML_CLASS_SEARCH . '" method="get" action="' . $url . '"><input type="text" name="search" placeholder="' . $placeholderText . '" /></form>';
			
	}

	
	/**
	 *	Generate ascending/descending buttons for sorting.
	 *
	 *	@param array $options - An array with the text for each direction: array('SORT_ASC' => 'asc', 'SORT_DESC' => 'desc')
	 *	@return the HTML for the buttons
	 */
	
	public static function generateSortDirectionMenu($options) {
		
		$query = self::getQueryArray();
		$current = self::getQueryKey('sort_dir');
				
		if (!$current) {
			$current = AM_TOOL_DEFAULT_SORT_DIR;
		}
		
		$html = '<ul class="' . AM_HTML_CLASS_SORT . '">';
		
		
		// Ascending buttom		
		if ($current == "sort_asc") {
			$class = ' class="' . AM_HTML_CLASS_CURRENT . '" ';
		} else {
			$class = ' ';
		}
		
		$query['sort_dir'] = "sort_asc";
		ksort($query);
		$html .= '<li><a' . $class . 'href="?' . http_build_query($query) . '">' . $options["SORT_ASC"] . '</a></li>';
		
		
		// Descending button
		if ($current == "sort_desc") {
			$class = ' class="' . AM_HTML_CLASS_CURRENT . '" ';
		} else {
			$class = ' ';
		}
		
		$query['sort_dir'] = "sort_desc";
		$html .= '<li><a' . $class . 'href="?' . http_build_query($query) . '">' . $options["SORT_DESC"] . '</a></li>';
		
		$html .= '</ul>';
	
		return $html;
		
	}

	
	/**
	 *	Generate the menu to select the sort type from the given types ($options).
	 *
	 *	@param array $options -	An array with the variables to "sort by", where the key is the variable and the value its description. 
	 *				An array item with a numeric key will be taken for the original order: array('Original', 'title' => 'By Title', 'tags' => 'By Tags').
	 *	@return the HTML of the menu
	 */
	
	public static function generateSortTypeMenu($options) {

		$query = self::getQueryArray();
		$current = self::getQueryKey('sort_type');
		
		// All option array items with numeric keys get merged into one item (last one kept).
		// That way the text for the 'Original Order' button can be defined with just adding a "keyless" value to the array. 
		for($i=0; isset($options[$i]); $i++){
			$options[''] = $options[$i];
			unset($options[$i]);
		}
				
		ksort($options);
		
		$html = '<ul class="' . AM_HTML_CLASS_SORT . '">';
		
		foreach ($options as $key => $value) {
							
			// Check if $value equals current filter in query
			if ($current == $key) {
				$class = ' class="' . AM_HTML_CLASS_CURRENT . '" ';
			} else {
				$class = ' ';
			}
		
			// Only change the ['sort_type'] key
			$query['sort_type'] = $key;
			
			ksort($query);
	
			$html .= '<li><a' . $class . 'href="?' . http_build_query($query) . '">' . $value . '</a></li>';
			
		}
	
		$html .= '</ul>';
	
		return $html;
	
	}
	
	
	/**
	 * 	Generate the HTML for a full site tree.
	 *
	 *	@param array $collection (all pages)
	 *	@param boolean $expandAll
	 *	@return the HTML of the tree
	 */
	
	public static function generateTree($collection, $expandAll = true) {
		
		// The tree starts on level 1. By default the homepage will not be included.
		// To include the homepage, it has to be moved to the first level by using Selection::makeHomePageFirstLevel()
		// or $[includeHome] from the templates.
		return self::branch('/', $expandAll, $collection);
		
	}
	
	
	/**
	 *	Get the query string, if existing.
	 *
	 *	@return $query
	 */
	
	private static function getQueryArray() {
		
		// First get existing query string to prevent overwriting existing settings passed already
		// and store its data in $query.
		if (isset($_GET)) {
			$query = $_GET;
		} else {
			$query = array();
		}
		
		return $query;
		
		
	}
	
	
	/**
	 *	Test if a key exists in the query string and return that key.
	 *
	 *	@param string $key
	 *	@return $queryKey
	 */
	
	private static function getQueryKey($key) {
	
		// Save currently passed filter query to determine current filter/sort_dir when generating list
		if (isset($_GET[$key])) {
			$queryKey = $_GET[$key];
		} else {
			$queryKey = '';
		}
		
		return $queryKey;
	
	}
		
	
}


?>
