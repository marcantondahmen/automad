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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models;

use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\PageIndex;
use Automad\Core\Session;
use Automad\Core\Sitemap;
use Automad\Core\Str;
use Automad\Routes;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page collection class handles building the page collection base on an entry point directory.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageCollection {
	/**
	 * The collection array.
	 *
	 * @var array<Page>
	 */
	private array $collection = array();

	/**
	 * An array of existing directories within the base directory (/automad, /config, /pages etc.)
	 */
	private array $reservedUrls;

	/**
	 * Automad's Shared object.
	 */
	private Shared $Shared;

	/**
	 * An array of URLs that are already assigned to a page or are a reserved URL.
	 */
	private array $takenUrls = array();

	/**
	 * The username of the currently logged in user or an empty string.
	 */
	private string $user;

	/**
	 * The constructor.
	 *
	 * @param Shared $Shared
	 */
	public function __construct(Shared $Shared) {
		$this->Shared = $Shared;
		$this->reservedUrls = $this->getReservedUrls();
		$this->user = Session::getUsername();

		$this->collectPages();

		new Sitemap($this->collection);
	}

	/**
	 * Get the page collection array.
	 *
	 * @return array<string, Page> the pages array
	 */
	public function get(): array {
		return $this->collection;
	}

	/**
	 * Searches $path recursively for data files and adds the parsed data to $collection.
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
	private function collectPages(
		string $path = '/',
		int $level = 0,
		string $parentUrl = '',
		string $index = '1'
	): void {
		$url = $this->makeUrl($parentUrl, basename($path));
		$Page = Page::fromDataStore($path, $url, $index, $this->Shared, $parentUrl, $level);

		if (!$Page) {
			return;
		}

		// Stop processing of page data and subdirectories if page is private and nobody is logged in.
		if ($Page->private && !$this->user) {
			return;
		}

		$this->collection[$url] = $Page;
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
			$pad = strlen((string) count($children));
			$i = 1;

			// Scan each directory recursively.
			foreach ($children as $child) {
				$childIndex = $index . '.' . str_pad((string) $i, $pad, '0', STR_PAD_LEFT);

				$this->collectPages($path . basename($child) . '/', $level + 1, $url, $childIndex);
				$i++;
			}
		}
	}

	/**
	 * Get the list of taken URLs that can't be used as page URLs.
	 */
	private function getReservedUrls(): array {
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
	private function makeUrl(string $parentUrl, string $slug): string {
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
