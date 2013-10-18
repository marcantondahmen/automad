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
		}
		
		$html = '<ul class="' . HTML_CLASS_FILTER . '">';
		
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
		
		$html = '<ul class="' . HTML_CLASS_NAV . '">';
		
		foreach($pages as $page) {
			
			if ($page->isCurrent()) {	
				$class = ' class="' . HTML_CLASS_CURRENT . '" ';
			} elseif ($page->isInCurrentPath()) {
				$class = ' class="' . HTML_CLASS_CURRENT_PATH . '" ';
			} else {
				$class = ' ';
			}
			
			$html .= '<li><a' . $class . 'href="' . BASE_URL . $page->relUrl . '">' . $page->data['title'] . '</a></li>'; 
			
		}
		
		$html .= '</ul>';
		
		return $html;
		
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
	 *	@param array $pages
	 *	@return the HTML of the tree
	 */
	
	public static function generateTree($pages) {
		
		// The tree starts at level one.
		$level = 1;
		
		$selection = new Selection($pages);
		$selection->makeHomePageFirstLevel();
		$selection->sortPagesByPath();
		$tree = $selection->getSelection();
		
		$html = '<ul class="' . HTML_CLASS_TREE . '">';
		
		foreach ($tree as $page) {
			
			// If the page level is deeper than the previous level,
			// a new sub-list gets started.
			if ($page->level > $level) {
				$html .= '<ul class="level' . $page->level . '">';
			} 
			
			// If the page level is smaller (higher) than the previous level, 
			// the previous sub-list gets first closed.
			if ($page->level < $level) {
				$html .= '</ul>';
			}
			
			// Check if Page is current page or parent of the current page
			if ($page->isCurrent()) {	
				$class = ' class="' . HTML_CLASS_CURRENT . '" ';
			} elseif ($page->isInCurrentPath()) {
				$class = ' class="' . HTML_CLASS_CURRENT_PATH . '" ';
			} else {
				$class = ' ';
			}
					
			$html .= '<li><a' . $class . 'href="' . BASE_URL . $page->relUrl . '">' . $page->data['title'] . '</a></li>';
			
			// The current level gets saved to be compared with the next iteration for starting or closing sub-lists
			$level = $page->level;
			
		}
		
		// Add all missing closing </ul> for open lists
		for ($x=$level; $x>=1; $x--) {
			$html .= '</ul>';
		}
		
		return $html;
		
	}
	
	
}


?>
