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
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Site class includes all methods and properties regarding the site, structure and pages.
 *	A Site object is the "main" object. It consists of many single Page objects and holds also additional data like the site's name and theme.
 */

 
class Site {
	
	
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
	 * 	Parse Site settings to replace defaults.
	 *
	 *	Get all sitewide settings (like site name, the theme etc.) from the main settings file 
	 *	in the root of the /shared directory.
	 *	
	 *	The settings file (by default /shared/site.txt) can basically hold any key/value pair.
	 *	These variables can be later access sidewide via the Site::getSiteData() method.
	 */
	
	private function parseSiteSettings() {
		
		// Define default settings.
		// Basically that is only the site name, because that is the only really needed value.		
		$defaults = 	array(	
					'sitename' => $_SERVER['SERVER_NAME']  
				);
		
		// Merge defaults with settings from file.
		$this->siteData = array_merge($defaults, Parse::textFile(AM_FILE_SITE_SETTINGS));
		
	}
	
	
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
		
		$fullPath = AM_BASE_DIR . AM_DIR_PAGES . $path;
		
		// Test if the directory actually has a data file.		
		if (count(glob($fullPath . '/*.' . AM_FILE_EXT_DATA)) > 0) {
			
			Debug::log('      ' . $fullPath);
						
			$ignore = array('.', '..', '@eaDir');
				
			if ($dh = opendir($fullPath)) {
			
				$url = $this->makeUrl($parentUrl, basename($path));
		
				while (false !== ($item = readdir($dh))) {
		
					if (!in_array($item, $ignore)) {
					
						$itemFullPath = $fullPath . $item;
						
						// If $item is a file with the AM_FILE_EXT_DATA, $item gets added to the index.
						// In case there are more than one matching file, the last accessed gets added.
						if (is_file($itemFullPath) && strtolower(substr($item, strrpos($item, '.') + 1)) == AM_FILE_EXT_DATA) {
						
							$data = Parse::markdownFile($itemFullPath);
						
							// In case the title is not set in the data file or is empty, use the slug of the URL instead.
							// In case the title is missig for the home page, use the site name instead.
							if (!array_key_exists(AM_PARSE_TITLE_KEY, $data) || ($data[AM_PARSE_TITLE_KEY] == '')) {
								if (trim($url, '/')) {
									// If page is not the home page...
									$data[AM_PARSE_TITLE_KEY] = ucwords(str_replace(array('_', '-'), ' ', basename($url)));
								} else {
									// If page is home page...
									$data[AM_PARSE_TITLE_KEY] = $this->getSiteName();
								}
							} 
						
							// Extract tags
							$tags = Parse::extractTags($data);
							
							// Check for an URL in $data and use that URL instead.
							if (array_key_exists(AM_PARSE_URL_KEY, $data)) {
								$url = $data[AM_PARSE_URL_KEY];
							}
							
							// Check for a theme in $data and use that as override for the site theme.
							if (array_key_exists(AM_PARSE_THEME_KEY, $data)) {
								$theme = $data[AM_PARSE_THEME_KEY];
							} else {
								$theme = $this->getSiteData('theme');;
							}
						
							// The relative URL ($url) of the page becomes the key (in $siteCollection). 
							// That way it is impossible to create twice the same url and it is very easy to access the page's data. 	
							$P = new Page();
							$P->data = $data;
							$P->tags = $tags;
							$P->url = $url;
							$P->path = $path;
							$P->level = $level;
							$P->parentUrl = $parentUrl;
							$P->theme = $theme;
							$P->template = str_replace('.' . AM_FILE_EXT_DATA, '', $item);
							$this->siteCollection[$url] = $P;
							
						}
					
						// If $item is a folder, $this->collectPages gets again executed for that folder (recursively).
						if (is_dir($itemFullPath)) {
						
							$this->collectPages($path . $item . '/', $level + 1, $url);
							
						}
						
					}
			
				}
			
				closedir($dh);	
			
			}
		
		} else {
			
			Debug::log('Skip  ' . $fullPath . ' (No .' . AM_FILE_EXT_DATA . ' file found!)');
		
		}
			
	}
		
	
	/** 
	 *	Parse sitewide settings and create $siteCollection
	 */
	
	public function __construct() {
		
		Debug::log('Site: New Instance created!');
		Debug::log('Site: Scan directories for page content:');
		
		$this->parseSiteSettings();
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
	 *	Return the name of the website - shortcut for $this->getSiteData('sitename').
	 *
	 *	@return string $this->getSiteData('sitename')
	 */
	
	public function getSiteName() {
		
		return $this->getSiteData('sitename');
		
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
			return $this->createPage('results', AM_PAGE_RESULTS_TITLE . ' / "' . Parse::queryKey('search') . '"');
	
		} else {
	
			// Else return error page
			return $this->createPage('error', AM_PAGE_ERROR_TITLE);
	
		}
		
	} 

	 
	/**
	 * 	Return the page object for the current page.
	 *
	 *	@return object $currentPage
	 */ 
	
	public function getCurrentPage() {
		
		if (isset($_SERVER["PATH_INFO"])) {
			$url = '/' . trim($_SERVER["PATH_INFO"], '/');
		} else {
			$url = '/';
		}
			
		return $this->getPageByUrl($url);
		
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
		$page->theme = $this->getSiteData('theme');
		$page->template = $template;
		$page->data[AM_PARSE_TITLE_KEY] = $title;
		$page->parentUrl = '';
		
		return $page;
		
	}
		 	 
	 
}


?>
