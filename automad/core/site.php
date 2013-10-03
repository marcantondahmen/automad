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
	 *	To access the data for a specific page, use the url as key: $this->siteIndex['url'].
	 */
	
	public $siteIndex = array();
	
	
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
		if (array_key_exists($url, $this->siteIndex)) {
							
			$i = 0;
			
			$newUrl = $url;
			
			while (array_key_exists($newUrl, $this->siteIndex)) {
				$i++;
				$newUrl = $url . "-" . $i;
			}
			
			$url = $newUrl;
			
		}
		
		return $url;
		
	}
	
	
	/**
	 *	Searches $relPath recursively for files with the DATA_FILE_EXTENSION and adds the parsed data to $siteIndex.
	 *
	 *	After successful indexing, the $siteIndex holds basically all information (except media files) from all pages of the whole site.
	 *	This makes searching and filtering very easy since all data is stored in one place.
	 *	To access the data of a specific page within the $siteIndex array, the page's url serves as the key: $this->siteIndex['/path/to/page']
	 *
	 *	@param string $relPath 
	 *	@param number $level 
	 *	@param string $parentRelUrl
	 */
	 
	private function indexPagesRecursively($relPath = '', $level = 0, $parentRelUrl = '') {
		
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
						
						// Set the title (parsed from data file) as main property, since every page needs a title.
						// In case the title is not set in the data file, use the slug of the URL instead.
						// In case the title is missig for the home page, use the site name instead.
						if (isset($data['title'])) {
							$title = $data['title'];
						} elseif ($relUrl) {
							$title = ucwords(basename($relUrl));
						} else {
							$title = $this->getSiteName();
						}
						
						// The relative $relUrl of the page becomes the key (in $siteIndex). 
						// That way it is impossible to create twice the same url and it is very easy to access the page's data. 
						$this->siteIndex[$relUrl] = array(
							"title" => $title,
							"template" => str_replace('.' . DATA_FILE_EXTENSION, '', $item),
							"level" => $level,
							"relPath" => $relPath,
							"parentRelUrl" => $parentRelUrl,
							"data" => $data
						);
						
					}
					
					// If $item is a folder, $this->indexPagesRecursively gets again executed for that folder (recursively).
					if (is_dir($itemFullPath)) {
						
						$this->indexPagesRecursively(ltrim($relPath . '/' . $item, '/'), $level + 1, $relUrl);
						
					}
						
				}
			
			}
			
			closedir($dh);	
		
		}
			
	}
		
	
	/** 
	 *	Parse sitewide settings and create siteIndex
	 */
	
	public function __construct() {
		
		$this->parseSiteSettings();
		$this->indexPagesRecursively();
		
	}

	
	/**
	 *	Return on of the main keys from $siteIndex (file, level, relPath, parentRelUrl, etc.).
	 *
	 *	@param string $key
	 *	@return string $this->siteData[$key]
	 */
	
	public function getSiteData($key) {
		
		return $this->siteData[$key];
			
	}
	 
	
	/**
	 *	Return name of the website - shortcut for $this->getSiteData('sitename').
	 *
	 *	@return string $this->getSiteData('sitename')
	 */
	
	public function getSiteName() {
		
		if ($this->getSiteData('sitename')) {
			
			return $this->getSiteData('sitename');
			
		}
		
	}
	
	
	/**
	 * 	Return $siteIndex array.
	 *
	 * 	@return array $this->siteIndex
	 */
	
	public function getSiteIndex() {
		
		return $this->siteIndex;
		
	}
	
	
	/**
	 *	Filter $siteIndex by relative url of the parent page.
	 *
	 *	@param mixed $parent
	 *	@return array $filtered
	 */
	
	public function filterSiteByParentRelUrl($parent) {
		
		$filtered = array();
		
		foreach ($this->siteIndex as $key => $page) {
			if ($page['parentRelUrl'] == $parent) {
				$filtered[$key] = $page;
			}
		}
		
		return $filtered;
		
	}
	

	/**
	 *	Filter $siteIndex by level (in the tree).
	 *
	 *	@param mixed $level
	 *	@return array $filtered
	 */
	
	public function filterSiteByLevel($level) {
		
		$filtered = array();
		
		foreach ($this->siteIndex as $key => $page) {
			if ($page['level'] == $level) {
				$filtered[$key] = $page;
			}
		}
		
		return $filtered;
		
	}
	
	
	/**
	 *	Filter $siteIndex by tag.
	 *
	 *	@param mixed $tag
	 *	@return array $filtered
	 */
	
	public function filterSiteByTag($tag) {
		
		$filtered = array();
		
		foreach ($this->siteIndex as $key => $page) {
			
			if (isset($page['data'][DATA_TAGS_KEY])) {
				if (in_array($tag, $page['data'][DATA_TAGS_KEY])) {
					$filtered[$key] = $page;
				}
			}
			
		}
		
		return $filtered;
		
	}
	 
	
	/**
	 *	Filter $siteIndex by multiple keywords (a search string).
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
		
		// loop elements in $siteIndex
		foreach ($this->siteIndex as $key => $page) {
			
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
	 *	Sorts the $siteIndex based on the file system path.
	 *
	 *	@param string $order (optional: SORT_ASC, SORT_DESC)
	 */ 
	 
	public function sortSiteByPath($order = SORT_ASC) {
		
		$arrayToSortBy = array();
		
		foreach ($this->siteIndex as $key => $value) {
			
			$arrayToSortBy[$key] = $value['relPath'];
			
		}
				
		array_multisort($arrayToSortBy, $order, $this->siteIndex);
				
	}
	 	 
	 
	/**
	 *	Sorts the $siteIndex based on the title.
	 *
	 *	@param string $order (optional: SORT_ASC, SORT_DESC)
	 */ 
	 
	public function sortSiteByTitle($order = SORT_ASC) {
		
		$arrayToSortBy = array();
		
		foreach ($this->siteIndex as $key => $value) {
			
			$arrayToSortBy[$key] = strtolower($value['title']);
			
		}
				
		array_multisort($arrayToSortBy, $order, $this->siteIndex);
				
	} 
	 
	 
}


?>
