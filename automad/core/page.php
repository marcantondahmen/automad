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
 *	The Page class includes all methodes regarding the current page.
 */


class Page {
	
	
	/**
	 * 	Site object.
	 */
	
	private $site;
	
	
	/**
	 * 	Collection of all existing pages of the website.
	 */
	
	private $collection;


	/**
	 * 	Requested URL.
	 */

	private $currentPageRelUrl;
	
	
	/**
	 * 	The current page array.
	 */
	
	private $currentPage;
	
	
	/**
	 * 	The constructor initializes a new Site object, gets the site collection (all pages) 
	 *	and gets the current page array from the requested URL ($pathInfo).
	 *
	 *	@param string $pathInfo
	 */
	
	public function __construct($pathInfo) {
		
		// trim slashes and add leading one to make sure that all possible formats get accepted like:
		// "/path/to/page" and "/path/to/page/" and also for the homepage "/".
		$this->currentPageRelUrl = '/' . trim($pathInfo, '/');
		
		// Create Site instance
		$this->site = new Site();
		
		// Get the site collection (all pages)
		$this->collection = $this->site->getCollection();
		
		// Get info of the current page (array)
		$this->currentPage = $this->collection[$this->currentPageRelUrl];
		
	}
	
	
	/**
	 * 	Renders the current page.
	 */
	
	public function render() {
		
		echo "<h1>" . $this->currentPage['data']['title'] . "</h1>";
		echo "<pre>";
		print_r ($this->currentPage);
		echo "</pre>";
		
	}
	
	
} 
 
 
?>
