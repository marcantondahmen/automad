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
	 *	@return the HTML tag for the link to the given page
	 */

	private static function addLink($page) {
	
		if ($page->isHome()) {	
			$class = ' class="' . HTML_CLASS_HOME . '" ';
		} elseif ($page->isCurrent()) {	
			$class = ' class="' . HTML_CLASS_CURRENT . '" ';
		} elseif ($page->isInCurrentPath()) {
			$class = ' class="' . HTML_CLASS_CURRENT_PATH . '" ';	
		} else {
			$class = ' ';
		}
		
		return '<a' . $class . 'href="' . BASE_URL . $page->relUrl . '">' . $page->data['title'] . '</a>';
		
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
		
	public static function generateFilters($tags, $targetPage = '') {

		if ($tags) {

			// First get existing query string to prevent overwriting existing settings passed already
			// and store its data in $query.
			if (isset($_GET)) {
				$query = $_GET;
			} else {
				$query = array();
			}
		
			// Save currently passed filter query to determine current filter when generating list
			if (isset($_GET['filter'])) {
				$current = $_GET['filter'];
			} else {
				$current = '';
			}
		
			$html = '<ul class="' . HTML_CLASS_FILTER . '">';			
		
			// If there is no $tagetPage in the options, the filters will be used to filter a page list 
			// on the current page without leaving the page after selecting a tag.
			// In that case, a visitor stays on the page while using the filters and therefore needs
			// the option to "reset" the filters again to an "unfiltered" mode.
			// The "All" button gets added for that purpose.
			if (!$targetPage) {
			
				// Get page url to clear query string if needed
				if (isset($_SERVER["PATH_INFO"])) {
					$currentUrl = BASE_URL . $_SERVER["PATH_INFO"];
				} else {
					$currentUrl = BASE_URL;
				}
			
				// Check if current query is empty
				if (!$current) {
					$class = ' class="' . HTML_CLASS_CURRENT . '" ';
				} else {
					$class = ' ';
				}
			
				// Remove filter form $query, but only the filter.
				// Other items stay in that array.
				unset($query['filter']);
			
				if (http_build_query($query)) {
					// In case there are also other items in the query string, 
					// it will be passed when updating the query string to remove the filter paramter.
					$queryStr = '?' . http_build_query($query);
				} else {
					// If there are no other items, the query strings and the "?" get completly removed.
					$queryStr = '';
				}
		
				$html .= '<li><a' . $class . 'href="' . $currentUrl . $queryStr . '">' . HTML_FILTERS_ALL . '</a></li>';
			
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
	 *	@param string $varStr (variable to output in the list, comma separated)
	 *	@return the HTML of the list
	 */
	
	public static function generateList($pages, $varStr) {
		
		if ($pages) {
		
			if (!$varStr) {
				$varStr = 'title';
			}	
			
			$vars = array_map('trim', explode(',', $varStr));
		
			$html = '<ul class="' . HTML_CLASS_LIST . '">';
		
			foreach ($pages as $page) {
			
				$html .= '<li><a href="' . BASE_URL . $page->relUrl . '">';
			
				foreach ($vars as $var) {
				
					if (isset($page->data[$var])) {
						// Variable key is used to define the html class.
						// That makes styling with CSS very customizable.
						$html .= '<div class="' . $var . '">' . $page->data[$var] . '</div>';
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
	 *	@param string $varStr (placeholder text)
	 *	@return the HTML for the search field
	 */
	
	public static function generateSearchField($url, $varStr) {
		
		if (!$varStr) {
			$varStr = HTML_SEARCH_PLACEHOLDER;
		}
		
		$html = '<form class="' . HTML_CLASS_SEARCH . '" method="get" action="' . $url . '"><input type="text" name="search" placeholder="' . HTML_SEARCH_PLACEHOLDER . '" /></form>';
	
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
		
		// To generate the full tree including the homepage (if first level or not),
		// $collection['/']->parentRelUrl makes sure, that there will be always the correct
		// parent URL selected.
		// If the homepage is included in the first level it would be '/',
		// else it would be just ''. 
		return self::branch($collection['/']->parentRelUrl, $expandAll, $collection);
		
	}
	
	
}


?>
