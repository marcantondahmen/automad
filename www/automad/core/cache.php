<?php defined('AUTOMAD') or die('Direct access not permitted!');
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
 *	The Cache class holds all methods for evaluating, reading and writing the HTML output from/to AM_DIR_CACHE.
 *
 *	First a virtual file name of a possibly existing cached version of the visited page gets determined from the PATH_INFO, the QUERY_STRING and the SERVER_NAME.
 *	To keep the whole site portable, the SERVER_NAME within the path is very important, to make sure, that all links/URLs are relative to the correct root directory.
 *	("sub.domain.com/" and "www.domain.com/sub" will return different root relative URLs > "/" and "/sub", but may host the same site > each will get its own /cache/directory)
 *
 *	In a second step, the existance of that file gets verified.
 *
 *	Third, the mtime of the site (last modified page mtime) gets determined and compared with the cache file's mtime. (That process gets limited by a certain delay)
 *	If the cache file mtime is smaller than the latest mtime (site), false gets returned.
 *
 *	To determine the latest changed page from all the existing pages, all directories under /pages and all *.txt files get collected in an array.
 *	The mtime for each item in that array gets stored in a new array ($mTimes[$item]). After sorting, all keys are stored in $mTimesKeys.
 *	The last modified item is then = end($mTimesKeys), and its mtime is $mTimes[$lastItem].
 *	Compared to using max() on the $mTime array, this method is a bit more complicated, but also determines, which of the items was last edited and not only its mtime.
 *	(That gives a bit more control for debugging)
 */


class Cache {
	
	
	/**
	 *	The determined matching file of the cached version of the currently visited page.
	 */
	
	private $pageCacheFile;
	
	
	/**
	 *	The constructor just determines $pageCacheFile to make it available within the instance.
	 */
	
	public function __construct() {
		
		$this->pageCacheFile = $this->getPageCacheFilePath();
		
	}
	

	/**
	 *	Verify if the cached version of the visited page is existing and still up to date.
	 *
	 *	@return boolean - true, if the cached version is valid.
	 */

	public function pageCacheIsApproved() {
		
		if (AM_CACHE_ENABLED) {
	
			if (file_exists($this->pageCacheFile)) {
		
				if ((@filemtime(AM_FILE_SITE_MTIME) + AM_CACHE_MONITOR_DELAY) < time()) {

					// The modification times get only checked every AM_CACHE_MONITOR_DELAY seconds, since
					// the process of collecting all mtimes itself takes some time too.
					// After scanning, the mTime gets written to a file.
					$siteMTime = $this->getSiteMTime();
					file_put_contents(AM_FILE_SITE_MTIME, serialize($siteMTime));			
					Debug::log('Cache: Scanned all pages and saved Site-mTime: ' . date('d. M Y, H:i:s', $siteMTime));
					
				} else {
					
					// In between it just gets loaded from a file.
					$siteMTime = unserialize(file_get_contents(AM_FILE_SITE_MTIME));
					Debug::log('Cache: Load Site-mTime from file: ' . date('d. M Y, H:i:s', $siteMTime));
			
				}
			
				$cacheMTime = filemtime($this->pageCacheFile);
			
				if ($cacheMTime < $siteMTime) {
					
					// If the cached page is older than the site's mTime,
					// the cache gets no approval.
					Debug::log('Cache: Cached version is deprecated! Cache-mTime: ' . date('d. M Y, H:i:s', $cacheMTime));
					return false;
					
				} else {
					
					// If the cached page is newer, it gets approved.
					Debug::log('Cache: Cached version got approved! Cache-mTime: ' . date('d. M Y, H:i:s', $cacheMTime));
					return true;
					
				}
	
			} else {
		
				Debug::log('Cache: Cached file does not exist!');
				return false;
		
			}
	
		} else {
			
			Debug::log('Cache: Caching is disabled!');
			return false;
			
		}
		
	}


