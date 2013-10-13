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
 *	The Page class holds all properties and methods of a single page.
 *	A Page object describes an entry in the collection of all pages in the Site class.
 *	Basically the Site object consists of many Page objects.
 */


class Page {
	
	
	/**
	 * 	The $data array holds all the information stored as "key: value" in the text file.
	 *	
	 *	The key can be everything alphanumeric as long as there is a matching var set in the template files.
	 *	Out of all possible keys ther are two very special ones:
	 *
	 *	- "title": 				The title of the page - will also be used for sorting
	 *	- "tags" (or better DATA_TAGS_KEY): 	The tags (or what ever is set in the const.php) will be extracted and stored as an array in the main properties of that page 
	 *						The original string will remain in the $data array for seaching
	 */
	
	public $data = array();
	
	
	/**
	 * 	The $tags get also extracted from the text file (see $data).
	 */
	
	public $tags = array();
	
	
	/**
	 *	The relative URL of the page (PATH_INFO).
	 */
	
	public $relUrl;
	
	
	/**
	 * 	The relative path in the file system.
	 */
	
	public $relPath;
	
	
	/**
	 * 	The level in the folder tree.
	 */
	
	public $level;
	
	
	/**
	 * 	The relative URL of the parent page.
	 */
	
	public $parentRelUrl;
	
	
	/**
	 * 	The template used to render the page (just the filename of the text file without the suffix).
	 */
	
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
