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
	 *	If a matching template can't be found, the default location will be searched.
	 *	If it still can't be found, the default template will be returned.
	 *
	 *	@param string $themePath
	 *	@return $templatePath
	 */
	
	public function getTemplatePath($themePath) {
		
		// First the passed $themePath is used to get the template file.
		// That path may be already the default location, in case the theme is not set
		// or the theme's folder can't be found.	
		$templatePath = $themePath . '/' . $this->template . '.php';
			
		// If there is no matching template file in the theme folder,
		// the default template location is used, if both locations are not equal already.
		if (!file_exists($templatePath) && $themePath != BASE_DIR . '/' . TEMPLATE_DEFAULT_DIR) {
			$templatePath = BASE_DIR . '/' . TEMPLATE_DEFAULT_DIR . '/' . $this->template . '.php';			
		}
		
		// If there is also no match in the default folder,
		// the default folder in combination with the default template name is used. 
		if (!file_exists($templatePath)) {	
			$templatePath = BASE_DIR . '/' . TEMPLATE_DEFAULT_DIR . '/' . TEMPLATE_DEFAULT_NAME . '.php';			
		}
		
		return $templatePath;
		
	}
	
	
} 
 
 
?>
