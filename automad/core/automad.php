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
use Automad\GUI\User as User; 


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Automad class includes all methods and properties regarding the site, structure and pages.
 *	A Automad object is the "main" object. It consists of many single Page objects, the Shared object and holds also additional data like the Filelist and Pagelist objects.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2013-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Automad {
	

	/**
	 * 	Automad's Context object.
	 *
	 *	The object is part of the Automad class to allow to access always the same instance of the Context class for all objects using the Automad object as parameter. 
	 */

	public $Context;
	
	
	/**
	 *	Automad's Shared object.
	 *
	 *	The Shared object is passed also to all Page objects to allow for access of global data from within a page without needing access to the full Automad object.
	 */
	
	public $Shared;
	
	
	/**
	 * 	Automad's Filelist object
	 *
	 *	The object is part of the Automad class to allow to access always the same instance of the Filelist class for all objects using the Automad object as parameter. 
	 */
	
	private $Filelist = false;


	/**
	 *	Automad's Pagelist object.
	 *	
	 *	The object is part of the Automad class to allow to access always the same instance of the Pagelist class for all objects using the Automad object as parameter. 
	 */
	
	private $Pagelist = false;
	
	
	/**
	 * 	Array holding all the site's pages and the related data. 
	 *	
	 *	To access the data for a specific page, use the url as key: $this->collection['url'].
	 */
	
	private $collection = array();
	
	
	/**
	 *	An array of existing directories within the base directory (/automad, /config, /pages etc.)
	 */
	
	private $reservedUrls;


	/**
	 *	The username of the currently logged in user or false.
	 */

	private $user;
	
	
	/**
	 *	Builds an URL out of the parent URL and the actual file system folder name.
	 *
	 *	It is important to only transform the actual folder name (slug) and not the whole path,
	 *	because of handling possible duplicate parent folder names right.
	 *	If there are for example two folders on the level above, called xxx.folder/ and yyy.folder/,
	 *	they will be transformed into folder/ and folder-1/. If the URL from yyy.folder/child/ is made from the whole path,
	 *	it will return folder/child/ instead of folder-1/child/, even if the parent URL would be folder-1/. 
	 *
	 *	The prefix for sorting (xxx.folder) will be stripped.
	 *	In case the resulting url is already in use, a suffix (-1, -2 ...) gets appende to the new url.
	 *
	 *	@param string $parentUrl
	 *	@param string $slug
	 *	@return string $url
	 */
	
	private function makeUrl($parentUrl, $slug) {
		
		// strip prefix from $slug
		$pattern = '/[a-zA-Z0-9_-]+\./';
		$replacement = '';
		$slug = preg_replace($pattern, $replacement, $slug);
		
		// Clean up $slug
		$slug = Str::sanitize($slug);
		
		// Build URL:
		// The ltrim (/) is needed to prevent a double / in front of every url, 
		// since $parentUrl will be empty for level 0 and 1 (//path/to/page).
		// Trimming all '/' and then prependig a single '/', makes sure that there is always just one slash 
		// at the beginning of the URL. 
		// The leading slash is better to have in case of the home page where the key becomes [/] insted of just [] 
		$url = '/' . ltrim($parentUrl . '/' . $slug, '/');
		
		// Merge reserved URLs with already used URLs in the collection.
		$takenUrls = array_merge($this->reservedUrls, array_keys($this->collection));
		
		// check if url already exists
		if (in_array($url, $takenUrls)) {
							
			$i = 0;
			$newUrl = $url;
			
			while (in_array($newUrl, $takenUrls)) {
				$i++;
				$newUrl = $url . "-" . $i;
			}
			
			$url = $newUrl;
			
		}
		
		return $url;
		
	}
	
	
	/**
	 *	Searches $path recursively for files with the AM_FILE_EXT_DATA and adds the parsed data to $collection.
	 *
	 *	After successful indexing, the $collection holds basically all information (except media files) from all pages of the whole site.
	 *	This makes searching and filtering very easy since all data is stored in one place.
	 *	To access the data of a specific page within the $collection array, the page's url serves as the key: $this->collection['/path/to/page']
	 *
	 *	@param string $path 
	 *	@param number $level 
	 *	@param string $parentUrl
	 */
	 
	private function collectPages($path = '/', $level = 0, $parentUrl = '') {
		
		// First check, if $path contains any data files.
		// If more that one file matches the pattern, the first one will be used as the page's data file and the others will just be ignored.
		if ($files = FileSystem::glob(AM_BASE_DIR . AM_DIR_PAGES . $path . '*.' . AM_FILE_EXT_DATA)) {
			
			$file = reset($files);
			
			// Set URL.
			$url = $this->makeUrl($parentUrl, basename($path));
			
			// Get content from text file.
			$data = Parse::textFile($file);

			// Check if page is private.			
			if (array_key_exists(AM_KEY_PRIVATE, $data)) {	
				$private = ($data[AM_KEY_PRIVATE] && $data[AM_KEY_PRIVATE] !== 'false');
			} else {
				$private = false;
			}
			
			$data[AM_KEY_PRIVATE] = $private;

			// Stop processing of page data and subdirectories if page is private and nobody is logged in.
			if (!$this->user && $private) {
				return false;
			}
			
			// In case the title is not set in the data file or is empty, use the slug of the URL instead.
			// In case the title is missig for the home page, use the site name instead.
			if (!array_key_exists(AM_KEY_TITLE, $data) || ($data[AM_KEY_TITLE] == '')) {
				if (trim($url, '/')) {
					// If page is not the home page...
					$data[AM_KEY_TITLE] = ucwords(str_replace(array('_', '-'), ' ', basename($url)));
				} else {
					// If page is home page...
					$data[AM_KEY_TITLE] = $this->Shared->get(AM_KEY_SITENAME);
				}
			} 
			
			// Check for an URL override in $data and use that URL if existing. If no URL is defined as override, add the created $url above to $data to be used as a page variable.
			if (empty($data[AM_KEY_URL])) {
				$data[AM_KEY_URL] = $url;
			}
			
			// Convert hidden value to boolean.
			if (array_key_exists(AM_KEY_HIDDEN, $data)) {	
				$data[AM_KEY_HIDDEN] = ($data[AM_KEY_HIDDEN] && $data[AM_KEY_HIDDEN] !== 'false');
			} else {
				$data[AM_KEY_HIDDEN] = false;
			}
			
			// Save original URL. 
			// In case an URL for redirects is defined in the data file, the original URL will be used to resolve relative links.
			$data[AM_KEY_ORIG_URL] = $url;
			
			// Set read-only variables.
			$data[AM_KEY_PATH] = $path;
			$data[AM_KEY_LEVEL] = $level;
			$data[AM_KEY_PARENT] = $parentUrl;
			$data[AM_KEY_TEMPLATE] = str_replace('.' . AM_FILE_EXT_DATA, '', basename($file));
						
			// The relative URL ($url) of the page becomes the key (in $collection). 
			// That way it is impossible to create twice the same url and it is very easy to access the page's data.
			// It will actually always be the "real" Automad-URL, even if a redirect-URL is specified (that one will be stored in $Page->url and $data instead).
			$this->collection[$url] = new Page($data, $this->Shared);
						
			// $path gets only scanned for sub-pages, in case it contains a data file.
			// That way it is impossible to generate pages without a parent page.
			if ($dirs = FileSystem::glob(AM_BASE_DIR . AM_DIR_PAGES . $path . '*', GLOB_ONLYDIR)) {
				
				// Sort $dirs array again to be independent from glob's default behavior in case of any inconsistency.
				sort($dirs);
				
				// Scan each directory recursively.	
				foreach ($dirs as $dir) {
					$this->collectPages($path . basename($dir) . '/', $level + 1, $url);
				}
				
			}
			
		}
			
	}
		
	
	/** 
	 *	Parse sitewide settings, create $collection and set the context to the currently requested page.
	 */
	
	public function __construct() {
		
		$this->getReservedUrls();
		$this->Shared = new Shared();
		$this->user = User::get();
		$this->collectPages();
		Debug::log(array('Shared' => $this->Shared, 'Collection' => $this->collection), 'New instance created');
		
		// Set the context initially to the requested page.
		$this->Context = new Context($this->getRequestedPage());
		
	}

	
	/**
	 * 	Define properties to be cached.
	 *
	 *	@return array $itemsToCache
	 */
	
	public function __sleep() {
		
		$itemsToCache = array('collection', 'Shared');
		Debug::log($itemsToCache, 'Preparing Automad object for serialization! Caching the following items');
		return $itemsToCache;
		
	}
	
	/**
	 * 	Set new Context after being restored from cache.
	 */
	
	public function __wakeup() {
		
		Debug::log(get_object_vars($this), 'Automad object got unserialized');
		$this->Context = new Context($this->getRequestedPage());
		
	}
	
	
	/**
	 * 	Return $collection array.
	 *
	 *	@return array $this->collection
	 */
	
	public function getCollection() {
		
		return $this->collection;
		
	}
		 
		 
	/**
	 * 	If existing, return the page object for the passed relative URL.
	 * 
	 *	@param string $url
	 *	@return object $page or NULL
	 */ 

	public function getPage($url) {
		
		if (array_key_exists($url, $this->collection)) {
			return $this->collection[$url];
		}
		
	} 


	/**
	 * 	Return the page object for the requested page.
	 *
	 *	@return object A page object or NULL
	 */ 
	
	private function getRequestedPage() {
		
		// Check whether the GUI is requesting the currently edited page.
		if (AM_REQUEST == AM_PAGE_DASHBOARD) {	
			return $this->getPage(Request::post('url'));
		} else {
			if ($Page = $this->getPage(AM_REQUEST)) {
				return $Page;
			} else {
				return $this->pageNotFound();
			}
		}
				
	} 
	

	/**
	 *	Get an array of reseverd URLs - all real directories within the base directory and the GUI URL. 
	 */

	private function getReservedUrls() {
		
		// Get all real directories.
		foreach (FileSystem::glob(AM_BASE_DIR . '/*', GLOB_ONLYDIR) as $dir) {
			$this->reservedUrls[] = '/' . basename($dir);
		}
		
		// Add the GUI URL if enabled.
		if (AM_PAGE_DASHBOARD) {
			$this->reservedUrls[] = AM_PAGE_DASHBOARD;
		}
		
		Debug::log($this->reservedUrls);
		
	}


	/**
	 *	Return Automad's instance of the Filelist class and create instance when accessed for the first time.
	 *
	 *	@return object Filelist object
	 */

	public function getFilelist() {
		
		if (!$this->Filelist) {
			$this->Filelist = new Filelist($this->Context);
		}
		
		return $this->Filelist;
		
	}


	/**
	 *	Return Automad's instance of the Pagelist class and create instance when accessed for the first time.
	 *
	 *	@return object Pagelist object
	 */
	
	public function getPagelist() {
		
		if (!$this->Pagelist) {
			$this->Pagelist = new Pagelist($this->collection, $this->Context);
		}
		
		return $this->Pagelist;
		
	}
	
	
	/**
	 *	Tests wheter the currently requested page actually exists and is not an error page.
	 *
	 *	@return boolean True if existing
	 */
	
	public function currentPageExists() {
		
		$Page = $this->Context->get();
		
		return ($Page->template != AM_PAGE_NOT_FOUND_TEMPLATE);
		
	} 	 


	/**
	 *	Load and buffer a template file and return its content as string. The Automad object gets passed as parameter to be available for all plain PHP within the included file.
	 *	This is basically the base method to load a template without parsing the Automad markup. It just gets the parsed PHP content.    
	 *	
	 *	Before returning the markup, all comments <# ... #> get stripped.
	 *
	 *	Note that even when the it is possible to use plain PHP in a template file, all that code will be parsed first when buffering, before any of the Automad markup is getting parsed.
	 *	That also means, that is not possible to make plain PHP code really interact with any of the Automad placeholder markup.
	 *
	 *	@param string $file
	 *	@return string The buffered output 
	 */

	public function loadTemplate($file) {
		
		$Automad = $this;
		
		if (is_readable($file)) {
			ob_start();
			include $file;
			$output = ob_get_contents();
			ob_end_clean();
		} else {
			$template = Str::stripStart($file, AM_BASE_DIR . AM_DIR_PACKAGES);
			$title = $this->Context->get()->get(AM_KEY_TITLE);
			$url = $this->Context->get()->get(AM_KEY_URL);
			$output = "<h1>Template $template for page $title ($url) is missing!</h1><h2>Make sure you have selected an existing template for this page!</h2>";
		}
		
		// Strip comments before return.
		return preg_replace('/(' . preg_quote(AM_DEL_COMMENT_OPEN) . '.*?' . preg_quote(AM_DEL_COMMENT_CLOSE) . ')/s', '', $output);
				
	}


	/**
	 *	Create a temporary page for a missing page and send a 404 header.
	 *      
	 *	@return object The error page
	 */
	
	private function pageNotFound() {
		
		header('HTTP/1.0 404 Not Found');
		
		if (file_exists(AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $this->Shared->get(AM_KEY_THEME) . '/' . AM_PAGE_NOT_FOUND_TEMPLATE . '.php')) {
			$data[AM_KEY_TEMPLATE] = AM_PAGE_NOT_FOUND_TEMPLATE;
			$data[AM_KEY_LEVEL] = 0;
			$data[AM_KEY_PARENT] = '';
			return new Page($data, $this->Shared);
		} else {
			exit('<h1>Page not found!</h1>');
		}
		
	}

	 
}
