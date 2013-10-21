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
 * 	The Html class holds all methods to generate html.
 */


class Html {
	
	
	/**
	 *	Add link to $page and check, if $page is the current page or within the current path.
	 *
	 *	@param object $page
	 *	@param string $classes - additional classes to add to the link (separated by space as one string)
	 *	@return the HTML tag for the link to the given page
	 */

	public static function addLink($page, $classes = '') {
	
		if ($page->isHome()) {	
			$classes .= ' ' . HTML_CLASS_HOME;	
		} 
		
		if ($page->isCurrent()) {	
			$classes .= ' ' . HTML_CLASS_CURRENT;
		} 
		
		if ($page->isInCurrentPath() && !$page->isHome()) {
			$classes .= ' ' . HTML_CLASS_CURRENT_PATH;	
		} 
		
		$classes = trim($classes);
		
		if ($classes) {
			$classes = ' class="' . $classes . '"';
		} 
				
		return '<a' . $classes . ' href="' . BASE_URL . $page->relUrl . '">' . $page->data['title'] . '</a>';
		
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
			$level = ' level-' . $pages[array_shift(array_keys($pages))]->level;
		
			$html = '<ul class="' . HTML_CLASS_TREE . $level . '">';	
		
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
		
		$html = '<div class="' . HTML_CLASS_BREADCRUMBS . '">';
		
		foreach ($pages as $page) {
			
			$html .= '<a href="' . BASE_URL . $page->relUrl . '">' . $page->data['title'] . '</a>' . HTML_BREADCRUMB_SEPARATOR;
			
		}
		
		// Remove last separator again
		$html = rtrim($html, HTML_BREADCRUMB_SEPARATOR);
		
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
		
			$html = '<ul class="' . HTML_CLASS_FILTER . '">';			
		
			// If there is no $tagetPage in the options, the filters will be used to filter a page list 
			// on the current page without leaving the page after selecting a tag.
			// In that case, a visitor stays on the page while using the filters and therefore needs
			// the option to "reset" the filters again to an "unfiltered" mode.
			// The "All" button gets added for that purpose.
			if (!$targetPage) {
			
				// Check if current query is empty. 
				// No query means no filter - in that case the HTML_CLASS_CURRENT gets applied to the "All" button.
				if (!$current) {
					$class = ' class="' . HTML_CLASS_CURRENT . '" ';
				} else {
					$class = ' ';
				}
			
				// Only change the ['filter'] key
				$query['filter'] = '';
					
				$html .= '<li><a' . $class . 'href="?' . http_build_query($query) . '">' . HTML_FILTERS_ALL . '</a></li>';
			
			}
		
			foreach ($tags as $tag) {
			
				// Check if $tag equals current filter in query
				if ($current == $tag) {
					$class = ' class="' . HTML_CLASS_CURRENT . '" ';
				} else {
					$class = ' ';
				}
			
				// Only change the ['filter'] key
				$query['filter'] = $tag;
		
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
	 *	@param string $optionStr (variable to output in the list, comma separated)
	 *	@return the HTML of the list
	 */
	
	public static function generateList($pages, $optionStr) {
		
		if ($pages) {
		
			if (!$optionStr) {
				$optionStr = 'title';
			}	
					
			$vars = Parse::toolOptions($optionStr);
			
			$html = '<ul class="' . HTML_CLASS_LIST . '">';
		
			foreach ($pages as $page) {
			
				$html .= '<li><a href="' . BASE_URL . $page->relUrl . '">';
			
				foreach ($vars as $var) {
				
					if (isset($page->data[$var])) {
						
						$text = $page->data[$var];
						
						// Shorten $text to maximal HTML_MAX_LIST_STR_LENGTH characters (full words).
						if (strlen($text) > HTML_LIST_MAX_STR_LENGTH) {
							// Cut $text to max chars
							$text = substr($text, 0, HTML_LIST_MAX_STR_LENGTH);
							// Find last space and get position
							$pos = strrpos($text, ' ');
							// Cut $text again at last space's position (< HTML_LIST_MAX_STR_LENGTH)
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
		
			$html = '<ul class="' . HTML_CLASS_NAV . '">';
		
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
	 *	@param string $url (absolute URL of the results page)
	 *	@param string $optionStr (placeholder text)
	 *	@return the HTML for the search field
	 */
	
	public static function generateSearchField($url, $optionStr) {
		
		// Don't parse $optionStr, since it can be only a string.
		if (!$optionStr) {
			$optionStr = HTML_SEARCH_PLACEHOLDER;
		}
		
		$html = '<form class="' . HTML_CLASS_SEARCH . '" method="get" action="' . $url . '"><input type="text" name="search" placeholder="' . $optionStr . '" /></form>';
	
		return $html;
			
	}

	
	/**
	 *	Generate ascending/descending buttons for sorting.
	 *
	 *	@param string $optionStr
	 *	@return the HTML for the buttons
	 */
	
	public static function generateSortDirectionMenu($optionStr) {
		
		$query = self::getQueryArray();
		$current = self::getQueryKey('sort_dir');
				
		if (!$current) {
			$current = HTML_DEFAULT_SORT_DIR;
		}
		
		$options = Parse::toolOptions($optionStr);
		
		$defaults["SORT_ASC"] = HTML_SORT_ASC;
		$defaults["SORT_DESC"] = HTML_SORT_DESC;
		
		$options = array_merge($defaults, $options);
		
		$html = '<ul class="' . HTML_CLASS_SORT . '">';
		
		// Ascending buttom		
		if ($current == "sort_asc") {
			$class = ' class="' . HTML_CLASS_CURRENT . '" ';
		} else {
			$class = ' ';
		}
		
		$query['sort_dir'] = "sort_asc";
		$html .= '<li><a' . $class . 'href="?' . http_build_query($query) . '">' . $options["SORT_ASC"] . '</a></li>';
		
		// Descending button
		if ($current == "sort_desc") {
			$class = ' class="' . HTML_CLASS_CURRENT . '" ';
		} else {
			$class = ' ';
		}
		
		$query['sort_dir'] = "sort_desc";
		$html .= '<li><a' . $class . 'href="?' . http_build_query($query) . '">' . $options["SORT_DESC"] . '</a></li>';
		
		$html .= '</ul>';
	
		return $html;
		
	}

	
	/**
	 *	Generate the menu to select the sort type from the given types ($optionStr).
	 *
	 *	@param string $optionStr
	 *	@return the HTML of the menu
	 */
	
	public static function generateSortTypeMenu($optionStr) {

		$query = self::getQueryArray();
		$current = self::getQueryKey('sort_type');		
		$options = Parse::toolOptions($optionStr);
		$defaults = Parse::toolOptions(HTML_DEFAULT_SORT_TYPES);		
		$options = array_merge($defaults, $options);
		
		for($i=0; isset($options[$i]); $i++){
			$options[''] = $options[$i];
			unset($options[$i]);
		}
		
		ksort($options);
		
		$html = '<ul class="' . HTML_CLASS_SORT . '">';
		
		foreach ($options as $key => $value) {
							
			// Check if $value equals current filter in query
			if ($current == $key) {
				$class = ' class="' . HTML_CLASS_CURRENT . '" ';
			} else {
				$class = ' ';
			}
		
			// Only change the ['sort_type'] key
			$query['sort_type'] = $key;
	
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
	
		// Save currently passed filter query to determine current filter when generating list
		if (isset($_GET[$key])) {
			$queryKey = $_GET[$key];
		} else {
			$queryKey = '';
		}
		
		return $queryKey;
	
	}
	
	
}


?>
