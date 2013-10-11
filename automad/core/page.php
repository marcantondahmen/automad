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
 *	The Page class holds all properties and methods of a page.
 */


class Page {
	
	
	public $data;
	public $tags;
	public $relUrl;
	public $relPath;
	public $level;
	public $parentRelUrl;
	public $template;
	
	
	/**
	 * 	Return the template of the page.
	 *
	 *	@param string $theme
	 *	@return string $templatePath
	 */
	
	public function getTemplatePath($theme) {
		
		$templatePath = BASE . '/' . SITE_THEMES_DIR . '/' . $theme . '/' . $this->template . '.php';
		
		if (!file_exists($templatePath)) {			
			$templatePath = BASE . '/' . SITE_THEMES_DIR . '/' . SITE_DEFAULT_THEME . '/' . PAGE_DEFAULT_TEMPLATE . '.php'; 	
		} 
		
		return $templatePath;
		
	}
	
} 
 
 
?>
