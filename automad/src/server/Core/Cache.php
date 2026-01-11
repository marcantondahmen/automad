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
 * Copyright (c) 2013-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\App;

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
 * The site's mTime gets determined. To keep things fast, the mTime gets only re-calculated after a certain delay and then stored in FILE_SITE_MTIME.
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
 * @copyright Copyright (c) 2013-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Cache {
	const DIR_IMAGES = AM_DIR_CACHE . '/images';
	const DIR_PAGES = AM_DIR_TMP . '/pages';
	const FILE_OBJECT_API_CACHE = AM_DIR_TMP . '/' . 'automad_admin';
	const FILE_OBJECT_CACHE = AM_DIR_TMP . '/' . 'automad_public';
	const FILE_SITE_MTIME = AM_DIR_TMP . '/' . 'site_mtime';

	/**
	 * The status of the Automad object cache.
	 */
	private bool $automadObjectCachingIsEnabled = false;

	/**
	 * The filename for the object cache.
	 * Note that in order to correctly handle caching of private pages,
	 * a separate cache file is used when a user is in.
	 */
	private string $objectCacheFile = '';

	/**
	 * The determined matching file of the cached version of the currently visited page.
	 */
	private string $pageCacheFile = '';

	/**
	 * In contrast to the AM_CACHE_ENABLED constant, this variable is only for
	 * storing the status of the page cache, independent from the Automad object cache.
	 */
	private bool $pageCachingIsEnabled = false;

	/**
	 * The latest modification time of the whole website (any file or directory).
	 */
	private ?int $siteMTime = null;

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
		if (!AM_CACHE_ENABLED) {
			Debug::log('Caching is disabled!');

			return;
		}

		// Get the site's mTime.
		$this->siteMTime = $this->getSiteMTime();

		// Define boolean variable for the Automad object cache status.
		$this->automadObjectCachingIsEnabled = true;

		// Define boolean variable for page cache status only,
		// independent from the Automad object cache.
		$this->pageCachingIsEnabled = true;

		// Define object cache file for visitors.
		$this->objectCacheFile = Cache::FILE_OBJECT_CACHE;

		// Disable page caching for in-page edit mode and define ui cache file.
		if (Session::getUsername()) {
			$this->pageCachingIsEnabled = false;
			Debug::log('Page cache is disabled during editing.');
			$this->objectCacheFile = Cache::FILE_OBJECT_API_CACHE;
			Debug::log($this->objectCacheFile, 'Using separate object cache during editing.');
		}

		// Get page cache file path in case page caching is enabled.
		if ($this->pageCachingIsEnabled) {
			$this->pageCacheFile = $this->getPageCacheFilePath();
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
	public function automadObjectCacheIsApproved(): bool {
		if (!$this->automadObjectCachingIsEnabled) {
			Debug::log('Automad object caching is disabled! Not checking Automad object!');

			return false;
		}

		if (!file_exists($this->objectCacheFile)) {
			Debug::log('Automad object cache does not exist!');

			return false;
		}

		$automadObjectMTime = intval(filemtime($this->objectCacheFile));

		if (($automadObjectMTime + AM_CACHE_LIFETIME) <= time()) {
			Debug::log(
				date('d. M Y, H:i:s', $automadObjectMTime),
				'Automad object cache has expired - maximum lifetime reached! Object cache mTime'
			);

			return false;
		}

		if ($automadObjectMTime <= $this->siteMTime) {
			Debug::log(
				date('d. M Y, H:i:s', $automadObjectMTime),
				'Automad object cache has expired - content has changed! Object cache mTime'
			);

			return false;
		}

		Debug::log(
			date('d. M Y, H:i:s', $automadObjectMTime),
			'Automad object cache is approved! Object cache mTime'
		);

		return true;
	}

	/**
	 * Clearing the cache is done by simply setting the stored Site's mTime to the current timestamp.
	 * That will trigger a full cache rebuild. Note that this method is only being called from outside
	 * and doesn't require any cache instance. It therefore should be static in order to avoid unneeded
	 * scanning of files when creating a new cache object.
	 */
	public static function clear(): void {
		Debug::log('Resetting the site modification time and removing objects');
		Cache::writeSiteMTime(time());

		if (file_exists(Cache::FILE_OBJECT_CACHE)) {
			unlink(Cache::FILE_OBJECT_CACHE);
		}

		if (file_exists(Cache::FILE_OBJECT_API_CACHE)) {
			unlink(Cache::FILE_OBJECT_API_CACHE);
		}
	}

	/**
	 * Get Automad from cache or create new instance.
	 *
	 * @return Automad The Automad object
	 */
	public function getAutomad(): Automad {
		if ($this->automadObjectCacheIsApproved()) {
			$Automad = $this->readAutomadObjectFromCache();

			if ($Automad) {
				return $Automad;
			}

			Debug::log('An error occured while unserializing the Automad cache.');
		}

		$Automad = Automad::create();

		$this->writeAutomadObjectToCache($Automad);

		return $Automad;
	}

	/**
	 * Get an array of all subdirectories and all files under /pages, /shared, /themes and /config
	 * and determine the latest mtime among all these items.
	 * That time basically represents the site's modification time, to find out the lastes edit/removal/add of a page.
	 * To be efficient under heavy traffic, the Site-mTime only gets re-determined after a certain delay.
	 *
	 * @return int The latest found mtime, which equal basically the site's modification time.
	 */
	public function getSiteMTime(): int {
		if (!is_readable(Cache::FILE_SITE_MTIME) || (@filemtime(Cache::FILE_SITE_MTIME) + AM_CACHE_MONITOR_DELAY) < time()) {
			// The modification times get only checked every AM_CACHE_MONITOR_DELAY seconds, since
			// the process of collecting all mtimes itself takes some time too.
			// After scanning, the mTime gets written to a file.

			// $arrayDirsAndFiles will collect all relevant files and dirs to be monitored for changes.
			$arrayDirsAndFiles = array();

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
			$siteMTime = intval($mTimes[$lastModifiedItem]);

			// Save mTime
			Debug::log('Scanned directories to get the site modification time');
			Debug::log($lastModifiedItem, 'Last modified item');
			Debug::log(date('d. M Y, H:i:s', $siteMTime), 'Site-mTime');
			Cache::writeSiteMTime($siteMTime);

			return $siteMTime;
		}

		// In between this delay, it just gets loaded from a file.
		$siteMTime = Cache::readSiteMTime();
		Debug::log(date('d. M Y, H:i:s', $siteMTime), 'Site-mTime is');

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
	public function pageCacheIsApproved(): bool {
		if (!$this->pageCachingIsEnabled) {
			Debug::log('Page caching is disabled! Not checking page cache!');

			return false;
		}

		if (!file_exists($this->pageCacheFile)) {
			Debug::log('Page cache does not exist!');

			return false;
		}

		$cacheMTime = intval(filemtime($this->pageCacheFile));

		if (($cacheMTime + AM_CACHE_LIFETIME) <= time()) {
			Debug::log(
				date('d. M Y, H:i:s', $cacheMTime),
				'Page cache has expired - maximum lifetime reached! Page cache mTime'
			);

			return false;
		}

		if ($cacheMTime <= $this->siteMTime) {
			Debug::log(
				date('d. M Y, H:i:s', $cacheMTime),
				'Page cache has expired - content has changed! Page cache mTime'
			);

			return false;
		}

		Debug::log(
			date('d. M Y, H:i:s', $cacheMTime),
			'Page cache is approved! Page cache mTime'
		);

		return true;
	}

	/**
	 * Read the rendered page from the cached version.
	 *
	 * @return string The full cached HTML of the page.
	 */
	public function readPageFromCache(): string {
		Debug::log($this->pageCacheFile, 'Reading cached page from');

		return strval(file_get_contents($this->pageCacheFile));
	}

	/**
	 * Read the site's modification time from file.
	 * This methods doesn't require any cache instance and should be static for performance reasons.
	 *
	 * @return int The site's modification time.
	 */
	public static function readSiteMTime(): int {
		Debug::log(Cache::FILE_SITE_MTIME, 'Reading Site-mTime from');

		return unserialize(strval(file_get_contents(Cache::FILE_SITE_MTIME)));
	}

	/**
	 * Write (serialize) the Automad object to $this->objectCacheFile.
	 *
	 * @param Automad $Automad
	 */
	public function writeAutomadObjectToCache(Automad $Automad): void {
		if (!$this->automadObjectCachingIsEnabled) {
			Debug::log('Automad object caching is disabled! Not writing Automad object to cache!');

			return;
		}

		FileSystem::write($this->objectCacheFile, serialize($Automad));
		Debug::log($this->objectCacheFile, 'Automad object written to');

		if (function_exists('curl_version')) {
			$c = curl_init();

			if ($c) {
				curl_setopt_array($c, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => 2, CURLOPT_POST => true, CURLOPT_POSTFIELDS => array('app' => 'Automad', 'url' => ($_SERVER['SERVER_NAME'] ?? '') . AM_BASE_URL, 'version' => App::VERSION, 'serverSoftware' => ($_SERVER['SERVER_SOFTWARE'] ?? '')), CURLOPT_URL => 'http://acid.automad.org/index.php'));
				curl_exec($c);
			}
		}
	}

	/**
	 * Write the rendered HTML output to the cache file.
	 *
	 * @param string $output
	 */
	public function writePageToCache(string $output): void {
		if (!$this->pageCachingIsEnabled) {
			Debug::log('Page caching is disabled! Not writing page to cache!');

			return;
		}

		FileSystem::write($this->pageCacheFile, $output);
		Debug::log($this->pageCacheFile, 'Page written to');
	}

	/**
	 * Get all subdirectories of a given directory.
	 *
	 * @param string $dir
	 * @return array The array of directories including the given directory itself
	 */
	private function getDirectoriesRecursively(string $dir): array {
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
	private function getPageCacheFilePath(): string {
		$route = rtrim(AM_REQUEST, '/');

		$parameters = array();
		$suffix = '';

		if ($sessionData = SessionData::get()) {
			$parameters['session'] = $sessionData;
		}

		if (!empty($_GET)) {
			$parameters['get'] = $_GET;
		}

		if (!empty($_POST)) {
			$parameters['post'] = $_POST;
		}

		if (!empty($parameters)) {
			$hash = sha1(strval(json_encode($parameters)));
			$suffix = ".$hash";
			Debug::log($parameters, "Parameters hash $hash");
		}

		// For proxies, use HTTP_X_FORWARDED_SERVER or HTTP_X_FORWARDED_HOST as server name.
		// The actual server name is then already part of the AM_BASE_URL.
		// For example: https://someproxy.com/domain.com/baseurl
		//				        ^---Proxy     ^--- AM_BASE_URL (set in const.php inlc. SERVER_NAME)
		$server = $_SERVER['HTTP_X_FORWARDED_SERVER'] ?? ($_SERVER['HTTP_X_FORWARDED_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? ''));
		$port = $_SERVER['SERVER_PORT'] ?? '80';
		$file = Cache::DIR_PAGES . '/' . $server . '_' . $port . AM_BASE_URL . $route . '/page' . $suffix;

		Debug::log($file);

		return $file;
	}

	/**
	 * Read (unserialize) the Automad object from $this->objectCacheFile and update the context to the requested page.
	 *
	 * @return Automad|null Automad object
	 */
	private function readAutomadObjectFromCache(): Automad|null {
		Debug::log($this->objectCacheFile, 'Reading cached Automad object from');

		try {
			return unserialize(strval(file_get_contents($this->objectCacheFile)));
		} catch (\Throwable $th) {
			Cache::clear();
		}

		return null;
	}

	/**
	 * Write the site's modification time to the cache.
	 * This method is also used in other static methods and doesn't require any cache instance.
	 * It therefore should be static for performance reasons.
	 *
	 * @param int $siteMTime
	 */
	private static function writeSiteMTime(int $siteMTime): void {
		FileSystem::write(Cache::FILE_SITE_MTIME, serialize($siteMTime));
		Debug::log(Cache::FILE_SITE_MTIME, 'Site-mTime written to');
	}
}
