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
 *	The Site class includes all methods and properties regarding the whole site and its structure.
 */

 
class Site {
	
	
	/**
	 * 	Array holding the site's settings.
	 */
	
	public $siteData = array();
	
	
	/**
	 * 	Array holding all the site's pages and the related data. 
	 *	
	 *	To access the data for a specific page, use the url as key: $this->siteCollection['url'].
	 */
	
	public $siteCollection = array();
	
	
	/**
	 * 	Parse Site settings.
	 *
	 *	Get all sitewide settings (like site name, the theme etc.) from the main settings file 
	 *	in the root of the content directory.
	 */
	
	private function parseSiteSettings() {
		
		$this->siteData = Data::parseTxt(SITE_CONTENT_DIR . '/' . SITE_SETTINGS_FILE);
		
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
		
		// build url
		$url = ltrim($parentUrl . '/' . preg_replace($pattern, $replacement, $slug), '/');
	
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
		
		$fullPath = BASE . '/' . SITE_CONTENT_DIR . '/' . SITE_PAGES_DIR . '/' . $relPath;
				
		if ($dh = opendir($fullPath)) {
		
			while (false !== ($item = readdir($dh))) {
		
				if ($item != "." && $item != "..") {
					
					$itemFullPath = $fullPath . '/' . $item;
					
					$relUrl = $this->makeUrl($parentRelUrl, basename($relPath));
				
					// If $item is a file with the DATA_FILE_EXTENSION, $item gets added to the index.
					// In case there are more than one matching files, they get all added.
					if (is_file($itemFullPath) && strtolower(substr($item, strrpos($item, '.') + 1)) == DATA_FILE_EXTENSION) {
						
						$data = Data::parseTxt($itemFullPath);
						
						// In case the title is not set in the data file or is empty, use the slug of the URL instead.
						// In case the title is missig for the home page, use the site name instead.
						if (!array_key_exists('title', $data) || ($data['title'] == '')) {
							
							if ($relUrl) {
								$data['title'] = ucwords(basename($relUrl));
							} else {
								$data['title'] = $this->getSiteName();
							}
							
						} 
						
						
						
							
						// The relative $relUrl of the page becomes the key (in $siteCollection). 
						// That way it is impossible to create twice the same url and it is very easy to access the page's data. 
						$this->siteCollection[$relUrl] = array(
							"template" => str_replace('.' . DATA_FILE_EXTENSION, '', $item),
							"level" => $level,
							"relPath" => $relPath,
							"parentRelUrl" => $parentRelUrl,
							"data" => $data
						);
						
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
	 *	Return a keys from $siteData (sitename, theme, etc.).
	 *
	 *	@param string $key
	 *	@return string $this->siteData[$key]
	 */
	
	public function getSiteData($key) {
		
		return $this->siteData[$key];
			
	}
	 
	
	/**
	 *	Return the name of the website - shortcut for $this->getSiteData('sitename').
	 *
	 *	@return string $this->getSiteData('sitename')
	 */
	
	public function getSiteName() {
		
		if ($this->getSiteData('sitename')) {
			
			return $this->getSiteData('sitename');
			
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
	 *	Filter $siteCollection by relative url of the parent page.
	 *
	 *	@param mixed $parent
	 *	@return array $filtered
	 */
	
	public function filterSiteByParentRelUrl($parent) {
		
		$filtered = array();
		
		foreach ($this->siteCollection as $key => $page) {
			if ($page['parentRelUrl'] == $parent) {
				$filtered[$key] = $page;
			}
		}
		
		return $filtered;
		
	}
	

	/**
	 *	Filter $siteCollection by level (in the tree).
	 *
	 *	@param mixed $level
	 *	@return array $filtered
	 */
	
	public function filterSiteByLevel($level) {
		
		$filtered = array();
		
		foreach ($this->siteCollection as $key => $page) {
			if ($page['level'] == $level) {
				$filtered[$key] = $page;
			}
		}
		
		return $filtered;
		
	}
	
	
	/**
	 *	Filter $siteCollection by tag.
	 *
	 *	@param mixed $tag
	 *	@return array $filtered
	 */
	
	public function filterSiteByTag($tag) {
		
		$filtered = array();
		
		foreach ($this->siteCollection as $key => $page) {
			
			if (isset($page['data'][DATA_TAGS_KEY])) {
				if (in_array($tag, $page['data'][DATA_TAGS_KEY])) {
					$filtered[$key] = $page;
				}
			}
			
		}
		
		return $filtered;
		
	}
	 
	
	/**
	 *	Filter $siteCollection by multiple keywords (a search string).
	 *
	 *	@param string $str
	 *	@return array $filtered
	 */
	
	public function filterSiteByKeywords($str) {
		
		$filtered = array();
		
		$keywords = explode(' ', $str);
		
		// generate pattern
		$pattern = '/^';
		foreach ($keywords as $keyword) {
			$pattern .= '(?=.*' . $keyword . ')';
		}
		// case-insensitive and multiline
		$pattern .= '/is';
		
		// loop elements in $siteCollection
		foreach ($this->siteCollection as $key => $page) {
			
			// Build string to search in.
			// All the page's data get combined in on single string ($dataAsString), to make sure that a page gets returned, 
			// even if the keywords are distributed over different variables in $page[data]. 
			$dataAsString = '';
			
			if (isset($page['data'])) {
				
				foreach ($page['data'] as $data) {
					if (is_array($data)) {
						// in case it is an array (for example for the tags)
						$dataAsString .= implode(' ', $data) . ' ';
					} else {
						$dataAsString .= $data . ' ';	
					}
				}
				
				// search
				if (preg_match($pattern, $dataAsString) == 1) {
					$filtered[$key] = $page;
				}
				
			}
			
		}
		
		return $filtered;
		
	}
	 
	 
	/**
	 *	Sorts the $siteCollection based on the file system path.
	 *
	 *	@param string $order (optional: SORT_ASC, SORT_DESC)
	 */ 
	 
	public function sortSiteByPath($order = SORT_ASC) {
		
		$arrayToSortBy = array();
		
		foreach ($this->siteCollection as $key => $value) {
			
			$arrayToSortBy[$key] = $value['relPath'];
			
		}
				
		array_multisort($arrayToSortBy, $order, $this->siteCollection);
				
	}
	 	 
	 
	/**
	 *	Sorts the $siteCollection based on the title.
	 *
	 *	@param string $order (optional: SORT_ASC, SORT_DESC)
	 */ 
	 
	public function sortSiteByTitle($order = SORT_ASC) {
		
		$arrayToSortBy = array();
		
		foreach ($this->siteCollection as $key => $value) {
			
			$arrayToSortBy[$key] = strtolower($value['data']['title']);
			
		}
				
		array_multisort($arrayToSortBy, $order, $this->siteCollection);
				
	} 
	 
	 
}


?>
