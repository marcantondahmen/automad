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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models;

use Automad\Admin\Session;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\PageIndex;
use Automad\Core\Parse;
use Automad\Core\Str;
use Automad\Routes;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page collection class handles building the page collection base on an entry point directory.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageCollection {
	/**
	 * The collection array.
	 */
	private $collection = array();

	/**
	 * An array of existing directories within the base directory (/automad, /config, /pages etc.)
	 */
	private $reservedUrls;

	/**
	 * Automad's Shared object.
	 */
	private $Shared;

	/**
	 * An array of URLs that are already assigned to a page or are a reserved URL.
	 */
	private $takenUrls = array();

	/**
	 * The username of the currently logged in user or false.
	 */
	private $user;

	/**
	 * The constructor.
	 * @param string $entryDir
	 * @param Shared $Shared
	 */
	public function __construct(string $entryDir, Shared $Shared) {
		$this->Shared = $Shared;
		$this->reservedUrls = $this->getReservedUrls();
		$this->user = Session::getUsername();

		$this->collectPages($entryDir);
	}

	/**
	 * Get the page collection array.
	 *
	 * @return array the pages array
	 */
	public function get() {
		return $this->collection;
	}

	/**
	 * Searches $path recursively for files with the AM_FILE_EXT_DATA and adds the parsed data to $collection.
	 *
	 * After successful indexing, the $collection holds basically all information (except media files) from all pages of the whole site.
	 * This makes searching and filtering very easy since all data is stored in one place.
	 * To access the data of a specific page within the $collection array, the page's url serves as the key: $this->collection['/path/to/page']
	 *
	 * @param string $path
	 * @param int $level
	 * @param string $parentUrl
	 * @param string $index
	 */
	private function collectPages(string $path = '/', int $level = 0, string $parentUrl = '', string $index = '1') {
		// First check, if $path contains any data files.
		// If more that one file matches the pattern, the first one will be used as the page's data file and the others will just be ignored.
		if ($files = FileSystem::glob(AM_BASE_DIR . AM_DIR_PAGES . $path . '*.' . AM_FILE_EXT_DATA)) {
			$file = reset($files);

			// Set URL.
			$url = $this->makeUrl($parentUrl, basename($path));

			// Get content from text file.
			$data = Parse::dataFile($file);

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
			$data[AM_KEY_PAGE_INDEX] = $index;
			$data[AM_KEY_PATH] = $path;
			$data[AM_KEY_LEVEL] = $level;
			$data[AM_KEY_PARENT] = $parentUrl;
			$data[AM_KEY_TEMPLATE] = str_replace('.' . AM_FILE_EXT_DATA, '', basename($file));

			// The relative URL ($url) of the page becomes the key (in $collection).
			// That way it is impossible to create twice the same url and it is very easy to access the page's data.
			// It will actually always be the "real" Automad-URL, even if a redirect-URL is specified (that one will be stored in $Page->url and $data instead).
			$this->collection[$url] = new Page($data, $this->Shared);

			$children = FileSystem::glob(AM_BASE_DIR . AM_DIR_PAGES . $path . '*', GLOB_ONLYDIR);

			// Merge index file with glob array in order to restore the defined page order.
			$fullPath = AM_BASE_DIR . AM_DIR_PAGES . $path;

			$layout = array_map(function ($item) use ($fullPath) {
				return "$fullPath$item";
			}, PageIndex::read($path));

			$layout = array_filter($layout, function ($item) {
				return is_readable($item);
			});

			if (!empty($children)) {
				$children = array_unique(array_merge($layout, $children));
			}

			// $path gets only scanned for sub-pages, in case it contains a data file.
			// That way it is impossible to generate pages without a parent page.
			if ($children) {
				$pad = strlen(count($children));
				$i = 1;

				// Scan each directory recursively.
				foreach ($children as $child) {
					$childIndex = $index . '.' . str_pad($i, $pad, '0', STR_PAD_LEFT);

					$this->collectPages($path . basename($child) . '/', $level + 1, $url, $childIndex);
					$i++;
				}
			}
		}
	}

	/**
	 * Get the list of taken URLs that can't be used as page URLs.
	 */
	private function getReservedUrls() {
		$reservedUrls = array();

		foreach (Routes::$registered as $route) {
			$url = preg_replace('#^(/[\w\-\_]*).*$#i', '$1', $route['route']);

			if ($url != '/') {
				$reservedUrls[] = $url;
			}
		}

		// Get all real directories.
		foreach (FileSystem::glob(AM_BASE_DIR . '/*', GLOB_ONLYDIR) as $dir) {
			$reservedUrls[] = '/' . basename($dir);
		}

		$reservedUrls = array_unique($reservedUrls);

		Debug::log($reservedUrls);

		return $reservedUrls;
	}

	/**
	 * Builds an URL out of the parent URL and the actual file system folder name.
	 *
	 * @param string $parentUrl
	 * @param string $slug
	 * @return string $url
	 */
	private function makeUrl(string $parentUrl, string $slug) {
		$url = '/' . ltrim($parentUrl . '/' . Str::slug($slug), '/');

		// Merge reserved URLs with already used URLs in the collection.
		$takenUrls = array_merge($this->reservedUrls, $this->takenUrls);

		// check if url already exists
		if (in_array($url, $takenUrls)) {
			$i = 0;
			$newUrl = $url;

			while (in_array($newUrl, $takenUrls)) {
				$i++;
				$newUrl = $url . '-' . $i;
			}

			$url = $newUrl;
		}

		$this->takenUrls[] = $url;

		return $url;
	}
}
