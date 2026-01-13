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

namespace Automad\Models;

use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\PageIndex;
use Automad\Core\Parse;
use Automad\Core\PublicationState;
use Automad\Core\Session;
use Automad\Core\Str;
use Automad\Core\Value;
use Automad\Models\History\History;
use Automad\Stores\DataStore;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Page class holds all properties and methods of a single page.
 * A Page object describes an entry in the collection of all pages in the Automad class.
 * Basically the Automad object consists of many Page objects.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Page {
	const TEMPLATE_FILE_DEFAULT = 'standard/light/sidebar_left.php';
	const TEMPLATE_NAME_404 = 'page_not_found';
	const TRASH_DIRECTORY = '/.trash';

	/**
	 * The $data array holds all the information stored in the data json file and
	 * some other system generated information (:path, :level, :template ...).
	 *
	 * The key can be everything alphanumeric as long as there is a matching var set in the template files.
	 * Out of all possible keys ther are two very special ones:
	 *
	 * - "title": The title of the page - will also be used for sorting
	 * - "tags":  The tags (or what ever is set in the const.php) will be extracted and stored as an array in the main properties of that page
	 *            The original string will remain in the $data array for seaching
	 */
	public array $data;

	/**
	 * Page is hidden.
	 */
	public bool $hidden;

	/**
	 * The page index in the layout (sorting order).
	 */
	public string $index;

	/**
	 * The page level.
	 */
	public int $level;

	/**
	 * The internal URL.
	 */
	public string $origUrl;

	/**
	 * The parent URL.
	 */
	public string $parentUrl;

	/**
	 * The filesystem path of the page directory.
	 */
	public string $path;

	/**
	 * Page is private.
	 */
	public bool $private;

	/**
	 * The Shared data object.
	 */
	public ?Shared $Shared;

	/**
	 * 	The $tags get also extracted from the text file (see $data).
	 */
	public array $tags;

	/**
	 * The page template.
	 */
	public string $template;

	/**
	 * The page URL.
	 */
	public string $url;

	/**
	 * Set main properties.
	 *
	 * @param array $data
	 * @param Shared|null $Shared
	 */
	public function __construct(array $data, ?Shared $Shared) {
		$this->Shared = $Shared;

		$this->data = array_merge(array(
			Fields::HIDDEN => false,
			Fields::PRIVATE => false,
			Fields::LEVEL => 0,
			Fields::ORIG_URL => '',
			Fields::PARENT => '',
			Fields::PATH => '',
			Fields::TEMPLATE => '',
			Fields::URL => '',
			Fields::PAGE_INDEX => ''
		), $data);

		$this->tags = $this->extractTags();

		$this->hidden = &$this->data[Fields::HIDDEN];
		$this->private = &$this->data[Fields::PRIVATE];
		$this->level = &$this->data[Fields::LEVEL];
		$this->origUrl = &$this->data[Fields::ORIG_URL];
		$this->parentUrl = &$this->data[Fields::PARENT];
		$this->path = &$this->data[Fields::PATH];
		$this->template = &$this->data[Fields::TEMPLATE];
		$this->url = &$this->data[Fields::URL];
		$this->index = &$this->data[Fields::PAGE_INDEX];
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
	public static function add(Page $Parent, string $title, string $themeTemplate, bool $isPrivate): string {
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
			Fields::TITLE => $title . ucwords(str_replace('-', ' ', $suffix)),
			Fields::PRIVATE => $isPrivate,
			Fields::TEMPLATE => $template,
			Fields::SLUG => basename($newPagePath)
		);

		if ($theme != '.') {
			$data[Fields::THEME] = $theme;
		}

		// Set date.
		$now = date(DataStore::DATE_FORMAT);

		$data[Fields::DATE] = $now;
		$data[Fields::TIME_CREATED] = $now;
		$data[Fields::TIME_LAST_MODIFIED] = $now;

		$DataStore = new DataStore($newPagePath);
		$DataStore->setState(PublicationState::DRAFT, $data)->save();

		PageIndex::append($Parent->path, $newPagePath);

		return Page::dashboardUrlByPath($newPagePath);
	}

	/**
	 * Return updated view URL based on $path.
	 *
	 * @param string $path
	 * @return string The view URL to the new page
	 */
	public static function dashboardUrlByPath(string $path): string {
		Cache::clear();
		$Page = Page::findByPath(rtrim($path, '/') . '/');

		if (!$Page) {
			return '';
		}

		return 'page?url=' . urlencode($Page->origUrl);
	}

	/**
	 * Delete page.
	 *
	 * @return bool true on success
	 */
	public function delete(): bool {
		PageIndex::remove(dirname($this->path), $this->path);

		return (bool) FileSystem::movePageDir(
			$this->path,
			Page::TRASH_DIRECTORY,
			basename($this->path)
		);
	}

	/**
	 * Duplicate a page.
	 *
	 * @return string the new URL
	 */
	public function duplicate(): string {
		$duplicatePath = $this->path;
		$suffix = FileSystem::uniquePathSuffix($duplicatePath, '-copy');
		$duplicatePath = FileSystem::appendSuffixToPath($duplicatePath, $suffix);

		FileSystem::copyPageFiles($this->path, $duplicatePath);
		Page::appendSuffixToTitleAndSlug($duplicatePath, $suffix);
		PageIndex::append(dirname($duplicatePath), $duplicatePath);

		return Page::dashboardUrlByPath($duplicatePath);
	}

	/**
	 * Find a page by its path.
	 *
	 * @param string $path
	 * @return Page|null
	 */
	public static function findByPath(string $path): ?Page {
		$Automad = Automad::fromCache();

		foreach ($Automad->getPages() as $Page) {
			if ($Page->path == $path) {
				return $Page;
			}
		}

		return null;
	}

	/**
	 * Get a page from the cache. In case the cache is outdated, create a new Automad object first.
	 *
	 * @param string $url
	 * @return Page|null
	 */
	public static function fromCache(string $url): ?Page {
		$Automad = Automad::fromCache();

		return $Automad->getPage($url);
	}

	/**
	 * Create a new Page object by loading data from the data file of the given path.
	 *
	 * @param string $path
	 * @param string $url
	 * @param string $index
	 * @param Shared $Shared
	 * @param string $parentUrl
	 * @param int $level
	 * @return ?Page
	 */
	public static function fromDataStore(
		string $path,
		string $url,
		string $index,
		Shared $Shared,
		string $parentUrl,
		int $level
	): ?Page {
		$DataStore = new DataStore($path);
		$data = $DataStore->getState(empty(Session::getUsername()));

		if (empty($data)) {
			return null;
		}

		if (array_key_exists(Fields::PRIVATE, $data)) {
			$data[Fields::PRIVATE] = ($data[Fields::PRIVATE] && $data[Fields::PRIVATE] !== 'false');
		} else {
			$data[Fields::PRIVATE] = false;
		}

		if (!array_key_exists(Fields::TITLE, $data) || ($data[Fields::TITLE] == '')) {
			if (trim($url, '/')) {
				$data[Fields::TITLE] = ucwords(str_replace(array('_', '-'), ' ', basename($url)));
			} else {
				$data[Fields::TITLE] = $Shared->get(Fields::SITENAME);
			}
		}

		if (empty($data[Fields::URL])) {
			$data[Fields::URL] = $url;
		}

		if (array_key_exists(Fields::HIDDEN, $data)) {
			$data[Fields::HIDDEN] = ($data[Fields::HIDDEN] && $data[Fields::HIDDEN] !== 'false');
		} else {
			$data[Fields::HIDDEN] = false;
		}

		if (AM_I18N_ENABLED) {
			$level--;
		}

		$data[Fields::ORIG_URL] = $url;
		$data[Fields::PAGE_INDEX] = $index;
		$data[Fields::PATH] = $path;
		$data[Fields::LEVEL] = $level;
		$data[Fields::PARENT] = $parentUrl;

		return new Page($data, $Shared);
	}

	/**
	 * Return requested data - from the page data array, from the shared data array or as generated system variable.
	 *
	 * The local page data array gets used as first and the shared data array gets used as second source for the requested variable.
	 * That way it is possible to override a shared data value on a per page basis.
	 * Note that not all data is stored in the data arrays.
	 * Some data (:basename ...) should only be generated when requested out of performance reasons.
	 *
	 * @param string $field
	 * @param bool $returnEditorArray
	 * @return array|string The requested value
	 * @psalm-return ($returnEditorArray is true ? array : string)
	 */
	public function get(string $field, bool $returnEditorArray = false): array|string {
		// Return as editor data object from the data array.
		if ($returnEditorArray) {
			if (array_key_exists($field, $this->data)) {
				return Value::asEditorArray($this->data[$field]);
			}

			return Value::asEditorArray($this->Shared->data[$field] ?? null);
		}

		// Return as string value from the data array.
		if (array_key_exists($field, $this->data)) {
			return Value::asString($this->data[$field]);
		}

		// Return value from the Shared data array.
		if ($this->Shared && array_key_exists($field, $this->Shared->data)) {
			return Value::asString($this->Shared->data[$field]);
		}

		// Generate system variable value or return an empty string.
		switch ($field) {
			case Fields::CURRENT_PAGE:
				return $this->isCurrent() ? 'true' : '';
			case Fields::CURRENT_PATH:
				return $this->isInCurrentPath() ? 'true' : '';
			case Fields::BASENAME:
				return basename($this->path);
			default:
				return '';
		}
	}

	/**
	 * Return the full file system path of a page's data file.
	 *
	 * @return string The full file system path
	 */
	public function getFile(): string {
		$DataStore = new DataStore($this->path);

		return $DataStore->getFile();
	}

	/**
	 * Return the template of the page.
	 *
	 * @return string The full file system path of the template file.
	 */
	public function getTemplate(): string {
		$packages = AM_BASE_DIR . AM_DIR_PACKAGES . '/';
		$templatePath = $packages . $this->get(Fields::THEME) . '/' . $this->template . '.php';

		if (file_exists($templatePath)) {
			return $templatePath;
		}

		return $packages . Page::TEMPLATE_FILE_DEFAULT;
	}

	/**
	 * Check if page is the current page.
	 *
	 * @return bool true if the the page is the currently requested page
	 */
	public function isCurrent(): bool {
		return (AM_REQUEST == $this->origUrl);
	}

	/**
	 * Check if the page URL is a part the current page's URL.
	 *
	 * @return bool true if the the page is a parent of the currently requested page or the requeste page itself
	 */
	public function isInCurrentPath(): bool {
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
	 * @param string $destParentPath
	 * @param string $slug
	 * @param array|null $layout
	 * @return string the new page path
	 */
	public function moveDirAndUpdateLinks(string $destParentPath, string $slug, ?array $layout = null): string {
		$oldPath = $this->path;

		$newPath = FileSystem::movePageDir(
			$this->path,
			$destParentPath,
			$slug
		);

		Debug::log(array($oldPath, $newPath, $layout));

		if (dirname($oldPath) !== dirname($newPath)) {
			if (!is_null($layout)) {
				$index = array_search($oldPath, $layout);

				if ($index !== false) {
					$layout[$index] = $newPath;
				}

				PageIndex::write($destParentPath, $layout);
			} else {
				PageIndex::append($destParentPath, $newPath);
			}

			PageIndex::remove(dirname($oldPath), $oldPath);
		} else {
			PageIndex::replace($destParentPath, $oldPath, $newPath);
		}

		$this->updatePageLinks($newPath);

		return $newPath;
	}

	/**
	 * Publish a page.
	 *
	 * @return string|null a new path in case the page has moved or null
	 */
	public function publish(): ?string {
		$DataStore = new DataStore($this->path);
		$draft = $DataStore->getState(PublicationState::DRAFT);

		$DataStore->publish();

		$title = $draft[Fields::TITLE] ?? '';
		$title = $title === '' ? basename($this->path) : $title;
		$slug = $draft[Fields::SLUG] ?? '';
		$newSlug = $draft[Fields::SLUG] ?? '';
		$newPagePath = $this->path;

		if ($this->origUrl != '/') {
			$newSlug = Page::updateSlug(
				$this->get(Fields::TITLE),
				$title,
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

		if ($slug != $newSlug) {
			$DataStore = new DataStore($newPagePath);

			$movedData = $DataStore->getState(PublicationState::PUBLISHED);
			$movedData[Fields::SLUG] = $newSlug;

			$DataStore->setState(PublicationState::PUBLISHED, $movedData)->save();
		}

		Cache::clear();

		if (
			$this->path != $newPagePath ||
			$newSlug != $slug
		) {
			return $newPagePath;
		}

		return null;
	}

	/**
	 * Save page data.
	 *
	 * @param array $data
	 * @param string $themeTemplate
	 * @return array a data array in case the page was moved or its privacy has changed
	 */
	public function save(array $data, string $themeTemplate): array {
		$DataStore = new DataStore($this->path);

		$theme = dirname($themeTemplate);
		$template = basename($themeTemplate);

		$data[Fields::TEMPLATE] = $template;

		if ($theme != '.') {
			$data[Fields::THEME] = $theme;
		}

		$private = !empty($data[Fields::PRIVATE]);
		$data[Fields::PRIVATE] = $private;

		$now = date(DataStore::DATE_FORMAT);

		$data[Fields::TIME_CREATED] = $this->data[Fields::TIME_CREATED] ?? $now;
		$data[Fields::TIME_LAST_MODIFIED] = $now;

		$slug = $data[Fields::SLUG] ?? '';
		$newSlug = $slug;

		if ($this->url != '/' && isset($data[Fields::TITLE])) {
			$newSlug = Page::updateSlug(
				$this->get(Fields::TITLE),
				$data[Fields::TITLE],
				$slug
			);
		}

		$data[Fields::SLUG] = $newSlug;

		$DataStore->setState(PublicationState::DRAFT, $data)
				  ->save();

		$History = History::get($this->path);
		$History->commit($data);

		Cache::clear();

		$newTheme = $data[Fields::THEME] ?? '';
		$currentTheme = $this->data[Fields::THEME] ?? '';

		// Soft reload in order to refresh fields in form.
		if ($currentTheme != $newTheme || $this->template != $template) {
			return array(
				'redirect' => Page::dashboardUrlByPath($this->path)
			);
		}

		if ($private != $this->private || $newSlug != $slug || $this->get(Fields::TITLE) != ($data[Fields::TITLE] ?? '')) {
			return array(
				'updateUI' => true,
				'slug' => $newSlug
			);
		}

		return array();
	}

	/**
	 * Set a page data value.
	 *
	 * @param string $field
	 * @param mixed $value
	 */
	public function set(string $field, mixed $value): void {
		$this->data[$field] = $value;
	}

	/**
	 * Create an empty undefined Page object.
	 *
	 * @return Page
	 */
	public static function undefined(): Page {
		return new Page(array(), null);
	}

	/**
	 * Update a single field.
	 *
	 * @param string $field
	 * @param mixed $value
	 */
	public function updateField(string $field, mixed $value): void {
		$DataStore = new DataStore($this->path);
		$draft = $DataStore->getState(PublicationState::DRAFT);

		if ($field == Fields::TITLE && $this->origUrl != '/') {
			$draft[Fields::SLUG] = self::updateSlug($draft[Fields::TITLE] ?? '', $value, $draft[Fields::SLUG] ?? '');
		}

		$draft[$field] = $value;

		$DataStore->setState(PublicationState::DRAFT, $draft)
				  ->save();

		$History = History::get($this->path);
		$History->commit($draft);

		Cache::clear();
	}

	/**
	 * Open a data text file under the given path, read the data,
	 * append a suffix to the title variable and write back the data.
	 *
	 * @param string $path
	 * @param string $suffix
	 */
	private static function appendSuffixToTitleAndSlug(string $path, string $suffix): void {
		if ($suffix) {
			$DataStore = new DataStore($path);
			$data = $DataStore->getState(PublicationState::DRAFT);

			$title = $data[Fields::TITLE] ?? basename($path);
			$data[Fields::TITLE] = $title . ucwords(str_replace('-', ' ', $suffix));

			$slug = Page::updateSlug(
				$title,
				$data[Fields::TITLE],
				''
			);

			$data[Fields::SLUG] = $slug;

			$DataStore->setState(PublicationState::DRAFT, $data)->save();
		}
	}

	/**
	 * Extracts the tags string out of a given array and returns an array with these tags.
	 *
	 * @return array $tags
	 */
	private function extractTags(): array {
		$tags = array();

		if (isset($this->data[Fields::TAGS])) {
			// All tags are splitted into an array
			$tags = explode(Parse::STRING_SEPARATOR, $this->data[Fields::TAGS]);
			// Trim & strip tags
			$tags = array_map(function (string $tag) {
				return trim(Str::stripTags($tag));
			}, $tags);

			sort($tags);
		}

		return $tags;
	}

	/**
	 * Update all file and page links based on a new path.
	 *
	 * @param string $newPath
	 * @return bool true on success
	 */
	private function updatePageLinks(string $newPath): bool {
		Cache::clear();

		$Automad = Automad::fromCache();
		$oldUrl = $this->origUrl;
		$oldPath = $this->path;

		if ($oldPath == $newPath) {
			return false;
		}

		$Page = Page::findByPath($newPath);

		if (!$Page) {
			return false;
		}

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
	private static function updateSlug(string $currentTitle, string $newTitle, string $slug): string {
		if (strlen($slug) === 0 || $slug === Str::slug($currentTitle, true, AM_DIRNAME_MAX_LEN)) {
			return Str::slug($newTitle);
		}

		return Str::slug($slug);
	}
}
