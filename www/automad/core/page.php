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
 *	AUTOMAD
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');

 
/**
 *	The Page class holds all properties and methods of a single page.
 *	A Page object describes an entry in the collection of all pages in the Automad class.
 *	Basically the Automad object consists of many Page objects.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Page {
	
	
	/**
	 * 	The $data array holds all the information stored as "key: value" in the text file.
	 *	
	 *	The key can be everything alphanumeric as long as there is a matching var set in the template files.
	 *	Out of all possible keys ther are two very special ones:
	 *
	 *	- "title": 				The title of the page - will also be used for sorting
	 *	- "tags" (or better AM_KEY_TAGS): 	The tags (or what ever is set in the const.php) will be extracted and stored as an array in the main properties of that page 
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
	
	public $url;
	
	
	/**
	 * 	The relative path in the file system.
	 */
	
	public $path;
	
	
	/**
	 * 	The level in the folder tree.
	 */
	
	public $level;
	
	
	/**
	 * 	The relative URL of the parent page.
	 */
	
	public $parentUrl;
	
	
	/**
	 *	The theme used to provide the template file.
	 */
	
	public $theme;
	
	
	/**
	 * 	The template used to render the page (just the filename of the text file without the suffix).
	 */
	
	public $template;
	
	
	/**
	 *	The visibility status of a page within selections.
	 */
	
	public $hidden;
	

	/**
	 * 	Return the template of the page.
	 *
	 *	@return The full file system path of the template file.
	 */
	
	public function getTemplate() {
		
		$templatePath = AM_BASE_DIR . AM_DIR_THEMES . '/' . $this->theme . '/' . $this->template . '.php';
		
		if (file_exists($templatePath)) {
			return $templatePath;
		} else {
			exit('Template "' . $templatePath . '" not found!');
		}
	
	}
	
	
	/**
	 *	Check if page is the current page.
	 *
	 *	@return boolean
	 */
	
	public function isCurrent() {
		
		return (AM_REQUEST == $this->url);
		
	}
	
	
	/**
	 *	Check if the page URL is a part the current page's URL.
	 *
	 *	@return boolean
	 */
	
	public function isInCurrentPath() {
				
		// Test if AM_REQUEST starts with $this->url.
		// The trailing slash is very important ($this->url . '/'), since without that slash,
		// /path/to/page and /path/to/page-1 would both match a current URL like /path/to/page-1/subpage, 
		// while /path/to/page/ would not match.
		return (strpos(AM_REQUEST, $this->url . '/') === 0 && !$this->isCurrent());
		
	}
	
	
	/**
	 *	Check if page is the home page.
	 *
	 *	@return boolean
	 */
	
	public function isHome() {
		
		return ($this->url == '/');
		
	}
	
} 
 
 
?>
