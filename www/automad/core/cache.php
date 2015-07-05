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
 *	The Cache class holds all methods for evaluating, reading and writing the HTML output and the Automad object from/to AM_DIR_CACHE.
 *	Basically there are three things which get cached - the latest modification time of all the site's files and directories (site's mTime), the page's HTML and the Automad object.
 *
 *	The workflow:
 *	
 *	1. 
 *	A virtual file name of a possibly existing cached version of the visited page gets determined from the PATH_INFO, the QUERY_STRING and the SERVER_NAME.
 *	To keep the whole site portable, the SERVER_NAME within the path is very important, to make sure, that all links/URLs are relative to the correct root directory.
 *	("sub.domain.com/" and "www.domain.com/sub" will return different root relative URLs > "/" and "/sub", but may host the same site > each will get its own /cache/directory)
 *
 *	2. 
 *	The site's mTime gets determined. To keep things fast, the mTime gets only re-calculated after a certain delay and then stored in AM_FILE_SITE_MTIME. 
 *	In between the mTime just gets loaded from that file. That means that not later then AM_CACHE_MONITOR_DELAY seconds, all pages will be up to date again.
 *	To determine the latest changed item, all directories and files under /pages, /shared, /themes and /config get collected in an array.
 *	The filemtime for each item in that array gets stored in a new array ($mTimes[$item]). After sorting, all keys are stored in $mTimesKeys.
 *	The last modified item is then = end($mTimesKeys), and its mtime is $mTimes[$lastItem].
 *	Compared to using max() on the $mTime array, this method is a bit more complicated, but also determines, which of the items was last edited and not only its mtime.
 *	(That gives a bit more control for debugging)
 *
 *	3.
 *	When calling now pageCacheIsApproved() from outside, true will be returned if the cached file exists, didn't reach the maximum lifetime and is newer than the site's mTime (and of course caching is active).
 *	If the cache is validated, readPageFromCache() can return the full HTML to be echoed.
 *	
 *	4. 
 *	In case the page's cached HTML is deprecated, automadObjectCacheIsApproved() can be called to verify the status of the Automad object cache (a file holding the serialized Automad object ($Automad)).
 *	If the Automad object cache is approved, readAutomadObjectFromCache() returns the unserialized Automad object to be used to create an updated page from a template (outside of Cache).
 *	That step is very helpful, since the current page's cache might be outdated, but other pages might be already up to date again and therefore the Automad object cache might be updated also in the mean time.
 *	So when something got changed across the Automad, the Automad object only has to be created once to be reused to update all pages. 
 *
 *	5.
 *	In case the page and the Automad object are deprecated, after creating both, they can be saved to cache using writePageToCache() and writeAutomadObjectToCache().
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Cache {
	
	
	/**
	 *	The determined matching file of the cached version of the currently visited page.
	 */
	
	private $pageCacheFile;
	

	/**
	 *	The latest modification time of the whole website (any file or directory).
	 */
	
	private $siteMTime;
	
	
	/**
	 *	The constructor just determines $pageCacheFile to make it available within the instance.
	 */
	
	public function __construct() {
		
		if (AM_CACHE_ENABLED) {
			
			Debug::log('Cache: New Instance created!');
			
			$this->pageCacheFile = $this->getPageCacheFilePath();
			$this->siteMTime = $this->getSiteMTime();
		
		} else {
			
			Debug::log('Cache: Caching is disabled!');
			
		}
		
	}
	

	/**
	 *	Clearing the cache is done by simply deleting the stored Site's mTime file. That will trigger a full cache rebuild.
	 */

	public function clear() {
		
		if (file_exists(AM_FILE_SITE_MTIME)) {
			
			unlink(AM_FILE_SITE_MTIME);
			
		}
		
	}


	/**
	 *	Verify if the cached version of the visited page is existing and still up to date.
	 *
	 *	A page gets approved as long as it is newer than the site's mTime and didn't reach the cache's lifetime. 
	 *	When reaching the cache's lifetime, the page cache always gets rebuilt, also if the site's content didn't change.
	 *	This enforced rebuilt is needed to avoid issues when deploying a site via tools like rsync and therefore possibly having inconsistent timestamps.
	 *	The lifetime therefore makes sure, that - after a certain period - the page gets rendered correctly in all cases.
	 *
	 *	@return boolean - true, if the cached version is valid.
	 */

	public function pageCacheIsApproved() {
		
		if (AM_CACHE_ENABLED) {
	
			if (file_exists($this->pageCacheFile)) {	
					
				$cacheMTime = filemtime($this->pageCacheFile);
			
				// Check if page didn't reach the cache's lifetime yet.
				if (($cacheMTime + AM_CACHE_LIFETIME) > time()) {
					
					// Check if page is newer than the site's mTime.
					if ($cacheMTime > $this->siteMTime) {
						
						// If the cached page is newer and didn't reach the cache's lifetime, it gets approved.
						Debug::log('Cache: Page cache got approved!');
						Debug::log('       Page cache mTime: ' . date('d. M Y, H:i:s', $cacheMTime));
						return true;
						
					} else {
						
						// If the cached page is older than the site's mTime,
						// the cache gets no approval.
						Debug::log('Cache: Page cache is deprecated! (Site got modified)'); 
						Debug::log('       Page cache mTime: ' . date('d. M Y, H:i:s', $cacheMTime));
						return false;
						
					}
				
				} else {	
					
					Debug::log('Cache: Page cache is deprecated! (Reached maximum lifetime)'); 
					Debug::log('       Page cache mTime: ' . date('d. M Y, H:i:s', $cacheMTime));
					return false;
					
				}	
				
			} else {
		
				Debug::log('Cache: Page cache does not exist!');
				return false;
		
			}
	
		} else {
			
			Debug::log('Cache: Caching is disabled! Not checking page cache!');
			return false;
			
		}
		
	}


	/**
	 *	Verify if the cached version of the Automad object is existingand  still up to date.
	 *
	 *	The object cache gets approved as long as it is newer than the site's mTime and didn't reach the cache's lifetime. 
	 *	When reaching the cache's lifetime, the Automad object cache always gets rebuilt, also if the site's content didn't change.
	 *	This enforced rebuilt is needed to avoid issues when deploying a site via tools like rsync and therefore possibly having inconsistent timestamps.
	 *	The lifetime therefore makes sure, that - after a certain period - the object gets created correctly in all cases.
	 *
	 *	@return boolean 
	 */

	public function automadObjectCacheIsApproved() {
		
		if (AM_CACHE_ENABLED) {
		
			if (file_exists(AM_FILE_OBJECT_CACHE)) {
		
				$automadObjectMTime = filemtime(AM_FILE_OBJECT_CACHE);
				
				// Check if object didn't reach the cache's lifetime yet.
				if (($automadObjectMTime + AM_CACHE_LIFETIME) > time()) {
				
					// Check if object is newer than the site's mTime.
					if ($automadObjectMTime > $this->siteMTime) {
						
						Debug::log('Cache: Automad object cache got approved!');
						Debug::log('       Automad object mTime: ' . date('d. M Y, H:i:s', $automadObjectMTime));
						return true;
						
					} else {
						
						Debug::log('Cache: Automad object cache is deprecated! (Site got modified)');
						Debug::log('       Automad object mTime: ' . date('d. M Y, H:i:s', $automadObjectMTime));
						return false;
					}
				
				} else {
								
					Debug::log('Cache: Automad object cache is deprecated! (Reached maximum lifetime)');
					Debug::log('       Automad object mTime: ' . date('d. M Y, H:i:s', $automadObjectMTime));
					return false;
			
				}
				
			} else {
				
				Debug::log('Cache: Automad object cache does not exist!');
				return false;
				
			}
			
		} else {
			
			Debug::log('Cache: Caching is disabled! Not checking automad object!');
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
			
		// Make sure that $currentPath is never just '/', by wrapping the string in an extra rtrim().
		$currentPath = rtrim(AM_REQUEST, '/');
		
		if ($_SERVER['QUERY_STRING']) {
			$queryString = '_' . Parse::sanitize($_SERVER['QUERY_STRING']);
		} else {
			$queryString = '';
		}
		
		// For proxies, use HTTP_X_FORWARDED_SERVER as server name. The actual server name is then already part of the AM_BASE_URL.
		// For example: https://someproxy.com/domain.com/baseurl
		//				        ^---Proxy     ^--- AM_BASE_URL (set in const.php inlc. SERVER_NAME)
		if (!isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !isset($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
			$serverName = $_SERVER['SERVER_NAME'];
		} else {
			$serverName = $_SERVER['HTTP_X_FORWARDED_SERVER'];
		}
		
		$pageCacheFile = AM_BASE_DIR . AM_DIR_CACHE_PAGES . '/' . $serverName . AM_BASE_URL . $currentPath . '/' . AM_FILE_PREFIX_CACHE . $queryString . '.' . AM_FILE_EXT_PAGE_CACHE;
		
		return $pageCacheFile;
		
	}
	
	
	/**
	 *	Get an array of all subdirectories and all files under /pages, /shared, /themes and /config (and the version.php) 
	 *	and determine the latest mtime among all these items.
	 *	That time basically represents the site's modification time, to find out the lastes edit/removal/add of a page.
	 *	To be efficient under heavy traffic, the Site-mTime only gets re-determined after a certain delay.
	 *
	 *	@return The latest found mtime, which equal basically the site's modification time.
	 */
	
	public function getSiteMTime() {
		
		if ((@filemtime(AM_FILE_SITE_MTIME) + AM_CACHE_MONITOR_DELAY) < time()) {
		
			// The modification times get only checked every AM_CACHE_MONITOR_DELAY seconds, since
			// the process of collecting all mtimes itself takes some time too.
			// After scanning, the mTime gets written to a file. 
		
			// $arrayDirsAndFiles will collect all relevant files and dirs to be monitored for changes.
			// At first, since it it just a single file, it will hold version.php. 
			// (This file always exists and there is no can needed to add it to the array)
			// The version file represents all changes to the core files, since it will always be increased with a changeset,
			// so the core itself doesn't need to be scanned.
			$arrayDirsAndFiles = array(AM_BASE_DIR . '/automad/version.php');
		
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
					if ($f = glob($d . '/*')) {
						$arrayFiles = array_merge($arrayFiles, array_filter($f, 'is_file'));
					}
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
			
			// Save mTime
			$old = umask(0);
			Debug::log('Cache: Changed umask: ' . umask());
			file_put_contents(AM_FILE_SITE_MTIME, serialize($siteMTime));
			umask($old);
			
			Debug::log('Cache: Scanned directories and saved Site-mTime.');
			Debug::log('       Last modified item: ' . $lastModifiedItem); 
			Debug::log('       Site-mTime:  ' . date('d. M Y, H:i:s', $siteMTime));
			Debug::log('       Write Site-mTime: ' . AM_FILE_SITE_MTIME);
			Debug::log('Cache: Restored umask: ' . umask());
		
		} else {
			
			// In between this delay, it just gets loaded from a file.
			$siteMTime = unserialize(file_get_contents(AM_FILE_SITE_MTIME));
			Debug::log('Cache: Read Site-mTime: ' . AM_FILE_SITE_MTIME);
			Debug::log('       Site-mTime:  ' . date('d. M Y, H:i:s', $siteMTime));
			
		}
		
		return $siteMTime;
		
	}
	
	
	/**
	 *	Read the rendered page from the cached version.
	 *
	 *	@return The full cached HTML of the page. 
	 */
	
	public function readPageFromCache() {
		
		Debug::log('Cache: Read page: ' . $this->pageCacheFile);
		return file_get_contents($this->pageCacheFile);
		
	}
	
	
	/**
	 *	 Read (unserialize) the Automad object from AM_FILE_OBJECT_CACHE.
	 *
	 *	@return Automad object
	 */
	
	public function readAutomadObjectFromCache() {
		
		$Automad = unserialize(file_get_contents(AM_FILE_OBJECT_CACHE));
		
		Debug::log('Cache: Read Automad object: ' . AM_FILE_OBJECT_CACHE);
		Debug::log($Automad->getCollection());
		
		return $Automad;
		
	}
	
	
	/**
	 *	Write the rendered HTML output to the cache file.
	 */
	
	public function writePageToCache($output) {
		
		if (AM_CACHE_ENABLED) {
			
			$old = umask(0);
			Debug::log('Cache: Changed umask: ' . umask());
		
			if(!file_exists(dirname($this->pageCacheFile))) {
				mkdir(dirname($this->pageCacheFile), 0777, true);
		    	}
		
			file_put_contents($this->pageCacheFile, $output);
			umask($old);
			Debug::log('Cache: Write page: ' . $this->pageCacheFile);
			Debug::log('Cache: Restored umask: ' . umask());
			
			// Only non-forwarded (no proxy) sites.
			if (function_exists('curl_version') && !isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !isset($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
				$c = curl_init();
				curl_setopt_array($c, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => 2, CURLOPT_POST => true, CURLOPT_POSTFIELDS => array('url' => $_SERVER['SERVER_NAME'] . AM_BASE_URL, 'app' => 'Automad', 'version' => AM_VERSION, 'licensekey' => AM_LIC_KEY), CURLOPT_URL => 'http://at.marcdahmen.de/track.php'));
				$r = curl_exec($c);
				curl_close($c);
			}
			
		} else {
			
			Debug::log('Cache: Caching is disabled! Not writing page to cache!');
			
		}
		
	}
	
	
	/**
	 *	Write (serialize) the Automad object to AM_FILE_OBJECT_CACHE.
	 */
	
	public function writeAutomadObjectToCache($Automad) {
		
		if (AM_CACHE_ENABLED) {
			
			$old = umask(0);
			Debug::log('Cache: Changed umask: ' . umask());
			file_put_contents(AM_FILE_OBJECT_CACHE, serialize($Automad));
			umask($old);
			Debug::log('Cache: Write Automad object: ' . AM_FILE_OBJECT_CACHE);
			Debug::log('Cache: Restored umask: ' . umask());
		
		} else {
			
			Debug::log('Cache: Caching is disabled! Not writing Automad object to cache!');
			
		}	
		
	}
	
	
}


?>