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
 * 	The Html class hold all methods to generate html.
 */


class Html {
	
	
	/**
	 * 	Site object.
	 */
	
	private $S;
	
	
	/**
	 * 	Current Page object.
	 */
	
	private $P;
	
	
	/**
	 * 	The Site object is passed as an argument. It shouldn't be created again (performance).
	 */
		
	public function __construct($site) {
		
		$this->S = $site;
		$this->P = $this->S->getCurrentPage();
		
	}
	

	/**
	 * 	Generate the HTML out of a selection of pages and a string of variables.
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
	
	private function listGenerateHtml($pages, $varStr) {
		
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
	 * 	Return the HTML for a list of pages below the current page.
	 *	The variables to be included in the output are set in a comma separated parameter string ($varStr).
	 *
	 *	@param string $varStr
	 *	@return the HTML of the list
	 */
	
	public function listChildren($varStr) {
		
		$selection = new Selection($this->S->getCollection());
		$selection->filterByParentRelUrl($this->P->relUrl);
		$selection->sortByPath();
		$pages = $selection->getSelection();
		
		return $this->listGenerateHtml($pages, $varStr);
		
	}
	
	
	/**
	 * 	Return the HTML for a list of all pages excluding the current page.
	 *	The variables to be included in the output are set in a comma separated parameter string ($varStr).
	 *
	 *	@param string $varStr
	 *	@return the HTML of the list
	 */
	
	public function listAll($varStr) {
	
		$selection = new Selection($this->S->getCollection());	
		$selection->sortByPath();
		$pages = $selection->getSelection();
		
		// Remove current page from selecion
		unset($pages[$this->P->relUrl]);
	
		return $this->listGenerateHtml($pages, $varStr);	
		
	}
	
	
	/**
	 *	Generate a list for the navigation below a given URL.
	 *
	 *	@param string $parentRelUrl
	 *	@return html of the generated list	
	 */
	
	public function navBelow($parentUrl) {
		
		$html = '<ul class="' . HTML_CLASS_NAV . '">';
		
		$selection = new Selection($this->S->getCollection());
		$selection->filterByParentRelUrl($parentUrl);
		$selection->sortByPath();
		$pages = $selection->getSelection();
		
		foreach($pages as $page) {
			
			$html .= '<li><a href="' . BASE_URL . $page->relUrl . '">' . $page->data['title'] . '</a></li>'; 
			
		}
		
		$html .= '</ul>';
		
		return $html;
		
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
	
		$html = '<ul class="' . HTML_CLASS_NAV . '">';
		
		$home = $this->S->getPageByUrl('/');
		
		$html .= '<li><a href="' . BASE_URL . '">' . $home->data['title'] . '</a></li>'; 
		
		$selection = new Selection($this->S->getCollection());
		$selection->filterByParentRelUrl('/');
		$selection->sortByPath();
		$pages = $selection->getSelection();
		
		foreach($pages as $page) {
			
			$html .= '<li><a href="' . BASE_URL . $page->relUrl . '">' . $page->data['title'] . '</a></li>'; 
			
		}
		
		$html .= '</ul>';
		
		return $html;
		
	}
	
	
	/**
	 * 	Return the site name
	 *
	 *	@return site name
	 */
	
	public function siteName() {
		
		return $this->S->getSiteName();
		
	}
	
	
	/**
	 * 	Return the URL of the page theme
	 *
	 *	@return page theme URL
	 */
	
	public function themeURL() {
		
		return str_replace(BASE_DIR, BASE_URL, $this->S->getThemePath());
		
	}
	
	
}


?>
