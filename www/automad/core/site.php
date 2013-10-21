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
	 *	in the root of the content directory.
	 */
	
	private function parseSiteSettings() {
		
		// Load defaults		
		$defaults = 	array(	
					'sitename' => SITE_DEFAULT_NAME, 
					'theme' => SITE_DEFAULT_THEME, 
					'errorPageTitle' => SITE_ERROR_PAGE_TITLE, 
					'resultsPageTitle' => SITE_RESULTS_PAGE_TITLE,
					'resultsPageUrl' => SITE_RESULTS_PAGE_URL
				);
		
		// Merge defaults with settings from file
		$this->siteData = array_merge($defaults, Parse::textFile(SITE_SETTINGS_FILE));
		
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
		
		// strip prefix regex replace pattern
		$pattern = '/[a-zA-Z0-9_-]+\./';
		$replacement = '';
		
		// Build URL:
		// The ltrim (/) is needed to prevent a double / in front of every url, 
		// since $parentUrl will be empty for level 0 and 1 (//path/to/page).
		// Trimming all '/' and then prependig a single '/', makes sure that there is always just one slash 
		// at the beginning of the URL. 
		// The leading slash is better to have in case of the home page where the key becomes [/] insted of just [] 
		$url = '/' . ltrim($parentUrl . '/' . preg_replace($pattern, $replacement, $slug), '/');
	
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
	 *	Searches $relPath recursively for files with the DATA_FILE_EXTENSION and adds the parsed data to $siteCollection.
	 *
	 *	After successful indexing, the $siteCollection holds basically all information (except media files) from all pages of the whole site.
	 *	This makes searching and filtering very easy since all data is stored in one place.
	 *	To access the data of a specific page within the $siteCollection array, the page's url serves as the key: $this->siteCollection['/path/to/page']
	 *
	 *	@param string $relPath 
	 *	@param number $level 
	 *	@param string $parentRelUrl
	 */
	 
	private function collectPages($relPath = '', $level = 0, $parentRelUrl = '') {
		
		$fullPath = rtrim(SITE_PAGES_DIR . '/' . $relPath, '/');
				
		$ignore = array('.', '..', '@eaDir');
				
		if ($dh = opendir($fullPath)) {
			
			$relUrl = $this->makeUrl($parentRelUrl, basename($relPath));
		
			while (false !== ($item = readdir($dh))) {
		
				if (!in_array($item, $ignore)) {
					
					$itemFullPath = $fullPath . '/' . $item;
									
					// If $item is a file with the DATA_FILE_EXTENSION, $item gets added to the index.
					// In case there are more than one matching file, the last accessed gets added.
					if (is_file($itemFullPath) && strtolower(substr($item, strrpos($item, '.') + 1)) == DATA_FILE_EXTENSION) {
						
						$data = Parse::textFile($itemFullPath);
						
						// In case the title is not set in the data file or is empty, use the slug of the URL instead.
						// In case the title is missig for the home page, use the site name instead.
						if (!array_key_exists('title', $data) || ($data['title'] == '')) {
							if ($relUrl) {
								$data['title'] = ucwords(basename($relUrl));
							} else {
								$data['title'] = $this->getSiteName();
							}
						} 
						
						// Extract tags
						$tags = Parse::extractTags($data);
						
						// The relative URL ($relUrl) of the page becomes the key (in $siteCollection). 
						// That way it is impossible to create twice the same url and it is very easy to access the page's data. 	
						$P = new Page();
						$P->data = $data;
						$P->tags = $tags;
						$P->relUrl = $relUrl;
						$P->relPath = $relPath;
						$P->level = $level;
						$P->parentRelUrl = $parentRelUrl;
						$P->template = str_replace('.' . DATA_FILE_EXTENSION, '', $item);
						$this->siteCollection[$relUrl] = $P;
							
					}
					
					// If $item is a folder, $this->collectPages gets again executed for that folder (recursively).
					if (is_dir($itemFullPath)) {
						
						$this->collectPages(ltrim($relPath . '/' . $item, '/'), $level + 1, $relUrl);
						
					}
						
				}
			
			}
			
			closedir($dh);	
		
		}
			
	}
		
	
	/** 
	 *	Parse sitewide settings and create $siteCollection
	 */
	
	public function __construct() {
		
		$this->parseSiteSettings();
		$this->collectPages();
		
	}

	
	/**
	 *	Return a key from $siteData (sitename, theme, etc.).
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
	 * 	Return the path to the theme for the website.
	 *
	 *	If the theme is not defined or not existing in the file system, 
	 *	the default template location will be returned instead.
	 *
	 *	@return theme path
	 */
	
	public function getThemePath() {
		
		$theme = $this->getSiteData('theme');
		$themePath = SITE_THEMES_DIR . '/' . $theme;

		if ($theme && is_dir($themePath)) {	
			// If theme is defined (and not '' in the constants) and exists in the file system as a folder, use that path.		
			return $themePath;
		} else {
			// If not, use the default template location.
			return TEMPLATE_DEFAULT_DIR;
		}
		
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
	
		} elseif (isset($_GET["search"]) && $url == $this->getSiteData('resultsPageUrl')) {
	
			// If not, but it has the URL of the search results page (settings) and has a query (!).
			// An empty query for a results page doesn't make sense.
			return $this->createPage('results', $this->getSiteData('resultsPageTitle') . ' / "' . $_GET["search"] . '"');
	
		} else {
	
			// Else return error page
			return $this->createPage('error', $this->getSiteData('errorPageTitle'));
	
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
		$page->template = $template;
		$page->data['title'] = $title;
		$page->parentRelUrl = '';
		
		return $page;
		
	}
		 	 
	 
}


?>
