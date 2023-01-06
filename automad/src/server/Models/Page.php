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

namespace Automad\Models;

use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\FileSystem;
use Automad\Core\PageIndex;
use Automad\Core\Parse;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Page class holds all properties and methods of a single page.
 * A Page object describes an entry in the collection of all pages in the Automad class.
 * Basically the Automad object consists of many Page objects.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Page {
	/**
	 * The $data array holds all the information stored as "key: value" in the text file and some other system generated information (:path, :level, :template ...).
	 *
	 * The key can be everything alphanumeric as long as there is a matching var set in the template files.
	 * Out of all possible keys ther are two very special ones:
	 *
	 * - "title": The title of the page - will also be used for sorting
	 * - "tags":  The tags (or what ever is set in the const.php) will be extracted and stored as an array in the main properties of that page
	 *            The original string will remain in the $data array for seaching
	 */
	public $data = array();

	/**
	 * The Shared data object.
	 */
	public $Shared;

	/**
	 * 	The $tags get also extracted from the text file (see $data).
	 */
	public $tags = array();

	/**
	 * Set main properties.
	 *
	 * @param array $data
	 * @param Shared $Shared
	 */
	public function __construct(array $data, Shared $Shared) {
		$this->data = $data;
		$this->Shared = $Shared;
		$this->tags = $this->extractTags();
	}

	/**
	 * Make basic data items accessible as page properties.
	 *
	 * @param string $key
	 * @return string The returned value from the data array
	 */
	public function __get(string $key) {
		// Map property names to the defined keys of the data array.
		$keyMap = array(
			'hidden' => AM_KEY_HIDDEN,
			'private' => AM_KEY_PRIVATE,
			'level' => AM_KEY_LEVEL,
			'origUrl' => AM_KEY_ORIG_URL,
			'parentUrl' => AM_KEY_PARENT,
			'path' =>AM_KEY_PATH,
			'template' => AM_KEY_TEMPLATE,
			'url' => AM_KEY_URL,
			'index' => AM_KEY_PAGE_INDEX
		);

		if (array_key_exists($key, $keyMap)) {
			return $this->get($keyMap[$key]);
		}

		// Trigger error for undefined properties.
		trigger_error('Page property "' . $key . '" not defined!', E_USER_ERROR);
	}

	/**
	 * Add page.
	 *
	 * @param Page $Parent
	 * @param string $title
	 * @param string $themeTemplate
	 * @param bool $isPrivate
	 * @return string the dashboard URL to the new page
	 */
	public static function add(Page $Parent, string $title, string $themeTemplate, bool $isPrivate) {
		$theme = dirname($themeTemplate);
		$template = basename($themeTemplate);

		// Save new subpage below the current page's path.
		$subdir = Str::slug($title, true, AM_DIRNAME_MAX_LEN);

		// Add trailing slash.
		$subdir .= '/';

		// Build path.
		$newPagePath = $Parent->path . $subdir;
		$suffix = FileSystem::uniquePathSuffix($newPagePath);
		$newPagePath = FileSystem::appendSuffixToPath($newPagePath, $suffix);

		// Data, also directly append possibly existing suffix to title here.
		$data = array(
			AM_KEY_TITLE => $title . ucwords(str_replace('-', ' ', $suffix)),
			AM_KEY_PRIVATE => $isPrivate
		);

		if ($theme != '.') {
			$data[AM_KEY_THEME] = $theme;
		}

		// Set date.
		$data[AM_KEY_DATE] = date('Y-m-d H:i:s');

		// Build the file name and save the txt file.
		$file = FileSystem::fullPagePath($newPagePath) . str_replace('.php', '', $template) . '.' . AM_FILE_EXT_DATA;
		FileSystem::writeData($data, $file);

		PageIndex::append($Parent->path, $newPagePath);
		Cache::clear();

		return Page::dashboardUrlByPath($newPagePath);
	}

	/**
	 * Return updated view URL based on $path.
	 *
	 * @param string $path
	 * @return string The view URL to the new page
	 */
	public static function dashboardUrlByPath(string $path) {
		$Cache = new Cache();
		$Cache->rebuild();

		$Page = Page::findByPath($path);

		return 'page?url=' . urlencode($Page->origUrl);
	}

	/**
	 * Delete page.
	 *
	 * @return bool true on success
	 */
	public function delete() {
		PageIndex::remove(dirname($this->path), $this->path);

		return (bool) FileSystem::movePageDir(
			$this->path,
			'..' . AM_DIR_TRASH . dirname($this->path),
			basename($this->path)
		);
	}

	/**
	 * Duplicate a page.
	 *
	 * @return string the new URL
	 */
	public function duplicate() {
		// Build path and suffix.
		$duplicatePath = $this->path;
		$suffix = FileSystem::uniquePathSuffix($duplicatePath, '-copy');
		$duplicatePath = FileSystem::appendSuffixToPath($duplicatePath, $suffix);

		FileSystem::copyPageFiles($this->path, $duplicatePath);
		Page::appendSuffixToTitle($duplicatePath, $suffix);

		PageIndex::append(dirname($duplicatePath), $duplicatePath);

		Cache::clear();

		return Page::dashboardUrlByPath($duplicatePath);
	}

	/**
	 * Find a page by its path.
	 *
	 * @param string $path
	 * @return Page|null
	 */
	public static function findByPath(string $path) {
		$Automad = Automad::fromCache();

		foreach ($Automad->getCollection() as $url => $Page) {
			if ($Page->path == $path) {
				return $Page;
			}
		}
	}

	/**
	 * Get a page from the cache. In case the cache is outdated, create a new Automad object first.
	 *
	 * @param string $url
	 * @return Page
	 */
	public static function fromCache(string $url) {
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();

		return $Automad->getPage($url);
	}

	/**
	 * Create a new Page object by loading data from a given file.
	 *
	 * @param string $file
	 * @param string $url
	 * @param string $path
	 * @param string $index
	 * @param Shared $Shared
	 * @param string $parentUrl
	 * @param int $level
	 * @return Page
	 */
	public static function fromFile(string $file, string $url, string $path, string $index, Shared $Shared, string $parentUrl, int $level) {
		$data = Parse::dataFile($file);

		if (array_key_exists(AM_KEY_PRIVATE, $data)) {
			$private = ($data[AM_KEY_PRIVATE] && $data[AM_KEY_PRIVATE] !== 'false');
		} else {
			$private = false;
		}

		$data[AM_KEY_PRIVATE] = $private;

		if (!array_key_exists(AM_KEY_TITLE, $data) || ($data[AM_KEY_TITLE] == '')) {
			if (trim($url, '/')) {
				$data[AM_KEY_TITLE] = ucwords(str_replace(array('_', '-'), ' ', basename($url)));
			} else {
				$data[AM_KEY_TITLE] = $Shared->get(AM_KEY_SITENAME);
			}
		}

		if (empty($data[AM_KEY_URL])) {
			$data[AM_KEY_URL] = $url;
		}

		if (array_key_exists(AM_KEY_HIDDEN, $data)) {
			$data[AM_KEY_HIDDEN] = ($data[AM_KEY_HIDDEN] && $data[AM_KEY_HIDDEN] !== 'false');
		} else {
			$data[AM_KEY_HIDDEN] = false;
		}

		$data[AM_KEY_ORIG_URL] = $url;

		$data[AM_KEY_PAGE_INDEX] = $index;
		$data[AM_KEY_PATH] = $path;
		$data[AM_KEY_LEVEL] = $level;
		$data[AM_KEY_PARENT] = $parentUrl;
		$data[AM_KEY_TEMPLATE] = str_replace('.' . AM_FILE_EXT_DATA, '', basename($file));

		return new Page($data, $Shared);
	}

	/**
	 * Return requested data - from the page data array, from the shared data array or as generated system variable.
	 *
	 * The local page data array gets used as first and the shared data array gets used as second source for the requested variable.
	 * That way it is possible to override a shared data value on a per page basis.
	 * Note that not all data is stored in the data arrays.
	 * Some data (:mtime, :basename ...) should only be generated when requested out of performance reasons.
	 *
	 * @param string $key
	 * @return string The requested value
	 */
	public function get(string $key) {
		// Return value from the data array.
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}

		// Return value from the Shared data array.
		if (array_key_exists($key, $this->Shared->data)) {
			return $this->Shared->data[$key];
		}

		// Generate system variable value or return an empty string.
		switch ($key) {
			case AM_KEY_CURRENT_PAGE:
				return $this->isCurrent();
			case AM_KEY_CURRENT_PATH:
				return $this->isInCurrentPath();
			case AM_KEY_BASENAME:
				return basename($this->path);
			case AM_KEY_MTIME:
				return $this->getMtime();
			default:
				return '';
		}
	}

	/**
	 * Return the full file system path of a page's data file.
	 *
	 * @return string The full file system path
	 */
	public function getFile() {
		return FileSystem::fullPagePath($this->path) . $this->template . '.' . AM_FILE_EXT_DATA;
	}

	/**
	 * Get the modification time/date of the page.
	 * To determine to correct mtime, the page directory mtime (to check if any files got added) and the page data file mtime will be checked and the highest value will be returned.
	 *
	 * @return string The max mtime (directory and data file)
	 */
	public function getMtime() {
		$path = AM_BASE_DIR . AM_DIR_PAGES . $this->path;
		$mtimes = array();

		foreach (array($path, $this->getFile()) as $item) {
			if (file_exists($item)) {
				$mtimes[] = date('Y-m-d H:i:s', filemtime($item));
			}
		}

		return max($mtimes);
	}

	/**
	 * Return the template of the page.
	 *
	 * @return string The full file system path of the template file.
	 */
	public function getTemplate() {
		$packages = AM_BASE_DIR . AM_DIR_PACKAGES . '/';
		$templatePath = $packages . $this->get(AM_KEY_THEME) . '/' . $this->template . '.php';

		if (file_exists($templatePath)) {
			return $templatePath;
		} else {
			return $packages . AM_FILE_DEFAULT_TEMPLATE;
		}
	}

	/**
	 * Check if page is the current page.
	 *
	 * @return bool true if the the page is the currently requested page
	 */
	public function isCurrent() {
		return (AM_REQUEST == $this->origUrl);
	}

	/**
	 * Check if the page URL is a part the current page's URL.
	 *
	 * @return bool true if the the page is a parent of the currently requested page or the requeste page itself
	 */
	public function isInCurrentPath() {
		// Test if AM_REQUEST starts with or is equal to $this->url.
		// The trailing slash in strpos() is very important (URL . /), since without that slash,
		// /path/to/page and /path/to/page-1 would both match a current URL like /path/to/page-1/subpage,
		// while /path/to/page/ would not match.
		// Since that will also exculde the current page (it will have the trailing slash more that AM_REQUEST), it has to be testes as well if $this->url equals AM_REQUEST.
		// To always include the homepage as well, rtrim($this->url, '/') avoids a double "//" for the URL "/".
		return (strpos(AM_REQUEST, rtrim($this->origUrl, '/') . '/') === 0 || $this->origUrl == AM_REQUEST);
	}

	/**
	 * Move a page directory and update all related links.
	 *
	 * @param string $destPath
	 * @param string $slug
	 * @return string the new page path
	 */
	public function moveDirAndUpdateLinks(string $destPath, string $slug) {
		$oldPath = $this->path;

		$newPagePath = FileSystem::movePageDir(
			$this->path,
			$destPath,
			$slug
		);

		PageIndex::replace($destPath, $oldPath, $newPagePath);
		$this->updatePageLinks($newPagePath);

		return $newPagePath;
	}

	/**
	 * Save page data.
	 *
	 * @param string $url
	 * @param array $data
	 * @param string $themeTemplate
	 * @param string $slug
	 * @return string a redirect URL in case the page was moved or its privacy has changed
	 */
	public function save(string $url, array $data, string $themeTemplate, string $slug) {
		$data = array_map('trim', $data);
		$data = array_filter($data, 'strlen');

		if (!empty($data[AM_KEY_PRIVATE])) {
			$private = true;
		} else {
			$private = false;
		}

		if (dirname($themeTemplate) != '.') {
			$data[AM_KEY_THEME] = dirname($themeTemplate);
		} else {
			unset($data[AM_KEY_THEME]);
		}

		unlink($this->getFile());

		$newTemplate = Str::stripEnd(basename($themeTemplate), '.php');
		$newPageFile = FileSystem::fullPagePath($this->path) . $newTemplate . '.' . AM_FILE_EXT_DATA;

		FileSystem::writeData($data, $newPageFile);

		if ($url != '/') {
			$newSlug = Page::updateSlug(
				$this->get(AM_KEY_TITLE),
				$data[AM_KEY_TITLE],
				$slug
			);

			$newPagePath = $this->moveDirAndUpdateLinks(
				dirname($this->path),
				$newSlug
			);

			$newSlug = basename($newPagePath);
		} else {
			$newPagePath = '/';
		}

		$newTheme = '';

		if (isset($data[AM_KEY_THEME])) {
			$newTheme = $data[AM_KEY_THEME];
		}

		$currentTheme = '';

		if (isset($this->data[AM_KEY_THEME])) {
			$currentTheme = $this->data[AM_KEY_THEME];
		}

		Cache::clear();

		if ($currentTheme != $newTheme || $this->template != $newTemplate) {
			return array(
				'redirect' => Page::dashboardUrlByPath($newPagePath)
			);
		}

		if ($this->path != $newPagePath ||
			$data[AM_KEY_TITLE] != $this->data[AM_KEY_TITLE] ||
			$newSlug != $slug ||
			$private != $this->private
		) {
			$Cache = new Cache();
			$Automad = $Cache->rebuild();

			$Page = Page::findByPath($newPagePath);

			$newOrigUrl = $Page->origUrl;
			$newUrl = $newOrigUrl;

			if (!empty($data[AM_KEY_URL])) {
				$newUrl = $data[AM_KEY_URL];
			}

			return array(
				'slug' => $newSlug,
				'url' => $newUrl,
				'path' => $newPagePath,
				'origUrl' => $newOrigUrl
			);
		}

		return false;
	}

	/**
	 * Open a data text file under the given path, read the data,
	 * append a suffix to the title variable and write back the data.
	 *
	 * @param string $path
	 * @param string $suffix
	 */
	private static function appendSuffixToTitle(string $path, string $suffix) {
		if ($suffix) {
			$path = FileSystem::fullPagePath($path);
			$files = FileSystem::glob($path . '*.' . AM_FILE_EXT_DATA);

			if (!empty($files)) {
				$file = reset($files);
				$data = Parse::dataFile($file);
				$data[AM_KEY_TITLE] .= ucwords(str_replace('-', ' ', $suffix));
				FileSystem::writeData($data, $file);
			}
		}
	}

	/**
	 * Extracts the tags string out of a given array and returns an array with these tags.
	 *
	 * @return array $tags
	 */
	private function extractTags() {
		$tags = array();

		if (isset($this->data[AM_KEY_TAGS])) {
			// All tags are splitted into an array
			$tags = explode(AM_PARSE_STR_SEPARATOR, $this->data[AM_KEY_TAGS]);
			// Trim & strip tags
			$tags = array_map(function ($tag) {
				return trim(Str::stripTags($tag));
			}, $tags);
		}

		return $tags;
	}

	/**
	 * Update all file and page links based on a new path.
	 *
	 * @param string $newPath
	 * @return bool true on success
	 */
	private function updatePageLinks(string $newPath) {
		$Cache = new Cache();
		$Automad = $Cache->rebuild();
		$oldUrl = $this->origUrl;
		$oldPath = $this->path;

		if ($oldPath == $newPath) {
			return false;
		}

		$Page = Page::findByPath($newPath);
		$newUrl = $Page->origUrl;

		$replace = array(
			rtrim(AM_DIR_PAGES . $oldPath, '/') => rtrim(AM_DIR_PAGES . $newPath, '/'),
			$oldUrl => $newUrl
		);

		foreach ($replace as $old => $new) {
			Links::update($Automad, $old, $new);
		}

		Cache::clear();

		return true;
	}

	/**
	 * Update slug in case it is not a custom one and just represents a sanitized version of the title.
	 *
	 * @param string $currentTitle
	 * @param string $newTitle
	 * @param string $slug
	 * @return string the updated directory name slug
	 */
	private static function updateSlug(string $currentTitle, string $newTitle, string $slug) {
		if (strlen($slug) === 0 || $slug === Str::slug($currentTitle, true, AM_DIRNAME_MAX_LEN)) {
			return Str::slug($newTitle);
		}

		return Str::slug($slug);
	}
}
