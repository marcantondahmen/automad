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
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\GUI\Controllers;

use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\Core\Str;
use Automad\GUI\Components\Layout\PageData;
use Automad\GUI\Utils\FileSystem;
use Automad\GUI\Utils\Text;
use Automad\GUI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Page controller.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Page {


	/**
	 *	Add page based on data in $_POST.
	 *
	 *	@return array $output
	 */

	public static function add() {

		$Automad = UICache::get();
		$output = array();
		$url = Request::post('url');

		// Validation of $_POST. URL, title and template must exist and != false.
		if ($url && ($Page = $Automad->getPage($url))) {

			$subpage = Request::post('subpage');

			if (is_array($subpage) && !empty($subpage['title'])) {

				// Check if the current page's directory is writable.
				if (is_writable(dirname(self::getPageFilePath($Page)))) {

					Debug::log($Page->url, 'page');
					Debug::log($subpage, 'new subpage');

					// The new page's properties.
					$title = $subpage['title'];
					$themeTemplate = self::getTemplateNameFromArray($subpage, 'theme_template');
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
					$newPagePath = $Page->path . $subdir;
					$suffix = FileSystem::uniquePathSuffix($newPagePath);
					$newPagePath = FileSystem::appendSuffixToPath($newPagePath, $suffix);

					// Data. Also directly append possibly existing suffix to title here.
					$data = array(
						AM_KEY_TITLE => $title . ucwords(str_replace('-', ' ', $suffix)),
						AM_KEY_PRIVATE => (!empty($subpage['private']))
					);

					if ($theme != '.') {
						$data[AM_KEY_THEME] = $theme;
					}

					// Set date.
					$data[AM_KEY_DATE] = date('Y-m-d H:i:s');

					// Build the file name and save the txt file. 
					$file = FileSystem::fullPagePath($newPagePath) . str_replace('.php', '', $template) . '.' . AM_FILE_EXT_DATA;
					FileSystem::writeData($data, $file);

					$output['redirect'] = self::contextUrlByPath($newPagePath);

					Cache::clear();

				} else {

					$output['error'] = Text::get('error_permission') . 
									   '<p>' . dirname(self::getPageFilePath($Page)) . '</p>';
				
				}

			} else {

				$output['error'] = Text::get('error_page_title');

			}

		} else {

			$output['error'] = Text::get('error_no_destination');

		}

		return $output;

	}


	/**
	 *	Send form when there is no posted data in the request or save data if there is.
	 *
	 *	@return array the $output array
	 */

	public static function data() {

		$Automad = UICache::get();
		$output = array();
		$url = Request::post('url');

		if ($url && ($Page = $Automad->getPage($url))) {

			// If the posted form contains any "data", save the form's data to the page file.
			if ($data = Request::post('data')) {
				// Save page and replace $output with the returned $output array (error or redirect).
				$output = self::save($Page, $url, $data);
			} else {
				// If only the URL got submitted, just get the form ready.
				$PageData = new PageData($Automad, $Page);
				$output['html'] = $PageData->render();
			}

		}

		return $output;

	}


	/**
	 *	Delete page.
	 *
	 *	@return array $output
	 */

	public static function delete() {

		$Automad = UICache::get();
		$output = array();
		$url = Request::post('url');
		$title = Request::post('title');

		// Validate $_POST.
		if ($url && ($Page = $Automad->getPage($url)) && $url != '/' && $title) {

			// Check if the page's directory and parent directory are wirtable.
			if (is_writable(dirname(self::getPageFilePath($Page))) 
				&& is_writable(dirname(dirname(self::getPageFilePath($Page))))) {

				FileSystem::movePageDir(
					$Page->path, 
					'..' . AM_DIR_TRASH . dirname($Page->path), 
					self::extractPrefixFromPath($Page->path), 
					$title
				);

				$output['redirect'] = '?view=Page&url=' . urlencode($Page->parentUrl);
				Debug::log($Page->url, 'deleted');

				Cache::clear();

			} else {

				$output['error'] = Text::get('error_permission') . 
								   '<p>' . dirname(dirname(self::getPageFilePath($Page))) . '</p>';
			}
		} else {

			$output['error'] = Text::get('error_page_not_found');
		}

		return $output;
		
	}


	/**
	 *	Duplicate a page.
	 *
	 *	@return array $output
	 */

	public static function duplicate() {

		$Automad = UICache::get();
		$output = array();
		$url = Request::post('url');

		if ($url) {

			if ($url != '/' && ($Page = $Automad->getPage($url))) {

				// Check permissions.
				if (is_writable(dirname(FileSystem::fullPagePath($Page->path)))) {

					// Build path and suffix.
					$duplicatePath = $Page->path;
					$suffix = FileSystem::uniquePathSuffix($duplicatePath, '-copy');
					$duplicatePath = FileSystem::appendSuffixToPath($duplicatePath, $suffix);

					// Copy files.
					FileSystem::copyPageFiles($Page->path, $duplicatePath);

					// Append suffix to title variable.
					FileSystem::appendSuffixToTitle($duplicatePath, $suffix);

					// Redirect to new page.
					$output['redirect'] = self::contextUrlByPath($duplicatePath);

					Cache::clear();

				} else {

					$output['error'] = Text::get('error_permission');

				}

			} else {

				$output['error'] = Text::get('error_page_not_found');

			}
		}

		return $output;

	}


	/**
	 *	Extract the deepest directory's prefix from a given path.
	 *
	 *	@param string $path
	 *	@return string Prefix
	 */

	public static function extractPrefixFromPath($path) {

		return substr(basename($path), 0, strpos(basename($path), '.'));

	}


	/**
	 *	Return the full file system path of a page's data file.
	 *
	 *	@param object $Page
	 *	@return string The full file system path
	 */

	public static function getPageFilePath($Page) {

		return FileSystem::fullPagePath($Page->path) . $Page->template . '.' . AM_FILE_EXT_DATA;
		
	}


	/**
	 *	Move a page.
	 *	
	 *	@return array $output
	 */

	public static function move() {

		$Automad = UICache::get();
		$output = array();
		$url = Request::post('url');
		$dest = Request::post('destination');
		$title = Request::post('title');

		// Validation of $_POST. To avoid all kinds of unexpected trouble, 
		// the URL and the destination must exist in the Automad's collection and a title must be present.
		if ($url 
			&& $dest 
			&& $title 
			&& ($Page = $Automad->getPage($url)) 
			&& ($dest = $Automad->getPage($dest))) {

			// The home page can't be moved!	
			if ($url != '/') {

				// Check if new parent directory is writable.
				if (is_writable(FileSystem::fullPagePath($dest->path))) {

					// Check if the current page's directory and parent directory is writable.
					if (is_writable(dirname(self::getPageFilePath($Page))) 
						&& is_writable(dirname(dirname(self::getPageFilePath($Page))))) {

						// Move page
						$newPagePath = FileSystem::movePageDir(
							$Page->path, 
							$dest->path, 
							self::extractPrefixFromPath($Page->path), 
							$title
						);

						$output['redirect'] = self::contextUrlByPath($newPagePath);
						Debug::log($Page->path, 'page');
						Debug::log($dest->path, 'destination');

						Cache::clear();

					} else {

						$output['error'] = Text::get('error_permission') . 
										   '<p>' . dirname(dirname(self::getPageFilePath($Page))) . '</p>';

					}

				} else {

					$output['error'] = Text::get('error_permission') . 
									   '<p>' . FileSystem::fullPagePath($dest->path) . '</p>';

				}

			}

		} else {

			$output['error'] = Text::get('error_no_destination');

		}

		return $output;
	}


	/**
	 *	Return updated view URL based on $path.
	 *
	 *	@param string $path
	 *	@return string The view URL to the new page
	 */

	private static function contextUrlByPath($path) {

		// Rebuild Automad object, since the file structure has changed.
		$Automad = new Automad();
		$Cache = new Cache();
		$Cache->writeAutomadObjectToCache($Automad);

		// Find new URL and return redirect query string.
		foreach ($Automad->getCollection() as $key => $Page) {

			if ($Page->path == $path) {
				// Just return a redirect URL (might be the old URL), 
				// to also reflect the possible renaming in all the GUI's navigation.
				return '?view=Page&url=' . urlencode($key);
			}
			
		}

	}


	/**
	 * 	Get the theme/template file from posted data or return a default template name
	 * 
	 *	@param array $array
	 *	@param string $key
	 *	@return string The template filename
	 */

	private static function getTemplateNameFromArray($array = false, $key = false) {

		$template = 'data.php';

		if (is_array($array) && $key) {
			if (!empty($array[$key])) {
				$template = $array[$key];
			}
		}

		Debug::log($template, 'Template');

		return $template;

	}


	/**
	 *	Save a page.
	 *	
	 *	@param object $Page
	 *	@param string $url
	 *	@param array $data
	 *	@return array $output (AJAX response)
	 */
	
	private static function save($Page, $url, $data) {
		
		$output = array();
	
		// A title is required for building the page's path.
		// If there is no title provided, an error will be returned instead of saving and moving the page.
		if ($data[AM_KEY_TITLE]) {
			
			// Check if the parent directory is writable for all pages but the homepage.
			// Since the directory of the homepage is just "pages" and its parent directory 
			// is the base directory, it should not be necessary to set the base directoy permissions 
			// to 777, since the homepage directory will never be renamed or moved.
			if ($url =='/' || is_writable(dirname(dirname(self::getPageFilePath($Page))))) {
	
				// Check if the page's file and the page's directory is writable.
				if (is_writable(self::getPageFilePath($Page)) 
					&& is_writable(dirname(self::getPageFilePath($Page)))) {
			
					// Trim data.
					$data = array_map('trim', $data);
					
					// Remove empty data.
					// Needs to be done here, to be able to simply test for empty title field.
					$data = array_filter($data, 'strlen');
		
					// Check if privacy has changed to trigger a reload.
					if (isset($data[AM_KEY_PRIVATE]) 
						&& $data[AM_KEY_PRIVATE] 
						&& $data[AM_KEY_PRIVATE] != 'false') {
						$private = true;
					} else {
						$private = false;
					}

					$changedPrivacy = ($private != $Page->private);

					// The theme and the template get passed as theme/template.php combination separate 
					// form $_POST['data']. That information has to be parsed first and "subdivided".
					$themeTemplate = self::getTemplateNameFromArray($_POST, 'theme_template');

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
	
						$prefix = Request::post('prefix');
						$newPagePath = FileSystem::movePageDir(
							$Page->path, 
							dirname($Page->path), 
							$prefix, 
							$data['title']
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
					
					if (($Page->path != $newPagePath) 
						|| ($currentTheme != $newTheme) 
						|| ($Page->template != $newTemplate) 
						|| $changedPrivacy) {
						$output['redirect'] = self::contextUrlByPath($newPagePath);
					} else {
						$output['success'] = Text::get('success_saved');
					}
					
					Cache::clear();
					
				} else {
					
					$output['error'] = Text::get('error_permission');
					
				}
	
			} else {
				
				$output['error'] = Text::get('error_permission');
				
			}
	
		} else {
		
			// If the title is missing, just return an error.
			$output['error'] = Text::get('error_page_title');
		
		}
		
		return $output;
		
	}


}
