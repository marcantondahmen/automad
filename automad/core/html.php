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
 *	(c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
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
	
	
}


?>