	/**
	 *	Determine the corresponding file in the cache for the visited page in consideration of a possible query string.
	 *	A page gets for each possible query string (to handle sort/filter) an unique cache file.
	 *
	 *	@return The determined file name of the matching cached version of the visited page.
	 */
	
	private function getPageCacheFilePath() {
	
		if (isset($_SERVER['PATH_INFO'])) {
			$currentPath = '/' . trim($_SERVER['PATH_INFO'], '/');
		} else {
			$currentPath = '';
		}
		
		if ($_SERVER['QUERY_STRING']) {
			$queryString = '_' . Parse::sanitize($_SERVER['QUERY_STRING']);
		} else {
			$queryString = '';
		}
		
		$pageCacheFile = AM_BASE_DIR . AM_DIR_CACHE . '/' . $_SERVER['SERVER_NAME'] . $currentPath . '/' . AM_FILE_PREFIX_CACHE . $queryString . '.' . AM_FILE_EXT_PAGE_CACHE;
		
		return $pageCacheFile;
		
	}
	
	
	/**
	 *	Get an array of all subdirectories and *.txt files under /pages and determine the latest mtime among all these items.
	 *	That time basically represents the site's modification time, to find out the lastes edit/removal/add of a page.
	 *
	 *	@return The latest found mtime, which equal basically the site's modification time.
	 */
	
	private function getSiteMTime() {
		
		$arrayDirsAndFiles = array();
		
		// The following directories are monitored for any changes.
		$monitoredDirs = array(AM_DIR_PAGES, AM_DIR_THEMES, AM_DIR_SHARED, '/config');
		
		foreach($monitoredDirs as $monitoredDir) {
		
			// Get all directories below the monitored directory (including the monitored directory).
			
			// Add base dir to string.
			$dir = AM_BASE_DIR . $monitoredDir;
			
			// Also add the directory itself, to monitor the top level.	
			$arrayDirs = array($dir);
	
			while ($dirs = glob($dir . '/*', GLOB_ONLYDIR)) {
				$dir .= '/*';
				$arrayDirs = array_merge($arrayDirs, $dirs);
			}

			// Get all files
			$arrayFiles = array();
	
			foreach ($arrayDirs as $d) {
				$arrayFiles = array_merge($arrayFiles, array_filter(glob($d . '/*'), 'is_file'));
			}
		
			// Merge all files and dirs into the full collection.
			$arrayDirsAndFiles = array_merge($arrayDirsAndFiles, $arrayDirs, $arrayFiles);

		}
		
		// Collect all modification times and find last modified item
		$mTimes = array();
	
		foreach ($arrayDirsAndFiles as $item) {
			$mTimes[$item] = filemtime($item);
		}
	
		// Needs to be that complicated to get the key and the mtime for debugging.
		// Can't use max() for that.
		asort($mTimes);
		$mTimesKeys = array_keys($mTimes);
		$lastModifiedItem = end($mTimesKeys);
		$siteMTime = $mTimes[$lastModifiedItem];
	
		Debug::log('Cache: Last modified: "' . $lastModifiedItem . '" - ' . date('d. M Y, H:i:s', $siteMTime));
		
		return $siteMTime;
		
	}
	
	
	/**
	 *	Read the rendered page from the cached version.
	 *
	 *	@return The full cached HTML of the page. 
	 */
	
	public function readPageFromCache() {
		
		Debug::log('Cache: Read: ' . $this->pageCacheFile);
		return file_get_contents($this->pageCacheFile);
		
	}
	
	
	/**
	 *	Write the rendered HTML output to the cache file.
	 */
	
	public function writePageToCache($output) {
		
		if (AM_CACHE_ENABLED) {
		
			if(!file_exists(dirname($this->pageCacheFile))) {
				mkdir(dirname($this->pageCacheFile), 0700, true);
		    	}
		
			file_put_contents($this->pageCacheFile, $output);
		
			Debug::log('Cache: Write: ' . $this->pageCacheFile);
		
		}
		
	}
	
	
}


?>