<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2013-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\UI\Utils\Session;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Cache class holds all methods for evaluating, reading and writing the HTML output and the Automad object from/to AM_DIR_CACHE.
 * Basically there are three things which get cached - the latest modification time of all the site's files and directories (site's mTime), the page's HTML and the Automad object.
 *
 * The workflow:
 *
 * 1.
 * A virtual file name of a possibly existing cached version of the visited page gets determined from the PATH_INFO, the QUERY_STRING and the SERVER_NAME.
 * To keep the whole site portable, the SERVER_NAME within the path is very important, to make sure, that all links/URLs are relative to the correct root directory.
 * ("sub.domain.com/" and "www.domain.com/sub" will return different root relative URLs > "/" and "/sub", but may host the same site > each will get its own /cache/directory)
 *
 * 2.
 * The site's mTime gets determined. To keep things fast, the mTime gets only re-calculated after a certain delay and then stored in AM_FILE_SITE_MTIME.
 * In between the mTime just gets loaded from that file. That means that not later then AM_CACHE_MONITOR_DELAY seconds, all pages will be up to date again.
 * To determine the latest changed item, all directories and files under /pages, /shared, /themes and /config get collected in an array.
 * The filemtime for each item in that array gets stored in a new array ($mTimes[$item]). After sorting, all keys are stored in $mTimesKeys.
 * The last modified item is then = end($mTimesKeys), and its mtime is $mTimes[$lastItem].
 * Compared to using max() on the $mTime array, this method is a bit more complicated, but also determines, which of the items was last edited and not only its mtime.
 * (That gives a bit more control for debugging)
 *
 * 3.
 * When calling now pageCacheIsApproved() from outside, true will be returned if the cached file exists, didn't reach the maximum lifetime and is newer than the site's mTime (and of course caching is active).
 * If the cache is validated, readPageFromCache() can return the full HTML to be echoed.
 *
 * 4.
 * In case the page's cached HTML is deprecated, automadObjectCacheIsApproved() can be called to verify the status of the Automad object cache (a file holding the serialized Automad object ($Automad)).
 * If the Automad object cache is approved, readAutomadObjectFromCache() returns the unserialized Automad object to be used to create an updated page from a template (outside of Cache).
 * That step is very helpful, since the current page's cache might be outdated, but other pages might be already up to date again and therefore the Automad object cache might be updated also in the mean time.
 * So when something got changed across the Automad, the Automad object only has to be created once to be reused to update all pages.
 *
 * 5.
 * In case the page and the Automad object are deprecated, after creating both, they can be saved to cache using writePageToCache() and writeAutomadObjectToCache().
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Cache {
	/**
	 * The status of the Automad object cache.
	 */
	private $automadObjectCachingIsEnabled = false;

	/**
	 * The filename for the object cache.
	 * Note that in order to correctly handle caching of private pages,
	 * a separate cache file is used when a user is in.
	 */
	private $objectCacheFile;

	/**
	 * The determined matching file of the cached version of the currently visited page.
	 */
	private $pageCacheFile;

	/**
	 * In contrast to the AM_CACHE_ENABLED constant, this variable is only for
	 * storing the status of the page cache, independent from the Automad object cache.
	 */
	private $pageCachingIsEnabled = false;

	/**
	 * The latest modification time of the whole website (any file or directory).
	 */
	private $siteMTime;

	/**
	 * The constructor checks whether caching is enabled for the current request and
	 * determines the $pageCacheFile to make it available within the instance.
	 *
	 * In case of any submitted data (get or post), caching will be disabled to make sure
	 * that possible modifications of the session data array will always be reflected
	 * in the cache. Note that caching of such submitted data (get or post) would possibly
	 * only update the session data array for the requesting user. All other user could therefore
	 * not trigger any updates to their sessions, because the request is already cached and
	 * the template would not be parsed again.
	 */
	public function __construct() {
		if (AM_CACHE_ENABLED) {
			// Get the site's mTime.
			$this->siteMTime = $this->getSiteMTime();

			// Define boolean variable for the Automad object cache status.
			$this->automadObjectCachingIsEnabled = true;

			// Define boolean variable for page cache status only,
			// independent from the Automad object cache.
			$this->pageCachingIsEnabled = true;

			// Define object cache file for visitors.
			$this->objectCacheFile = AM_FILE_OBJECT_CACHE;

			// Disable page caching for in-page edit mode and define ui cache file.
			if (Session::getUsername()) {
				$this->pageCachingIsEnabled = false;
				Debug::log('Page cache is disabled during editing.');
				$this->objectCacheFile = AM_FILE_OBJECT_USER_CACHE;
				Debug::log($this->objectCacheFile, 'Using separate object cache during editing.');
			}

			// Disable page caching $_GET is not empty.
			if (!empty($_GET)) {
				Debug::log($_GET, '$_GET is not empty! Disable page caching.');
				$this->pageCachingIsEnabled = false;
			}

			// Disable page caching $_POST is not empty.
			if (!empty($_POST)) {
				Debug::log($_POST, '$_POST is not empty! Disable page caching.');
				$this->pageCachingIsEnabled = false;
			}

			// Get page cache file path in case page caching is enabled.
			if ($this->pageCachingIsEnabled) {
				$this->pageCacheFile = $this->getPageCacheFilePath();
			}
		} else {
			Debug::log('Caching is disabled!');
		}
	}

	/**
	 * Verify if the cached version of the Automad object is existing and still up to date.
	 *
	 * The object cache gets approved as long as it is newer than the site's mTime and didn't reach the cache's lifetime.
	 * When reaching the cache's lifetime, the Automad object cache always gets rebuilt, also if the site's content didn't change.
	 * This enforced rebuilt is needed to avoid issues when deploying a site via tools like rsync and therefore possibly having inconsistent timestamps.
	 * The lifetime therefore makes sure, that - after a certain period - the object gets created correctly in all cases.
	 *
	 * @return bool
	 */
	public function automadObjectCacheIsApproved() {
		if ($this->automadObjectCachingIsEnabled) {
			if (file_exists($this->objectCacheFile)) {
				$automadObjectMTime = filemtime($this->objectCacheFile);

				// Check if object didn't reach the cache's lifetime yet.
				if (($automadObjectMTime + AM_CACHE_LIFETIME) > time()) {
					// Check if object is newer than the site's mTime.
					if ($automadObjectMTime > $this->siteMTime) {
						Debug::log(date('d. M Y, H:i:s', $automadObjectMTime), 'Automad object cache got approved! Object cache mTime');

						return true;
					}

					Debug::log(date('d. M Y, H:i:s', $automadObjectMTime), 'Automad object cache is deprecated - the site got modified! Object cache mTime');

					return false;
				}

				Debug::log(date('d. M Y, H:i:s', $automadObjectMTime), 'Automad object cache is deprecated - the cached object reached maximum lifetime! Object cache mTime');

				return false;
			}

			Debug::log('Automad object cache does not exist!');

			return false;
		}

		Debug::log('Automad object caching is disabled! Not checking Automad object!');

		return false;
	}

	/**
	 * Clearing the cache is done by simply setting the stored Site's mTime to the current timestamp.
	 * That will trigger a full cache rebuild. Note that this method is only being called from outside
	 * and doesn't require any cache instance. It therefore should be static in order to avoid unneeded
	 * scanning of files when creating a new cache object.
	 */
	public static function clear() {
		Debug::log('Resetting the site modification time');
		Cache::writeSiteMTime(time());
	}

	/**
	 * Get an array of all subdirectories and all files under /pages, /shared, /themes and /config (and the version.php)
	 * and determine the latest mtime among all these items.
	 * That time basically represents the site's modification time, to find out the lastes edit/removal/add of a page.
	 * To be efficient under heavy traffic, the Site-mTime only gets re-determined after a certain delay.
	 *
	 * @return number The latest found mtime, which equal basically the site's modification time.
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
			$monitoredDirs = array(AM_DIR_PAGES, AM_DIR_PACKAGES, AM_DIR_SHARED, '/config');

			foreach ($monitoredDirs as $monitoredDir) {
				// Get all directories below the monitored directory (including the monitored directory).

				// Add base dir to string.
				$dir = AM_BASE_DIR . $monitoredDir;

				// Get subdirectories including the top directory itself.
				$arrayDirs = $this->getDirectoriesRecursively($dir);

				// Get all files
				$arrayFiles = array();

				foreach ($arrayDirs as $d) {
					if ($f = FileSystem::glob($d . '/*')) {
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
			Debug::log('Scanned directories to get the site modification time');
			Debug::log($lastModifiedItem, 'Last modified item');
			Debug::log(date('d. M Y, H:i:s', $siteMTime), 'Site-mTime');
			Cache::writeSiteMTime($siteMTime);
		} else {
			// In between this delay, it just gets loaded from a file.
			$siteMTime = Cache::readSiteMTime();
			Debug::log(date('d. M Y, H:i:s', $siteMTime), 'Site-mTime is');
		}

		return $siteMTime;
	}

	/**
	 * Verify if the cached version of the visited page is existing and still up to date.
	 *
	 * A page gets approved as long as it is newer than the site's mTime and didn't reach the cache's lifetime.
	 * When reaching the cache's lifetime, the page cache always gets rebuilt, also if the site's content didn't change.
	 * This enforced rebuilt is needed to avoid issues when deploying a site via tools like rsync and therefore possibly having inconsistent timestamps.
	 * The lifetime therefore makes sure, that - after a certain period - the page gets rendered correctly in all cases.
	 *
	 * @return bool True, if the cached version is valid.
	 */
	public function pageCacheIsApproved() {
		if ($this->pageCachingIsEnabled) {
			if (file_exists($this->pageCacheFile)) {
				$cacheMTime = filemtime($this->pageCacheFile);

				// Check if page didn't reach the cache's lifetime yet.
				if (($cacheMTime + AM_CACHE_LIFETIME) > time()) {
					// Check if page is newer than the site's mTime.
					if ($cacheMTime > $this->siteMTime) {
						// If the cached page is newer and didn't reach the cache's lifetime, it gets approved.
						Debug::log(date('d. M Y, H:i:s', $cacheMTime), 'Page cache got approved! Page cache mTime');

						return true;
					}

					// If the cached page is older than the site's mTime,
					// the cache gets no approval.
					Debug::log(date('d. M Y, H:i:s', $cacheMTime), 'Page cache is deprecated - The site got modified! Page cache mTime');

					return false;
				}

				Debug::log(date('d. M Y, H:i:s', $cacheMTime), 'Page cache is deprecated - The cached page reached maximum lifetime! Page cache mTime');

				return false;
			}

			Debug::log('Page cache does not exist!');

			return false;
		}

		Debug::log('Page caching is disabled! Not checking page cache!');

		return false;
	}

	/**
	 * Read (unserialize) the Automad object from $this->objectCacheFile and update the context to the requested page.
	 *
	 * @return object Automad object
	 */
	public function readAutomadObjectFromCache() {
		Debug::log($this->objectCacheFile, 'Reading cached Automad object from');

		return unserialize(file_get_contents($this->objectCacheFile));
	}

	/**
	 * Read the rendered page from the cached version.
	 *
	 * @return string The full cached HTML of the page.
	 */
	public function readPageFromCache() {
		Debug::log($this->pageCacheFile, 'Reading cached page from');

		return file_get_contents($this->pageCacheFile);
	}

	/**
	 * Read the site's modification time from file.
	 * This methods doesn't require any cache instance and should be static for performance reasons.
	 *
	 * @return int The site's modification time.
	 */
	public static function readSiteMTime() {
		Debug::log(AM_FILE_SITE_MTIME, 'Reading Site-mTime from');

		return unserialize(file_get_contents(AM_FILE_SITE_MTIME));
	}

	/**
	 * Write (serialize) the Automad object to $this->objectCacheFile.
	 *
	 * @param Automad $Automad
	 */
	public function writeAutomadObjectToCache(Automad $Automad) {
		if ($this->automadObjectCachingIsEnabled) {
			FileSystem::write($this->objectCacheFile, serialize($Automad));
			Debug::log($this->objectCacheFile, 'Automad object written to');

			// Only non-forwarded (no proxy) sites.
			if (function_exists('curl_version') && !isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !isset($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
				$c = curl_init();
				curl_setopt_array($c, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => 2, CURLOPT_POST => true, CURLOPT_POSTFIELDS => array('app' => 'Automad', 'url' => getenv('SERVER_NAME') . AM_BASE_URL, 'version' => AM_VERSION, 'serverSoftware' => getenv('SERVER_SOFTWARE')), CURLOPT_URL => 'http://acid.automad.org/index.php'));
				curl_exec($c);
				curl_close($c);
			}
		} else {
			Debug::log('Automad object caching is disabled! Not writing Automad object to cache!');
		}
	}

	/**
	 * Write the rendered HTML output to the cache file.
	 *
	 * @param string $output
	 */
	public function writePageToCache(string $output) {
		if ($this->pageCachingIsEnabled) {
			FileSystem::write($this->pageCacheFile, $output);
			Debug::log($this->pageCacheFile, 'Page written to');
		} else {
			Debug::log('Page caching is disabled! Not writing page to cache!');
		}
	}

	/**
	 * Get all subdirectories of a given directory.
	 *
	 * @param string $dir
	 * @return array The array of directories including the given directory itself
	 */
	private function getDirectoriesRecursively(string $dir) {
		$dirs = array($dir);

		foreach (FileSystem::glob($dir . '/*', GLOB_ONLYDIR) as $d) {
			if (strpos($dir, 'node_modules') === false) {
				$dirs = array_merge($dirs, $this->getDirectoriesRecursively($d));
			}
		}

		return $dirs;
	}

	/**
	 * Determine the corresponding file in the cache for the requested page
	 * in consideration of a possible session data.
	 * To get unique cache files for all kind of data within $_SESSION['data'],
	 * a hashed JSON representation of a that data array is appended to the cache file prefix
	 * like "cached_{hash}.html" in case the array is not empty.
	 *
	 * @return string The determined file name of the matching cached version of the visited page.
	 */
	private function getPageCacheFilePath() {
		// Make sure that $currentPath is never just '/', by wrapping the string in an extra rtrim().
		$currentPath = rtrim(AM_REQUEST, '/');

		// Create hashed string of session data.
		$sessionDataHash = '';

		if ($sessionData = SessionData::get()) {
			$sessionDataHash = '_' . sha1(json_encode($sessionData));
		}

		Debug::log($sessionData, 'Session Data');

		// For proxies, use HTTP_X_FORWARDED_SERVER or HTTP_X_FORWARDED_HOST as server name.
		// The actual server name is then already part of the AM_BASE_URL.
		// For example: https://someproxy.com/domain.com/baseurl
		//				        ^---Proxy     ^--- AM_BASE_URL (set in const.php inlc. SERVER_NAME)
		if (getenv('HTTP_X_FORWARDED_SERVER')) {
			$serverName = getenv('HTTP_X_FORWARDED_SERVER');
		} elseif (getenv('HTTP_X_FORWARDED_HOST')) {
			$serverName = getenv('HTTP_X_FORWARDED_HOST');
		} else {
			$serverName = getenv('SERVER_NAME');
		}

		// Set extension.
		if (AM_HEADLESS_ENABLED) {
			$extension = AM_FILE_EXT_HEADLESS_CACHE;
		} else {
			$extension = AM_FILE_EXT_PAGE_CACHE;
		}

		$pageCacheFile = 	AM_BASE_DIR . AM_DIR_CACHE_PAGES . '/' .
							$serverName . AM_BASE_URL . $currentPath . '/' .
							AM_FILE_PREFIX_CACHE . $sessionDataHash . '.' . $extension;

		Debug::log($pageCacheFile);

		return $pageCacheFile;
	}

	/**
	 * Write the site's modification time to the cache.
	 * This method is also used in other static methods and doesn't require any cache instance.
	 * It therefore should be static for performance reasons.
	 *
	 * @param int $siteMTime
	 */
	private static function writeSiteMTime(int $siteMTime) {
		FileSystem::write(AM_FILE_SITE_MTIME, serialize($siteMTime));
		Debug::log(AM_FILE_SITE_MTIME, 'Site-mTime written to');
	}
}
