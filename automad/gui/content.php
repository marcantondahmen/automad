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
 *	Copyright (c) 2016-2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Content class provides all methods to add, modify, move or delete content (pages, shared data and files). 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2018 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Content {

	
	/**
	 *	The Automad object.
	 */

	private $Automad;


	/**
	 *  The Html object.
	 */
	
	private $Html;
	
	
	/**
	 *	Set $this->Automad when creating an instance.
	 *
	 *	@param object $Automad
	 */
	
	public function __construct($Automad) {
		
		$this->Automad = $Automad;
		$this->Html = new Html($Automad);
		
	}
	
	
	/**
	 *	Add page based on $_POST.
	 *
	 *	@return array $output (AJAX response)
	 */
	
	public function addPage() {
		
		$output = array();
		
		// Validation of $_POST. URL, title and template must exist and != false.
		if (isset($_POST['url']) && ($Page = $this->Automad->getPage($_POST['url']))) {
	
			if (isset($_POST['subpage']) && isset($_POST['subpage']['title']) && $_POST['subpage']['title'] && isset($_POST['subpage']['theme_template']) && $_POST['subpage']['theme_template']) {
		
				// Check if the current page's directory is writable.
				if (is_writable(dirname($this->getPageFilePath($Page)))) {
	
					Core\Debug::ajax($output, 'page', $Page->url);
					Core\Debug::ajax($output, 'new subpage', $_POST['subpage']);
	
					// The new page's properties.
					$title = $_POST['subpage']['title'];
					$theme_template = $_POST['subpage']['theme_template'];
					$theme = dirname($theme_template);
					$template = basename($theme_template);
					
					// Save new subpage below the current page's path.		
					$subdir = Core\Str::sanitize($title, true, AM_DIRNAME_MAX_LEN);
					
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
					$data = array(AM_KEY_TITLE => $title . ucwords(str_replace('-', ' ', $suffix)));
					
					if ($theme != '.') {
						$data[AM_KEY_THEME] = $theme;
					}
					
					// Build the file name and save the txt file. 
					$file = FileSystem::fullPagePath($newPagePath) . str_replace('.php', '', $template) . '.' . AM_FILE_EXT_DATA;
					FileSystem::writeData($data, $file);
					
					$output['redirect'] = $this->contextUrlByPath($newPagePath);
					
					$this->clearCache();
		
				} else {
			
					$output['error'] = Text::get('error_permission') . '<p>' . dirname($this->getPageFilePath($Page)) . '</p>';
			
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
	 *	Clear the page cache.
	 */
	
	private function clearCache() {
		
		$Cache = new Core\Cache();
		$Cache->clear();
		
	}
	
	
	/**
	 *	Copy an image resized based on $_POST.
	 *
	 *	@return array $output (AJAX response)
	 */
	
	public function copyResized() {
		
		$output = array();
		
		$options = array_merge(
			array(
				'url' => '',
				'filename' => '',
				'width' => false,
				'height' => false,
				'crop' => false
			),
			$_POST
		);
		
		$width = $options['width'];
		$height = $options['height'];
		
		if (!((is_numeric($width) || is_bool($width)) && (is_numeric($height) || is_bool($height)))) {
			$output['error'] = Text::get('error_file_size');
			return $output;
		}
		
		if ($options['filename']) {
		
			// Get parent directory.
			if ($options['url']) {
				$Page = $this->Automad->getPage($options['url']);
				$directory = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
			} else {
				$directory = AM_BASE_DIR . AM_DIR_SHARED . '/';
			}
		
			$file = $directory . $options['filename'];
		
			Core\Debug::ajax($output, 'file', $file);
			Core\Debug::ajax($output, 'options', $options);
				
			if (file_exists($file)) {
				
				if (is_writable($directory)) {
					
					$img = new Core\Image(
						$file, 
						$width, 
						$height,
						boolval($options['crop'])
					);
					
					$cachedFile = AM_BASE_DIR . $img->file;
					$resizedFile = preg_replace('/(\.\w{3,4})$/', '-' . floor($img->width) . 'x' . floor($img->height) . '$1', $file);
					
					if (!$output['error'] = FileSystem::renameMedia($cachedFile, $resizedFile)) {
						$output['success'] = Text::get('success_created') . ' "' . basename($resizedFile) . '"';
					}
					
				} else {
					$output['error'] = Text::get('error_permission') . ' "' . $directory . '"';
				}
				
			} else {
				$output['error'] = Text::get('error_file_not_found');
			}
		
		}
		
		return $output;
		
	}
	
	
	/**
	 *	Delete files.
	 *
	 *	@param array $files
	 *	@param string $path
	 *	@return array $output (AJAX response)
	 */
	
	public function deleteFiles($files, $path) {
		
		$output = array();
		
		// Check if directory is writable.
		if (is_writable($path)) {
	
			$success = array();
			$errors = array();
	
			foreach ($files as $f) {
		
				// Make sure submitted filename has no '../' (basename).
				$file = $path . basename($f);
		
				if ($error = FileSystem::deleteMedia($file)) {
					$errors[] = $error;
				} else {
					$success[] = '"' . basename($file) . '"';
				}
				
			}
	
			$this->clearCache();
	
			$output['success'] = Text::get('success_remove') . '<br />' . implode('<br />', $success);
			$output['error'] = implode('<br />', $errors);

		} else {
		
			$output['error'] = Text::get('error_permission') . ' "' . basename($path) . '"';
		
		}
		
		return $output;
		
	}
	
	
	/**
	 *	Delete page based on $_POST.
	 *
	 *	@return array $output (AJAX response)
	 */
	
	public function deletePage() {
		
		$output = array();

		// Validate $_POST.
		if (isset($_POST['url']) && ($Page = $this->Automad->getPage($_POST['url'])) && $_POST['url'] != '/' && !empty($_POST['title'])) {

			// Check if the page's directory and parent directory are wirtable.
			if (is_writable(dirname($this->getPageFilePath($Page))) && is_writable(dirname(dirname($this->getPageFilePath($Page))))) {

				FileSystem::movePageDir($Page->path, '..' . AM_DIR_TRASH . dirname($Page->path), $this->extractPrefixFromPath($Page->path), $_POST['title']);
				$output['redirect'] = '?context=edit_page&url=' . urlencode($Page->parentUrl);
				Core\Debug::ajax($output, 'deleted', $Page->url);

				$this->clearCache();

			} else {
		
				$output['error'] = Text::get('error_permission') . '<p>' . dirname(dirname($this->getPageFilePath($Page))) . '</p>';
		
			}
	
		} else {
			
			$output['error'] = Text::get('error_page_not_found');
			
		}
		
		return $output;
		
	}
	
	
	/**
	 *	Duplicate a page based on $_POST.
	 *      
	 * 	@return array $output (AJAX response) 
	 */
	
	public function duplicatePage() {
		
		$output = array();
		
		if (!empty($_POST['url'])) {
			
			$url = $_POST['url'];
			
			if ($url != '/' && ($Page = $this->Automad->getPage($url))) {
				
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
					$output['redirect'] = $this->contextUrlByPath($duplicatePath);
					
					$this->clearCache();
					
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
	 *	Edit file information (file name and caption) based on $_POST.
	 *	
	 *	@return array $output (AJAX response)
	 */
	
	public function editFileInfo() {
		
		$output = array();

		if (!empty($_POST['old-name']) && !empty($_POST['new-name']) && isset($_POST['caption'])) {
	
			if ($_POST['new-name']) {
				
				$path = $this->getPathByPostUrl();
				$oldFile = $path . basename($_POST['old-name']);
				$newFile = $path . Core\Str::sanitize(basename($_POST['new-name']));
				$caption = $_POST['caption'];
				
				if (FileSystem::isAllowedFileType($newFile)) {
					
					// Rename file and caption if needed.
					if ($newFile != $oldFile) {
						$output['error'] = FileSystem::renameMedia($oldFile, $newFile);
					}
					
					// Write caption.
					if (empty($output['error'])) {
						
						$newCaptionFile = $newFile . '.' . AM_FILE_EXT_CAPTION;
						
						// Only if file exists already or $caption is empty.
						if (is_writable($newCaptionFile) || !file_exists($newCaptionFile)) {
							FileSystem::write($newCaptionFile, $caption);
						} else {
							$output['error'] = Text::get('error_file_save') . ' "' . basename($newCaptionFile) . '"';
						}
						
					}
				
					$this->clearCache();
					 
				} else {
			
					$output['error'] = Text::get('error_file_format') . ' "' . FileSystem::getExtension($newFile) . '"';
				
				}
				
			} else {
				
				$output['error'] = Text::get('error_file_name');
				
			}
	
		} else {
			
			$output['error'] = Text::get('error_form');
			
		}

		return $output;
				
	}


	/**
	 *	Extract the deepest directory's prefix from a given path.
	 *
	 * 	@param string $path
	 *	@return string Prefix
	 */

	public function extractPrefixFromPath($path) {
		
		return substr(basename($path), 0, strpos(basename($path), '.'));
			
	}
	
	
	/**
	 *	Return a JSON formatted string to be used as autocomplete infomation in a search field.
	 *	
	 *	The collected data consists of all page titles, URLs and all available tags.
	 *
	 *	@return string The JSON encoded autocomplete data
	 */
	
	public function getAutoCompleteJSON() {
		
		$titles = array();
		$urls = array();
		$tags = array();
		$values = array();
		
		foreach ($this->Automad->getCollection() as $Page) {
			$titles[] = $Page->get(AM_KEY_TITLE);
			$urls[] = $Page->origUrl;
			$tags = array_merge($tags, $Page->tags);
		}
		
		$titles = array_unique($titles);
		$tags = array_unique($tags);
		
		// Sort arrays separately to keep titles, urls and tags grouped.
		sort($titles);
		sort($tags);
		sort($urls);
		
		foreach (array_merge($titles, $tags, $urls) as $value) {
			$values[]['value'] = $value;
		}
		
		return json_encode($values, JSON_UNESCAPED_SLASHES);
		
	}
	
	
	/**
	 *	Return the full file system path of a page's data file.
	 *
	 *	@param object $Page
	 *	@return string The full file system path
	 */

	public function getPageFilePath($Page) {
		
		return FileSystem::fullPagePath($Page->path) . $Page->template . '.' . AM_FILE_EXT_DATA;
	
	}


	/**
	 *  Return the file system path for the directory of a page based on $_POST['url'].   
	 *  In case URL is empty, return the '/shared' directory.
	 *      
	 *  @return string The full path to the related directory
	 */
	
	public function getPathByPostUrl() {
		
		if (isset($_POST['url']) && ($Page = $this->Automad->getPage($_POST['url']))) {
			return FileSystem::fullPagePath($Page->path);
		} else {
			return AM_BASE_DIR . AM_DIR_SHARED . '/';
		}
		
	}


	/**
	 *	Get results for a search query from $_GET. In case there is only one match, redirect to the edit page for that URL. 
	 *
	 *	@return array The matching pages 
	 */

	public function getSearchResults() {
		
		$pages = array();
	
		if ($query = Core\Parse::query('query')) {
		
			$collection = $this->Automad->getCollection();
		
			if (array_key_exists($query, $collection)) {
			
				// If $query matches an actual URL of an existing page, just get that page to be the only match in the $pages array.
				// Since $pages has only one element, the request gets directly redirected to the edit page (see below).
				$pages = array($this->Automad->getPage($query));
							
			} else {
			
				$Selection = new Core\Selection($collection);
				$Selection->filterByKeywords($query);
				$Selection->sortPages(AM_KEY_MTIME . ' desc');
				$pages = $Selection->getSelection(false);
			
			}
	
			// Redirect to edit mode for a single result or in case $query represents an actually existing URL.
			if (count($pages) == 1) {
				$Page = reset($pages);
				header('Location: ' . AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?context=edit_page&url=' . urlencode($Page->url));
				die;	
			}
		
		}
		
		return $pages;
		
	}
	
	
	/**
	 *	Handle AJAX request for editing a data variable in-page context.   
	 *          
	 *  If no data gets received, form fields to build up the editing dialog are send back. 
	 *  Else the received data gets merged with the full data array of the requested context and 
	 *  saved back into the .txt file. 
	 *  In case the title variable gets modified, the page directory gets renamed accordingly.
	 *
	 *  @return array $output (AJAX response)
	 */
	
	public function inPageEdit() {
		
		$output = array();
		
		if (!empty($_POST['context'])) {
		
			// Check if page actually exists.
			if ($Page = $this->Automad->getPage($_POST['context'])) {
				
				// If data gets received, merge and save.
				// Else send back form fields.
				if (isset($_POST['data'], $_POST['url']) && is_array($_POST['data'])) {
					
					// Merge and save data.
					$data = array_merge(Core\Parse::textFile($this->getPageFilePath($Page)), $_POST['data']);
					FileSystem::writeData($data, $this->getPageFilePath($Page));
					Core\Debug::ajax($output, 'saved data', $data);
					Core\Debug::ajax($output, 'data file', $this->getPageFilePath($Page));
					
					// If the title has changed, the page directory has to be renamed as long as it is not the home page.
					if (!empty($_POST['data'][AM_KEY_TITLE]) && $Page->url != '/') {
						
						// Move directory.
						$newPagePath = FileSystem::movePageDir(
							$Page->path, 
							dirname($Page->path), 
							$this->extractPrefixFromPath($Page->path), 
							$_POST['data'][AM_KEY_TITLE]
						);
						
						Core\Debug::ajax($output, 'renamed page', $newPagePath);
						
					}
					
					// Clear cache to reflect changes.
					$this->clearCache();
					
					// If the page directory got renamed, find the new URL.
					if ($Page->url == $_POST['url'] && isset($newPagePath)) {
						
						// The page has to be redirected to a new url in case the edited context is actually 
						// the requested page and the title of the page and therefore the URL has changed.
						
						// Rebuild Automad object, since the file structure has changed.
						$Automad = new Core\Automad();
						
						// Find new URL and return redirect URL.
						foreach ($Automad->getCollection() as $key => $Page) {

							if ($Page->path == $newPagePath) {
								$output['redirect'] = AM_BASE_INDEX . $key;
								break;
							}

						}
							
					} else {
						
						// There are two cases where the currently requested page has to be
						// simply reloaded without redirection:
						// 
						// 1.	The context of the edits is not the current page and another
						// 		pages gets actually edited.
						// 		That would be the case for edits of pages displayed in pagelists or menus.
						// 	
						// 2.	The context is the current page, but the title didn't change and
						// 		therefore the URL stays the same.
						$output['redirect'] = AM_BASE_INDEX . $_POST['url'];
						
					}
					
					// Append query string if not empty.
					if (!empty($_POST['query'])) {
						$output['redirect'] .= '?' . $_POST['query'];
					}
								
				} else {
					
					// Return form fields if key is defined.
					if (!empty($_POST['key'])) {
						
						$value = '';
						
						if (!empty($Page->data[$_POST['key']])) {
							$value = $Page->data[$_POST['key']];
						}
						
						$output['html'] = '<div id="am-inpage-edit-fields">' .
										  '<input type="hidden" name="context" value="' . $_POST['context'] . '" />' .
										  $this->Html->formField($_POST['key'], $value) . 
										  '</div>';
						
					}
			
				}
				
			}
				
		}
		
		return $output;
	
	}
	
	
	/**
	 *	Move a page based on $_POST.
	 *	
	 *	@return array $output (AJAX response)
	 */
	
	public function movePage() {
		
		$output = array();

		// Validation of $_POST.
		// To avoid all kinds of unexpected trouble, the URL and the destination must exist in the Automad's collection and a title must be present.
		if (isset($_POST['url']) && isset($_POST['destination']) && !empty($_POST['title']) && ($Page = $this->Automad->getPage($_POST['url'])) && ($dest = $this->Automad->getPage($_POST['destination']))) {
	
			// The home page can't be moved!	
			if ($_POST['url'] != '/') {
		
				// Check if new parent directory is writable.
				if (is_writable(FileSystem::fullPagePath($dest->path))) {
	
					// Check if the current page's directory and parent directory is writable.
					if (is_writable(dirname($this->getPageFilePath($Page))) && is_writable(dirname(dirname($this->getPageFilePath($Page))))) {
	
						// Move page
						$newPagePath = FileSystem::movePageDir($Page->path, $dest->path, $this->extractPrefixFromPath($Page->path), $_POST['title']);	
						$output['redirect'] = $this->contextUrlByPath($newPagePath);
						Core\Debug::ajax($output, 'page', $Page->path);
						Core\Debug::ajax($output, 'destination', $dest->path);
						
						$this->clearCache();
		
					} else {
				
						$output['error'] = Text::get('error_permission') . '<p>' . dirname(dirname($this->getPageFilePath($Page))) . '</p>';
				
					}
		
				} else {
			
					$output['error'] = Text::get('error_permission') . '<p>' . FileSystem::fullPagePath($dest->path) . '</p>';
			
				}
			
			} 	

		} else {
	
			$output['error'] = Text::get('error_no_destination'); 
	
		}
		
		return $output;
		
	}
	
	
	/**
	 *	Return updated context URL based on $path.
	 *      
	 *  @param string $path
	 *  @return string The context URL to the new page
	 */
	
	private function contextUrlByPath($path) {
		
		// Rebuild Automad object, since the file structure has changed.
		$Automad = new Core\Automad();
		
		// Find new URL and return redirect query string.
		foreach ($Automad->getCollection() as $key => $Page) {

			if ($Page->path == $path) {

				// Just return a redirect URL (might be the old URL), to also reflect the possible renaming in all the GUI's navigation.
				return '?context=edit_page&url=' . urlencode($key);

			}

		}
		
	}
	
	
	/**
	 *	Save a page.
	 *	
	 *	@param string $url
	 *	@param array $data
	 *	@return array $output (AJAX response)
	 */
	
	public function savePage($url, $data) {
		
		$output = array();
		$Page = $this->Automad->getPage($url);
	
		// A title is required for building the page's path.
		// If there is no title provided, an error will be returned instead of saving and moving the page.
		if ($data[AM_KEY_TITLE]) {
			
			// Check if the parent directory is writable for all pages but the homepage.
			// Since the directory of the homepage is just "pages" and its parent directory is the base directory,
			// it should not be necessary to set the base directoy permissions to 777, since the homepage directory will never be renamed or moved.
			if ($url =='/' || is_writable(dirname(dirname($this->getPageFilePath($Page))))) {
	
				// Check if the page's file and the page's directory is writable.
				if (is_writable($this->getPageFilePath($Page)) && is_writable(dirname($this->getPageFilePath($Page)))) {
			
					// Trim data.
					$data = array_map('trim', $data);
					
					// Remove empty data.
					// Needs to be done here, to be able to simply test for empty title field.
					$data = array_filter($data, 'strlen');
		
					// Set hidden parameter within the $data array. 
					// Since it is a checkbox, it must get parsed separately.
					if (isset($_POST[AM_KEY_HIDDEN])) {
						$data[AM_KEY_HIDDEN] = 1;
					}
	
					// The theme and the template get passed as theme/template.php combination separate form $_POST['data']. 
					// That information has to be parsed first and "subdivided".

					// Get correct theme name.
					// If the theme is not set and there is no slash passed within 'theme_template', the resulting dirname is just a dot.
					// In that case, $data[AM_KEY_THEME] gets removed (no theme - use site theme). 
					if (dirname($_POST['theme_template']) != '.') {
						$data[AM_KEY_THEME] = dirname($_POST['theme_template']);
					} else {
						unset($data[AM_KEY_THEME]);
					}

					// Delete old (current) file, in case, the template has changed.
					unlink($this->getPageFilePath($Page));

					// Build the path of the data file by appending the basename of 'theme_template' to $Page->path.
					$newTemplate = Core\Str::stripEnd(basename($_POST['theme_template']), '.php');
					$newPageFile = FileSystem::fullPagePath($Page->path) . $newTemplate . '.' . AM_FILE_EXT_DATA;
					
					// Save new file within current directory, even when the prefix/title changed. 
					// Renaming/moving is done in a later step, to keep files and subpages bundled to the current text file.
					FileSystem::writeData($data, $newPageFile);

					// If the page is not the homepage, 
					// rename the page's directory including all children and all files, after saving according to the (new) title and prefix.
					// FileSystem::movePageDir() will check if renaming is needed, and will skip moving, when old and new path are equal.
					if ($url != '/') {
	
						if (!isset($_POST['prefix'])) {
							$prefix = '';
						} else {
							$prefix = $_POST['prefix'];
						}

						$newPagePath = FileSystem::movePageDir($Page->path, dirname($Page->path), $prefix, $data['title']);
	
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
					
					if (($Page->path != $newPagePath) || ($currentTheme != $newTheme) || ($Page->template != $newTemplate)) {
						$output['redirect'] = $this->contextUrlByPath($newPagePath);
					} else {
						$output['success'] = Text::get('success_saved');
					}
					
					$this->clearCache();
					
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
	

	/**
	 *	Save shared data.
	 *
	 *	@param array $data
	 *	@return array $output (AJAX response)
	 */
	
	public function saveSharedData($data) {
		
		$output = array();
	
		if (is_writable(AM_FILE_SHARED_DATA)) {
			FileSystem::writeData($data, AM_FILE_SHARED_DATA);
			$this->clearCache();
			$output['success'] = Text::get('success_saved');
		} else {
			$output['error'] = Text::get('error_permission') . '<br /><small>' . AM_FILE_SHARED_DATA . '</small>';
		}

		return $output;
		
	}
	
	
	/**
	 *	Upload handler based on $_POST and $_FILES.
	 *
	 *	@return array $output (AJAX response)
	 */
	
	public function upload() {
		
		$output = array();
		Core\Debug::ajax($output, 'files', $_POST + $_FILES);

		// Set path.
		// If an URL is also posted, use that URL's page path. Without any URL, the /shared path is used.
		$path = $this->getPathByPostUrl();
		
		// Move uploaded files
		if (isset($_FILES['files']['name'])) {
	
			// Check if upload destination is writable.
			if (is_writable($path)) {
	
				$errors = array();

				// In case the $_FILES array consists of multiple files (IE uploads!).
				for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
	
					// Check if file has a valid filename (allowed file type).
					if (FileSystem::isAllowedFileType($_FILES['files']['name'][$i])) {
						$newFile = $path . Core\Str::sanitize($_FILES['files']['name'][$i]);
						move_uploaded_file($_FILES['files']['tmp_name'][$i], $newFile);
					} else {
						$errors[] = Text::get('error_file_format') . ' "' . FileSystem::getExtension($_FILES['files']['name'][$i]) . '"';
					}
	
				}

				$this->clearCache();
		
				if ($errors) {
					$output['error'] = implode('<br />', $errors);
				} 

			} else {
		
				$output['error'] = Text::get('error_permission') . ' "' . basename($path) . '"';
				
			}

		}
		
		return $output;
			
	}
	
	
}
