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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Models;

use Automad\Core\Cache;
use Automad\Core\Str;
use Automad\UI\Models\Search\FileKeys;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Page model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Page {
	/**
	 * Add page.
	 *
	 * @param \Automad\Core\Page $Parent
	 * @param string $title
	 * @param string $themeTemplate
	 * @param boolean $isPrivate
	 * @return string the URL to the new page
	 */
	public static function add($Parent, $title, $themeTemplate, $isPrivate) {
		$theme = dirname($themeTemplate);
		$template = basename($themeTemplate);

		// Save new subpage below the current page's path.
		$subdir = Str::sanitize($title, true, AM_DIRNAME_MAX_LEN);

		// If $subdir is an empty string after sanitizing, set it to 'untitled'.
		if (!$subdir) {
			$subdir = 'untitled';
		}

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

		Cache::clear();

		return self::contextUrlByPath($newPagePath);
	}

	/**
	 * Return updated view URL based on $path.
	 *
	 * @param string $path
	 * @return string The view URL to the new page
	 */
	public static function contextUrlByPath($path) {
		// Rebuild Automad object, since the file structure has changed.
		return '?view=Page&url=' . urlencode(self::urlByPath(UICache::rebuild(), $path));
	}

	/**
	 * Delete page.
	 *
	 * @param \Automad\Core\Page $Page
	 * @param string $title
	 * @return boolean true on success
	 */
	public static function delete($Page, $title) {
		return FileSystem::movePageDir(
			$Page->path,
			'..' . AM_DIR_TRASH . dirname($Page->path),
			self::extractPrefixFromPath($Page->path),
			$title
		);
	}

	/**
	 * Duplicate a page.
	 *
	 * @param \Automad\Core\Page $Page
	 * @return string the new URL
	 */
	public static function duplicate($Page) {
		// Build path and suffix.
		$duplicatePath = $Page->path;
		$suffix = FileSystem::uniquePathSuffix($duplicatePath, '-copy');
		$duplicatePath = FileSystem::appendSuffixToPath($duplicatePath, $suffix);

		FileSystem::copyPageFiles($Page->path, $duplicatePath);
		FileSystem::appendSuffixToTitle($duplicatePath, $suffix);

		Cache::clear();

		return self::contextUrlByPath($duplicatePath);
	}

	/**
	 * Extract the deepest directory's prefix from a given path.
	 *
	 * @param string $path
	 * @return string Prefix
	 */
	public static function extractPrefixFromPath($path) {
		return substr(basename($path), 0, strpos(basename($path), '.'));
	}

	/**
	 * Return the full file system path of a page's data file.
	 *
	 * @param object $Page
	 * @return string The full file system path
	 */
	public static function getPageFilePath($Page) {
		return FileSystem::fullPagePath($Page->path) . $Page->template . '.' . AM_FILE_EXT_DATA;
	}

	/**
	 * Move a page directory and update all related links.
	 *
	 * @param \Automad\Core\Page $Page
	 * @param string $destPath
	 * @param string $prefix
	 * @param string $title
	 * @return string the new page path
	 */
	public static function moveDirAndUpdateLinks($Page, $destPath, $prefix, $title) {
		$newPagePath = FileSystem::movePageDir(
			$Page->path,
			$destPath,
			$prefix,
			$title
		);

		self::updatePageLinks($Page, $newPagePath);

		return $newPagePath;
	}

	/**
	 * Save page data.
	 *
	 * @param \Automad\Core\Page $Page
	 * @param string $url
	 * @param array $data
	 * @param string $themeTemplate
	 * @param string $prefix
	 * @return string a redirect URL in case the page was moved or its privacy has changed
	 */
	public static function save($Page, $url, $data, $themeTemplate, $prefix) {
		// Trim data.
		$data = array_map('trim', $data);

		// Remove empty data.
		// Needs to be done here, to be able to simply test for empty title field.
		$data = array_filter($data, 'strlen');

		// Check if privacy has changed to trigger a reload.
		if (!empty($data[AM_KEY_PRIVATE])) {
			$private = true;
		} else {
			$private = false;
		}

		$changedPrivacy = ($private != $Page->private);

		// Get correct theme name.
		// If the theme is not set and there is no slash passed within 'theme_template',
		// the resulting dirname is just a dot.
		// In that case, $data[AM_KEY_THEME] gets removed (no theme - use site theme).
		if (dirname($themeTemplate) != '.') {
			$data[AM_KEY_THEME] = dirname($themeTemplate);
		} else {
			unset($data[AM_KEY_THEME]);
		}

		// Delete old (current) file, in case, the template has changed.
		unlink(self::getPageFilePath($Page));

		// Build the path of the data file by appending
		// the basename of 'theme_template' to $Page->path.
		$newTemplate = Str::stripEnd(basename($themeTemplate), '.php');
		$newPageFile = FileSystem::fullPagePath($Page->path) . $newTemplate . '.' . AM_FILE_EXT_DATA;

		// Save new file within current directory, even when the prefix/title changed.
		// Renaming/moving is done in a later step, to keep files and subpages
		// bundled to the current text file.
		FileSystem::writeData($data, $newPageFile);

		// If the page is not the homepage,
		// rename the page's directory including all children and all files, after
		// saving according to the (new) title and prefix.
		// FileSystem::movePageDir() will check if renaming is needed, and will
		// skip moving, when old and new path are equal.
		if ($url != '/') {
			$newPagePath = self::moveDirAndUpdateLinks(
				$Page,
				dirname($Page->path),
				$prefix,
				$data[AM_KEY_TITLE]
			);
		} else {
			// In case the page is the home page, the path is just '/'.
			$newPagePath = '/';
		}

		// Check whether the dashboard has to be redirected.
		// Only in case the page path (title and prefix) or the theme/template has changed,
		// the page has to be redirected to update the site tree and variables.
		$newTheme = '';

		if (isset($data[AM_KEY_THEME])) {
			$newTheme = $data[AM_KEY_THEME];
		}

		$currentTheme = '';

		if (isset($Page->data[AM_KEY_THEME])) {
			$currentTheme = $Page->data[AM_KEY_THEME];
		}

		Cache::clear();

		if (($Page->path != $newPagePath)
						|| ($currentTheme != $newTheme)
						|| ($Page->template != $newTemplate)
						|| $changedPrivacy) {
			return self::contextUrlByPath($newPagePath);
		}

		return false;
	}

	/**
	 * Update all file and page links based on a new path.
	 *
	 * @param \Automad\Core\Page $Page
	 * @param string $path
	 * @param mixed $newPath
	 * @return boolean true on success
	 */
	private static function updatePageLinks($Page, $newPath) {
		Cache::clear();

		$Automad = UICache::rebuild();
		$oldUrl = $Page->origUrl;
		$oldPath = $Page->path;

		if ($oldPath == $newPath) {
			return false;
		}

		$newUrl = self::urlByPath($Automad, $newPath);

		$replace = array(
			rtrim(AM_DIR_PAGES . $oldPath, '/') => rtrim(AM_DIR_PAGES . $newPath, '/'),
			$oldUrl => $newUrl
		);

		foreach ($replace as $old => $new) {
			$searchValue = '(?<=^|"|\(|\s)' . preg_quote($old) . '(?="|/|,|\?|#|\s|$)';
			$replaceValue = $new;

			$Search = new Search($Automad, $searchValue, true, false);
			$fileResultsArray = $Search->searchPerFile();
			$fileKeysArray = array();

			foreach ($fileResultsArray as $FileResults) {
				$keys = array();

				foreach ($FileResults->fieldResultsArray as $FieldResults) {
					$keys[] = $FieldResults->key;
				}

				$fileKeysArray[] = new FileKeys($FileResults->path, $keys);
			}

			$Replacement = new Replacement($searchValue, $replaceValue, true, false);
			$Replacement->replaceInFiles($fileKeysArray);
		}

		Cache::clear();

		return true;
	}

	/**
	 * Return updated page URL based on $path.
	 *
	 * @param \Automad\Core\Automad $Automad
	 * @param string $path
	 * @return string The page URL
	 */
	private static function urlByPath($Automad, $path) {
		// Find new URL and return redirect query string.
		foreach ($Automad->getCollection() as $url => $Page) {
			if ($Page->path == $path) {
				return $url;
			}
		}
	}
}
