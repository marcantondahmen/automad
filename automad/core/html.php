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
	 *	Generate a list for the navigation below a given URL.
	 *
	 *	@param string $parentRelUrl
	 *	@return html of the generated list	
	 */
	
	public function navBelow($parentUrl) {
		
		$html = '<ul>';
		
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
	
		$html = '<ul>';
		
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
	
	
}


?>
