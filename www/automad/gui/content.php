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
 *	Copyright (c) 2016 by Marc Anton Dahmen
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
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2016 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Content {

	
	/**
	 *	The Automad object.
	 */

	private $Automad;
	
	
	/**
	 *	Set $this->Automad when creating an instance.
	 *
	 *	@param object $Automad
	 */
	
	public function __construct($Automad) {
		
		$this->Automad = $Automad;
		
	}
	
	
	/**
	 *	Add page based on $_POST.
	 *
	 *	@return $output array (AJAX response)
	 */
	
	public function addPage() {
		
		$output = array();
		
		// Validation of $_POST. URL, title and template must exist and != false.
		if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->Automad->getCollection())) {
	
			if (isset($_POST['subpage']) && isset($_POST['subpage']['title']) && $_POST['subpage']['title'] && isset($_POST['subpage']['theme_template']) && $_POST['subpage']['theme_template']) {
		
				// The current page, where the subpage has to be added to, becomes the parent page for the new page.
				$Page = $this->Automad->getPageByUrl($_POST['url']);
	
				// Check if the current page's directory is writable.
				if (is_writable(dirname($this->getPageFilePath($Page)))) {
	
					// The new page's properties.
					$title = $_POST['subpage']['title'];
					$theme_template = $_POST['subpage']['theme_template'];
					$theme = dirname($theme_template);
					$template = basename($theme_template);
					
					// Save new subpage below the current page's path.		
					$subdir = Core\String::sanitize($title, true);
					
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
					
					// Create the page directory.
					$old = umask(0);
					mkdir(FileSystem::fullPagePath($newPagePath), 0777, true);
					umask($old);
					
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
	
			$output['error'] = Text::get('error_page_not_found');
	
		}	
		
		return $output;
		
	}
	
	
	/**
	 *      Clear the page cache.
	 */
	
	private function clearCache() {
		
		$Cache = new Core\Cache();
		$Cache->clear();
		
	}
	
		
	/**
	 *	Delete files.
	 *
	 *	@param array $files
	 *	@param string $path
	 *	@return $output
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
					$success[] = Text::get('success_remove') . ' "' . basename($file) . '"';
				}
				
			}
	
			$this->clearCache();
	
			$output['success'] = implode('<br />', $success);
			$output['error'] = implode('<br />', $errors);

		} else {
		
			$output['error'] = Text::get('error_permission') . ' "' . basename($path) . '"';
		
		}
		
		return $output;
		
	}
	
	
	/**
	 *	Delete page based on $_POST.
	 *
	 *	@return $output array (AJAX response)
	 */
	
	public function deletePage() {
		
		$output = array();

		// Validate $_POST.
		if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->Automad->getCollection()) && $_POST['url'] != '/' && isset($_POST['title']) && $_POST['title']) {

			$Page = $this->Automad->getPageByUrl($_POST['url']);

			// Check if the page's directory and parent directory are wirtable.
			if (is_writable(dirname($this->getPageFilePath($Page))) && is_writable(dirname(dirname($this->getPageFilePath($Page))))) {

				FileSystem::movePageDir($Page->path, '..' . AM_DIR_TRASH . dirname($Page->path), $this->extractPrefixFromPath($Page->path), $_POST['title']);
				$output['redirect'] = '?context=edit_page&url=' . urlencode($Page->parentUrl);

				$this->clearCache();

			} else {
		
				$output['error'] = Text::get('error_permission') . '<p>' . dirname(dirname($this->getPageFilePath($Page))) . '</p>';
		
			}
	
		} 
		
		return $output;
		
	}
	
	
	/**
	 *      Duplicate a page based on $_POST.
	 *      
	 *      @return $output array (AJAX response) 
	 */
	
	public function duplicatePage() {
		
		$output = array();
		
		if (!empty($_POST['url'])) {
			
			$url = $_POST['url'];
			
			if ($url && $url != '/' && array_key_exists($url, $this->Automad->getCollection())) {
				
				$Page = $this->Automad->getPageByUrl($url);
				
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
				
			}
			
		}
		
		return $output;
		
	}
	

	/**
	 *	Edit file information (file name and caption) based on $_POST.
	 *	
	 *	@return $output
	 */
	
	public function editFileInfo() {
		
		$output = array();

		if (!empty($_POST['old-name']) && !empty($_POST['new-name']) && isset($_POST['caption'])) {
	
			if ($_POST['new-name']) {
				
				$path = $this->getPathByPostUrl();
				$oldFile = $path . basename($_POST['old-name']);
				$newFile = $path . Core\String::sanitize(basename($_POST['new-name']));
				
				$caption = $_POST['caption'];
				$ext = FileSystem::getExtension($newFile);
				
				if (in_array($ext, Core\Parse::allowedFileTypes())) {
					
					// Rename file and caption if needed.
					if ($newFile != $oldFile) {
						$output['error'] = FileSystem::renameMedia($oldFile, $newFile);
					}
					
					// Write caption.
					if (empty($output['error'])) {
						
						$newCaptionFile = $newFile . '.' . AM_FILE_EXT_CAPTION;
						
						// Only if file exists already or $caption is empty.
						if (is_writable($newCaptionFile) || !file_exists($newCaptionFile)) {
							$old = umask(0);
							file_put_contents($newCaptionFile, $caption);
							umask($old);
						} else {
							$output['error'] = Text::get('error_file_save') . ' "' . basename($newCaptionFile) . '"';
						}
						
					}
				
					$this->clearCache();
					 
				} else {
			
					$output['error'] = Text::get('error_file_format') . ' "' . $ext . '"';
				
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
	 *	@return Prefix
	 */

	public function extractPrefixFromPath($path) {
		
		return substr(basename($path), 0, strpos(basename($path), '.'));
			
	}
	
	
	/**
	 *	Return a JSON formatted string to be used as autocomplete infomation in a search field.
	 *	
	 *	The collected data consists of all page titles, URLs and all available tags.
	 *
	 *	@return the JSON data
	 */
	
	public function getAutoCompleteJSON() {
		
		$data = array();
		$tags = array();
		
		foreach ($this->Automad->getCollection() as $Page) {
			$data[]['value'] = $Page->get(AM_KEY_TITLE);
			$data[]['value'] = $Page->url;
			$tags = array_merge($tags, $Page->tags);
		}
		
		$tags = array_unique($tags);
		
		foreach ($tags as $tag) {
			$data[]['value'] = $tag;
		}
		
		return json_encode($data, JSON_UNESCAPED_SLASHES);
		
	}
	
	
	/**
	 * 	Get a preview version of an image file based on $_POST.
	 *
	 *      @return The $output to be used as response for an AJAX request.
	 */
	
	public function getFilePreview() {
		
		$output = array();
		
		if (!empty($_POST['file']) && isset($_POST['url']) && !empty($_POST['height'] && !empty($_POST['width']))) {
			
			$path = $this->getPathByPostUrl();
			$file = $path . $_POST['file'];
			$ext = FileSystem::getExtension($file);
			
			if (in_array($ext, array('jpg', 'png', 'gif'))) {
				$Image = new Core\Image($file, $_POST['width'], $_POST['height'], true);
				$output['html'] = '<div class="uk-text-center"><img src="' . AM_BASE_URL . $Image->file . '" /></div>';
			} 
			
		}
		
		return $output;
		
	}
	
	
	/**
	 *	Return the full file system path of a page's data file.
	 *
	 *	@param object $Page
	 *	@return Filename
	 */

	public function getPageFilePath($Page) {
		
		return FileSystem::fullPagePath($Page->path) . $Page->template . '.' . AM_FILE_EXT_DATA;
	
	}


	/**
	 *      Return the file path for a based on $_POST['url']. In case URL is empty, return the '/shared' directory.
	 *      
	 *      @return The full path to the related directory.
	 */
	
	public function getPathByPostUrl() {
		
		// Check if file from a specified page or the shared files will be listed and managed.
		// To display a file list of a certain page, its URL has to be submitted along with the form data.
		if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->Automad->getCollection())) {
			
			$Page = $this->Automad->getPageByUrl($_POST['url']);
			return FileSystem::fullPagePath($Page->path);
			
		} else {
			
			return AM_BASE_DIR . AM_DIR_SHARED . '/';
			
		}
		
	}


	/**
	 *	Get results for a search query from $_GET. In case there is only one match, redirect to the edit page for that URL. 
	 *
	 *	@return an array with matching pages. 
	 */

	public function getSearchResults() {
		
		$pages = array();
	
		if ($query = Core\Parse::queryKey('query')) {
		
			$collection = $this->Automad->getCollection();
		
			if (array_key_exists($query, $collection)) {
			
				// If $query matches an actual URL of an existing page, just get that page to be the only match in the $pages array.
				// Since $pages has only one elements, the request gets directly redirected to the edit page (see below).
				$pages = array($this->Automad->getPageByUrl($query));
							
			} else {
			
				$Selection = new Core\Selection($collection);
				$Selection->filterByKeywords($query);
				$Selection->sortPages(AM_KEY_MTIME, SORT_DESC);
				$pages = $Selection->getSelection(false);
			
			}
	
			// Redirect to edit mode for a single result or in case $query represents an actually existing URL.
			if (count($pages) == 1) {
				$Page = reset($pages);
				header('Location: ' . AM_BASE_URL . AM_INDEX . AM_PAGE_GUI . '?context=edit_page&url=' . urlencode($Page->url));
				die;	
			}
		
		}
		
		return $pages;
		
	}
	
	
	/**
	 *	Move a page based on $_POST.
	 *	
	 *	@return $output array (error/redirect)
	 */
	
	public function movePage() {
		
		$output = array();

		// Validation of $_POST.
		// To avoid all kinds of unexpected trouble, the URL and the destination must exist in the Automad's collection and a title must be present.
		if (isset($_POST['url']) && isset($_POST['title']) && isset($_POST['destination']) && array_key_exists($_POST['url'], $this->Automad->getCollection()) && array_key_exists($_POST['destination'], $this->Automad->getCollection()) && $_POST['title']) {
	
			// The home page can't be moved!	
			if ($_POST['url'] != '/') {
		
				$Page = $this->Automad->getPageByUrl($_POST['url']);
				$dest = $this->Automad->getPageByUrl($_POST['destination']);
		
				// Check if new parent directory is writable.
				if (is_writable(FileSystem::fullPagePath($dest->path))) {
	
					// Check if the current page's directory and parent directory is writable.
					if (is_writable(dirname($this->getPageFilePath($Page))) && is_writable(dirname(dirname($this->getPageFilePath($Page))))) {
	
						// Move page
						$newPagePath = FileSystem::movePageDir($Page->path, $dest->path, $this->extractPrefixFromPath($Page->path), $_POST['title']);	
						$output['redirect'] = $this->contextUrlByPath($newPagePath);
						
						$this->clearCache();
		
					} else {
				
						$output['error'] = Text::get('error_permission') . '<p>' . dirname(dirname($this->getPageFilePath($Page))) . '</p>';
				
					}
		
				} else {
			
					$output['error'] = Text::get('error_permission') . '<p>' . FileSystem::fullPagePath($dest->path) . '</p>';
			
				}
			
			} 	

		} else {
	
			$output['error'] = Text::get('error_page_not_found'); 
	
		}
		
		return $output;
		
	}
	
	
	/**
	 *      Return updated context URL based on $path.
	 *      
	 *      @param string $path
	 *      @return The context URL to the new page.
	 */
	
	private function contextUrlByPath($path) {
		
		// Rebuild Automad object, since the file structure has changed.
		$Automad = new \Automad\Core\Automad();
		
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
	 *	@return $output array (error/redirect)
	 */
	
	public function savePage($url, $data) {
		
		$output = array();
		$Page = $this->Automad->getPageByUrl($url);
	
		// A title is required for building the page's path.
		// If there is no title provided, an error will be returned instead of saving and moving the page.
		if ($data[AM_KEY_TITLE]) {
			
			// Check if the parent directory is writable for all pages but the homepage.
			// Since the directory of the homepage is just "www/pages" and its parent directory is the base directory (normally "www"),
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
					if (isset($_POST['hidden'])) {
						$data['hidden'] = 1;
					}
	
					// The theme and the template get passed as theme/template.php combination separate form $_POST['data']. 
					// That information has to be parsed first and "subdivided".

					// Get correct theme name.
					// If the theme is not set and there is no slash passed within 'theme_template', the resulting dirname is just a dot.
					// In that case, $data['theme'] gets removed (no theme - use site theme). 
					if (dirname($_POST['theme_template']) != '.') {
						$data['theme'] = dirname($_POST['theme_template']);
					} else {
						unset($data['theme']);
					}

					// Delete old (current) file, in case, the template has changed.
					unlink($this->getPageFilePath($Page));

					// Build the path of the data file by appending the basename of 'theme_template' to $Page->path.
					$newPageFile = FileSystem::fullPagePath($Page->path) . str_replace('.php', '', basename($_POST['theme_template'])) . '.' . AM_FILE_EXT_DATA;
					
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
					
					$output['redirect'] = $this->contextUrlByPath($newPagePath);
					
					$this->clearCache();
					
				} else {
					
					$output['error'] = Text::get('error_permission') . '<br /><small>' . dirname($this->getPageFilePath($Page)) . '</small>';
					
				}
	
			} else {
				
				$output['error'] = Text::get('error_permission') . '<br /><small>' . dirname(dirname($this->getPageFilePath($Page))) . '</small>';
				
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
	 *	@return $output array (error)
	 */
	
	public function saveSharedData($data) {
		
		$output = array();
		
		// Filter empty values.
		$data = array_filter($_POST['data']);

		// Build file content to be written to the txt file.
		$pairs = array();

		foreach ($data as $key => $value) {
			$pairs[] = $key . AM_PARSE_PAIR_SEPARATOR . ' ' . $value;
		}

		$content = implode("\r\n\r\n" . AM_PARSE_BLOCK_SEPARATOR . "\r\n\r\n", $pairs);
	
		// Write file.
		$old = umask(0);
	
		if (!@file_put_contents(AM_FILE_SHARED_DATA, $content)) {
			$output['error'] = Text::get('error_permission') . '<p>' . AM_FILE_SHARED_DATA . '</p>';
		}
	
		umask($old);
	
		$this->clearCache();
	
		return $output;
		
	}
	
	
	/**
	 *	Upload handler based on $_POST and $_FILES.
	 *
	 *	@return $output
	 */
	
	public function upload() {
		
		$output = array();
		$output['debug'] = $_POST + $_FILES;

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
					if (Core\Parse::isFileName($_FILES['files']['name'][$i])) {
						$newFile = $path . Core\String::sanitize($_FILES['files']['name'][$i]);
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


?>