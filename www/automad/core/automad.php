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
 *	The Automad class includes all methods and properties regarding the site, structure and pages.
 *	A Automad object is the "main" object. It consists of many single Page objects and holds also additional data like the site's name and theme.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Automad {
	

	/**
	 *	Automad's Listing object.
	 *	
	 *	The object is part of the Automad class to allow to access always the same instance of the Listing class for all objects using the Automad object as parameter. 
	 */
	
	private $Listing = false;
		
	
	/**
	 *	Boolean, if true, the site's structure gets scanned as well as the site's content. 
	 *	Setting that variable to false will skip all txt files and its content parsing.
	 */
	
	private $parseTxt;
	
	
	/**
	 * 	Array holding the site's settings.
	 */
	
	private $siteData = array();
	
	
	/**
	 * 	Array holding all the site's pages and the related data. 
	 *	
	 *	To access the data for a specific page, use the url as key: $this->siteCollection['url'].
	 */
	
	private $siteCollection = array();
	
	
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
		$slug = Parse::sanitize($slug);
		
		// Build URL:
		// The ltrim (/) is needed to prevent a double / in front of every url, 
		// since $parentUrl will be empty for level 0 and 1 (//path/to/page).
		// Trimming all '/' and then prependig a single '/', makes sure that there is always just one slash 
		// at the beginning of the URL. 
		// The leading slash is better to have in case of the home page where the key becomes [/] insted of just [] 
		$url = '/' . ltrim($parentUrl . '/' . $slug, '/');
	
		// check if url already exists
		if (array_key_exists($url, $this->siteCollection)) {
							
			$i = 0;
			
			$newUrl = $url;
			
			while (array_key_exists($newUrl, $this->siteCollection)) {
				$i++;
				$newUrl = $url . "-" . $i;
			}
			
			$url = $newUrl;
			
		}
		
		return $url;
		
	}
	
	
	/**
	 *	Searches $path recursively for files with the AM_FILE_EXT_DATA and adds the parsed data to $siteCollection.
	 *
	 *	After successful indexing, the $siteCollection holds basically all information (except media files) from all pages of the whole site.
	 *	This makes searching and filtering very easy since all data is stored in one place.
	 *	To access the data of a specific page within the $siteCollection array, the page's url serves as the key: $this->siteCollection['/path/to/page']
	 *
	 *	@param string $path 
	 *	@param number $level 
	 *	@param string $parentUrl
	 */
	 
	private function collectPages($path = '/', $level = 0, $parentUrl = '') {
		
		// First check, if $path contains any data files.
		// If more that one file matches the pattern, the first one will be used as the page's data file and the others will just be ignored.
		if ($files = glob(AM_BASE_DIR . AM_DIR_PAGES . $path . '*.' . AM_FILE_EXT_DATA)) {
			
			$file = reset($files);
			
			$url = $this->makeUrl($parentUrl, basename($path));
			
			$Page = new Page();
			
			// Directly set URL here as first property, 
			// to be able to overwrite that url with an optional redirect-url from the data file. 
			$Page->url = $url;
		
			// If $this->parseTxt is true (default), then all txt files get parsed as well and 
			// the corresponding properties of $Page get defined. 
			// Skipping the parsing can be useful when just the structure is needed and not the content (GUI).
			if ($this->parseTxt) {
		
				$data = Parse::markdownFile($file);
		
				// In case the title is not set in the data file or is empty, use the slug of the URL instead.
				// In case the title is missig for the home page, use the site name instead.
				if (!array_key_exists(AM_KEY_TITLE, $data) || ($data[AM_KEY_TITLE] == '')) {
					if (trim($url, '/')) {
						// If page is not the home page...
						$data[AM_KEY_TITLE] = ucwords(str_replace(array('_', '-'), ' ', basename($url)));
					} else {
						// If page is home page...
						$data[AM_KEY_TITLE] = $this->getSiteName();
					}
				} 
		
				// Extract tags
				$tags = Parse::extractTags($data);
			
				// Check for an URL in $data and use that URL instead.
				if (array_key_exists(AM_KEY_URL, $data)) {
					$Page->url = $data[AM_KEY_URL];
				}
			
				// Check for a theme in $data and use that as override for the site theme.
				if (array_key_exists(AM_KEY_THEME, $data) && $data[AM_KEY_THEME]) {
					$theme = $data[AM_KEY_THEME];
				} else {
					$theme = $this->getSiteData(AM_KEY_THEME);
				}
			
				// Check if the page should be hidden from selections.
				$hidden = false;
				if (array_key_exists(AM_KEY_HIDDEN, $data)) {
					if ($data[AM_KEY_HIDDEN] === 'true' || $data[AM_KEY_HIDDEN] === '1') {
						$hidden = true;
					}
				}
			
				// Set Page properties from txt file.
				$Page->data = $data;
				$Page->tags = $tags;
				$Page->theme = $theme;
				$Page->hidden = $hidden;
			
			}
			
			// Set all main Page properties
			$Page->path = $path;
			$Page->level = $level;
			$Page->parentUrl = $parentUrl;
			$Page->template = str_replace('.' . AM_FILE_EXT_DATA, '', basename($file));
			
			// The relative URL ($url) of the page becomes the key (in $siteCollection). 
			// That way it is impossible to create twice the same url and it is very easy to access the page's data.
			// It will actually always be the "real" Automad-URL, even if a redirect-URL is specified (that one will be stored in $Page->url instead).
			$this->siteCollection[$url] = $Page;
			
			Debug::log('      ' . $path . ' >>> ' . $Page->url);
			
			// $path gets only scanned for sub-pages, in case it contains a data file.
			// That way it is impossible to generate pages without a parent page.
			if ($dirs = glob(AM_BASE_DIR . AM_DIR_PAGES . $path . '*', GLOB_ONLYDIR)) {
				
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
	 *	Parse sitewide settings and create $siteCollection. 
	 *	If $parseTxt is false, parsing the content and the settings get skipped and only the site's structure gets determined. (Useful for GUI)
	 *
	 *	@param boolean $parseTxt
	 */
	
	public function __construct($parseTxt = true) {
		
		Debug::log('Automad: New instance created!');
		
		$this->parseTxt = $parseTxt;
		
		if ($parseTxt) {
			$this->siteData = Parse::siteData();
		}
		
		Debug::log('Automad: Scan directories for page content:');
		
		$this->collectPages();
		
	}

	
	/**
	 *	Return a key from $this->siteData (sitename, theme, etc.).
	 *
	 *	@param string $key
	 *	@return string $this->siteData[$key]
	 */
	
	public function getSiteData($key) {
		
		if (array_key_exists($key, $this->siteData)) {
			return $this->siteData[$key];
		}
			
	}
	
	
	/**
	 *	Return the name of the website - shortcut for $this->getSiteData(AM_KEY_SITENAME).
	 *
	 *	@return string $this->getSiteData(AM_KEY_SITENAME)
	 */
	
	public function getSiteName() {
		
		return $this->getSiteData(AM_KEY_SITENAME);
		
	}
	
	
	/**
	 * 	Return $siteCollection array.
	 *
	 * 	@return array $this->siteCollection
	 */
	
	public function getCollection() {
		
		return $this->siteCollection;
		
	}
		 
		 
	/**
	 * 	If existing, return the page object for the passed relative URL, else return error page.
	 * 
	 *	@param string $url
	 *	@return object $page
	 */ 

	public function getPageByUrl($url) {
		
		if (array_key_exists($url, $this->siteCollection)) {
			
			// If page exists
			return $this->siteCollection[$url];
	
		} elseif (Parse::queryKey('search') && $url == AM_PAGE_RESULTS_URL) {
	
			// If not, but it has the URL of the search results page (settings) and has a query (!).
			// An empty query for a results page doesn't make sense.
			return $this->createPage(AM_PAGE_RESULTS_TEMPLATE, AM_PAGE_RESULTS_TITLE . ' / "' . htmlspecialchars(Parse::queryKey('search')) . '"');
	
		} else {
	
			// Else return error page
			return $this->createPage(AM_PAGE_NOT_FOUND_TEMPLATE, AM_PAGE_NOT_FOUND_TITLE);
	
		}
		
	} 

	 
	/**
	 * 	Return the page object for the current page.
	 *
	 *	@return object $currentPage
	 */ 
	
	public function getCurrentPage() {
			
		// Check whether the GUI is requesting the currently edited page.
		if (AM_REQUEST == AM_PAGE_GUI && isset($_POST['url'])) {
			return $this->getPageByUrl($_POST['url']);
		} else {
			return $this->getPageByUrl(AM_REQUEST);
		}
			
	} 
	

	/**
	 *	Return Automad's instance of the Listing class and create instance when accessed for the first time.
	 *
	 *	@return Listing object
	 */
	
	public function getListing() {
		
		if (!$this->Listing) {
			$this->Listing = new Listing($this->siteCollection, $this->getCurrentPage());
		}
		
		return $this->Listing;
		
	}

	
	/**
	 * 	Create a temporary page on the fly (for example error page or search results).
	 *
	 *	@param string $template
	 *	@param string $title
	 *	@param string $parent
	 *	@return temporary page object
	 */
	
	private function createPage($template, $title) {
		
		$page = new Page();
		$page->theme = $this->getSiteData(AM_KEY_THEME);
		$page->template = $template;
		$page->data[AM_KEY_TITLE] = $title;
		$page->parentUrl = '';
		$page->level = 0;
		
		return $page;
		
	}
	
	
	/**
	 *	Tests wheter the currently requested page actually exists.
	 *
	 *	@return true if existing
	 */
	
	public function currentPageExists() {
		
		$Page = $this->getCurrentPage();
		
		return ($Page->template != AM_PAGE_NOT_FOUND_TEMPLATE);
		
	} 	 

	 
}


?>
