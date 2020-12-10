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
 *	Copyright (c) 2013-2020 by Marc Anton Dahmen
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
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2013-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Page {
	
	
	/**
	 * 	The $data array holds all the information stored as "key: value" in the text file and some other system generated information (:path, :level, :template ...).
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
	 *	The Shared data object.
	 */
	
	public $Shared;
	
	
	/**
	 * 	The $tags get also extracted from the text file (see $data).
	 */
	
	public $tags = array();
	
	
	/**
	 *	Set main properties.
	 *
	 *	@param array $data
	 *	@param object $Shared
	 */
	
	public function __construct($data, $Shared) {
		
		$this->data = $data;
		$this->Shared = $Shared;
		$this->tags = $this->extractTags();
		
	}
	
	
	/**
	 *  Make basic data items accessible as page properties.
	 *      
	 *	@param string $key The property name
	 *	@return string The returned value from the data array
	 */
	
	public function __get($key) {
		
		// Map property names to the defined keys of the data array.
		$keyMap = array(
			'hidden' => AM_KEY_HIDDEN,
			'private' => AM_KEY_PRIVATE,
			'level' => AM_KEY_LEVEL,
			'origUrl' => AM_KEY_ORIG_URL,
			'parentUrl' => AM_KEY_PARENT,
			'path' =>AM_KEY_PATH,
			'template' => AM_KEY_TEMPLATE,
			'url' => AM_KEY_URL
		);
		
		if (array_key_exists($key, $keyMap)) {
			return $this->get($keyMap[$key]);
		} 
		
		// Trigger error for undefined properties.
		trigger_error('Page property "' . $key . '" not defined!', E_USER_ERROR);
		
	}
	
	
 	/**
 	 *	Extracts the tags string out of a given array and returns an array with these tags.
 	 *
 	 *	@return array $tags
 	 */
	
	private function extractTags() {
		
		$tags = array();
		
		if (isset($this->data[AM_KEY_TAGS])) {
			
			// All tags are splitted into an array
			$tags = explode(AM_PARSE_STR_SEPARATOR, $this->data[AM_KEY_TAGS]);
			// Trim & strip tags
			$tags = array_map(function($tag) {
					return trim(Str::stripTags($tag)); 
				}, $tags);
			
		}
		
		return $tags;
		
	}
	
	
	/**
	 *	Return requested data - from the page data array, from the shared data array or as generated system variable. 
	 *	
	 *	The local page data array gets used as first and the shared data array gets used as second source for the requested variable. 
	 *	That way it is possible to override a shared data value on a per page basis.
	 *	Note that not all data is stored in the data arrays. 
	 *	Some data (:mtime, :basename ...) should only be generated when requested out of performance reasons.
	 *
	 *	@param string $key
	 *	@return string The requested value
	 */
	
	public function get($key) {
		
		// Check whether the requested data is part of the data array or has to be generated.
		if (array_key_exists($key, $this->data)) {
			
			// Return value from the data array.
			return $this->data[$key];
			
		} else if (array_key_exists($key, $this->Shared->data)) {	
			
			// Return value from the Shared data array.
			return $this->Shared->data[$key];
			
		} else {
			
			// Generate system variable value or return false.
			switch ($key) {
				
				case AM_KEY_CURRENT_PAGE:
					return $this->isCurrent();
				case AM_KEY_CURRENT_PATH:
					return $this->isInCurrentPath();
				case AM_KEY_BASENAME:
					return basename($this->path);
				case AM_KEY_MTIME:
					return $this->getMtime();
				default:
					return false;
					
			}
			
		}
			
	}
	
	
	/**
	 *	Get the modification time/date of the page. 
	 *	To determine to correct mtime, the page directory mtime (to check if any files got added) and the page data file mtime will be checked and the highest value will be returned.
	 * 
	 *	@return string The max mtime (directory and data file)
	 */
	
	public function getMtime() {
		
		$path = AM_BASE_DIR . AM_DIR_PAGES . $this->path;
		$mtimes = array();
		
		foreach (array($path, $path . $this->template . '.' . AM_FILE_EXT_DATA) as $item) {
			if (file_exists($item)) {
				$mtimes[] = date ('Y-m-d H:i:s', filemtime($item));
			}
		}
		
		return max($mtimes);
		
	}
	
	
	/**
	 * 	Return the template of the page.
	 *
	 *	@return string The full file system path of the template file.
	 */
	
	public function getTemplate() {
		
		$templatePath = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $this->get(AM_KEY_THEME) . '/' . $this->template . '.php';
		
		if (file_exists($templatePath)) {

			return $templatePath;

		} else {

			// Add backwards compatibility for old theme names and removed templates.
			$templatePath = str_replace(array('/contact', '/gallery', '/profile'), '/project', $templatePath);
			$templatePath = str_replace('_2_columns', '', $templatePath);
			$templatePath = str_replace(array('/alpha', '/bravo'), '/light', $templatePath);

			return $templatePath;

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
				
		// Test if AM_REQUEST starts with or is equal to $this->url.
		// The trailing slash in strpos() is very important (URL . /), since without that slash,
		// /path/to/page and /path/to/page-1 would both match a current URL like /path/to/page-1/subpage, 
		// while /path/to/page/ would not match. 
		// Since that will also exculde the current page (it will have the trailing slash more that AM_REQUEST), it has to be testes as well if $this->url equals AM_REQUEST.
		// To always include the homepage as well, rtrim($this->url, '/') avoids a double "//" for the URL "/". 
		return (strpos(AM_REQUEST, rtrim($this->url, '/') . '/') === 0 || $this->url == AM_REQUEST);
		
	}
	
	
} 
