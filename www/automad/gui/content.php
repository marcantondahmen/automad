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
	 *	The Automad object;
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
	
					// The new page's properties
					$title = $_POST['subpage']['title'];
					$theme_template = $_POST['subpage']['theme_template'];

					// Build initial content for data file.
					$content = AM_KEY_TITLE . AM_PARSE_PAIR_SEPARATOR . ' ' . $title;
	
					// The new page's theme.
					if (dirname($theme_template) != '.') {
						$content .= "\r\n\r\n" . AM_PARSE_BLOCK_SEPARATOR . "\r\n\r\n" . AM_KEY_THEME . AM_PARSE_PAIR_SEPARATOR . ' ' . dirname($theme_template);
					} 

					// Save new subpage below the current page's path.		
					$subdir = Core\String::sanitize($title, true);
					
					// If $subdir is an empty string after sanitizing, set it to 'untitled'.
					if (!$subdir) {
						$subdir = 'untitled';
					}
					
					// Add trailing slash.
					$subdir .= '/';
					
					$newPagePath = $Page->path . $subdir;

					$i = 1;

					// In case page exists already...
					while (file_exists(AM_BASE_DIR . AM_DIR_PAGES . $newPagePath)) {
						$newPagePath = $Page->path . $i . '.' . $subdir;		
						$i++;	
					}

					// Build the file name. 
					$file = AM_BASE_DIR . AM_DIR_PAGES . $newPagePath . str_replace('.php', '', basename($theme_template)) . '.' . AM_FILE_EXT_DATA;

					// Save content.
					$old = umask(0);

					if (!file_exists(AM_BASE_DIR . AM_DIR_PAGES . $newPagePath)) {
						mkdir(AM_BASE_DIR . AM_DIR_PAGES . $newPagePath, 0777, true);
					}

					file_put_contents($file, $content);
					umask($old);

					// Clear the cache to make sure, the changes get reflected on the website directly.
					$Cache = new Core\Cache();
					$Cache->clear();

					// Rebuild Automad object, since the file structure has changed.
					$Automad = new Core\Automad();

					// Find new URL and return redirect query string.
					foreach ($Automad->getCollection() as $key => $Page) {

						if ($Page->path == $newPagePath) {
	
							$output['redirect'] = '?context=edit_page&url=' . urlencode($key);
							break;
	
						}

					}
		
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

				$this->moveDirectory($Page->path, '..' . AM_DIR_TRASH . dirname($Page->path), $this->extractPrefixFromPath($Page->path), $_POST['title']);
				$output['redirect'] = '?context=edit_page&url=' . urlencode($Page->parentUrl);

				$Cache = new Core\Cache();
				$Cache->clear();

			} else {
		
				$output['error'] = Text::get('error_permission') . '<p>' . dirname(dirname($this->getPageFilePath($Page))) . '</p>';
		
			}
	
		} 
		
		return $output;
		
	}
	

	/**
	 *	Extract the deepest directory's prefix from a given path.
	 *
	 *	@return Prefix
	 */

	public function extractPrefixFromPath($path) {
		
		return substr(basename($path), 0, strpos(basename($path), '.'));
			
	}
	
	
	/**
	 *	Return the full file system path of a page's data file.
	 *
	 *	@param object $Page
	 *	@return Filename
	 */

	public function getPageFilePath($Page) {
		
		return AM_BASE_DIR . AM_DIR_PAGES . $Page->path . $Page->template . '.' . AM_FILE_EXT_DATA;
	
	}
	
	
	/**
	 *	Move a directory to a new location.
	 *	The final path is composed of the parent directoy, the prefix and the title.
	 *	In case the resulting path is already occupied, an index get appended to the prefix, to be reproducible when resaving the page.
	 *
	 *	@param string $oldPath
	 *	@param string $newParentPath (destination)
	 *	@param string $prefix
	 *	@param string $title
	 *	@return $newPath
	 */

	private function moveDirectory($oldPath, $newParentPath, $prefix, $title) {
		
		// Normalize parent path.
		$newParentPath = '/' . ltrim(trim($newParentPath, '/') . '/', '/');
		
		// Not only sanitize strings, but also remove all dots, to make sure a single dot will work fine as a prefix.title separator.
		$prefix = ltrim(Core\String::sanitize($prefix, true) . '.', '.');
		$title = Core\String::sanitize($title, true);
		
		// If the title is an empty string after sanitizing, set it to 'untitled'.
		if (!$title) {
			$title = 'untitled';
		}
		
		// Add trailing slash.
		$title .= '/';

		// Build new path.
		$newPath = $newParentPath . $prefix . $title;
			
		// Contiune only if old and new paths are different.	
		if ($oldPath != $newPath) {
			
			$i = 1;
			
			// Check if path exists already
			while (file_exists(AM_BASE_DIR . AM_DIR_PAGES . $newPath)) {
				
				$newPrefix = ltrim(trim($prefix, '.') . '-' . $i, '-') . '.';
				$newPath = $newParentPath . $newPrefix . $title;
				$i++;
				
			}
		
			$old = umask(0);		
			
			if (!file_exists(AM_BASE_DIR . AM_DIR_PAGES . $newParentPath)) {
				mkdir(AM_BASE_DIR . AM_DIR_PAGES . $newParentPath, 0777, true);
			}
			
			rename(AM_BASE_DIR . AM_DIR_PAGES . $oldPath, AM_BASE_DIR . AM_DIR_PAGES . $newPath);
			
			umask($old);
		
		}
		
		return $newPath;
		
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
				if (is_writable(AM_BASE_DIR . AM_DIR_PAGES . $dest->path)) {
	
					// Check if the current page's directory and parent directory is writable.
					if (is_writable(dirname($this->getPageFilePath($Page))) && is_writable(dirname(dirname($this->getPageFilePath($Page))))) {
	
						// Move page
						$newPagePath = $this->moveDirectory($Page->path, $dest->path, $this->extractPrefixFromPath($Page->path), $_POST['title']);
	
						// Clear the cache to make sure, the changes get reflected on the website directly.
						$Cache = new \Automad\Core\Cache();
						$Cache->clear();
	
						// Rebuild Automad object, since the file structure has changed.
						$Automad = new \Automad\Core\Automad();

						// Find new URL and return redirect query string.
						foreach ($Automad->getCollection() as $key => $page) {

							if ($page->path == $newPagePath) {
		
								$output['redirect'] = '?context=edit_page&url=' . urlencode($key);
								break;
		
							}

						}
		
					} else {
				
						$output['error'] = Text::get('error_permission') . '<p>' . dirname(dirname($this->getPageFilePath($Page))) . '</p>';
				
					}
		
				} else {
			
					$output['error'] = Text::get('error_permission') . '<p>' . AM_BASE_DIR . AM_DIR_PAGES . rtrim($dest->path, '/') . '</p>';
			
				}
			
			} 	

		} else {
	
			$output['error'] = Text::get('error_page_not_found'); 
	
		}
		
		return $output;
		
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
		if ($data['title']) {
			
			// Check if the parent directory is writable.
			if (is_writable(dirname(dirname($this->getPageFilePath($Page))))) {
	
				// Check if the page's file and the page's directory is writable.
				if (is_writable($this->getPageFilePath($Page)) && is_writable(dirname($this->getPageFilePath($Page)))) {
			
					// Remove empty data.
					// Needs to be done here, to be able to simply test for empty title field.
					$data = array_filter($data);
		
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
	
					// Build file content to be written to the txt file.
					$pairs = array();

					foreach ($data as $key => $value) {
						$pairs[] = $key . AM_PARSE_PAIR_SEPARATOR . ' ' . $value;
					}

					$content = implode("\r\n\r\n" . AM_PARSE_BLOCK_SEPARATOR . "\r\n\r\n", $pairs);
	
					// Delete old (current) file, in case, the template has changed.
					unlink($this->getPageFilePath($Page));

					// Build the path of the data file by appending the basename of 'theme_template' to $Page->path.
					$newPageFile = AM_BASE_DIR . AM_DIR_PAGES . $Page->path . str_replace('.php', '', basename($_POST['theme_template'])) . '.' . AM_FILE_EXT_DATA;
	
					// Save new file within current directory, even when the prefix/title changed. 
					// Renaming/moving is done in a later step, to keep files and subpages bundled to the current text file.
					$old = umask(0);
					file_put_contents($newPageFile, $content);
					umask($old);
	
					// If the page is not the homepage, 
					// rename the page's directory including all children and all files, after saving according to the (new) title and prefix.
					// $this->moveDirectory() will check if renaming is needed, and will skip moving, when old and new path are equal.
					if ($url != '/') {
	
						if (!isset($_POST['prefix'])) {
							$prefix = '';
						} else {
							$prefix = $_POST['prefix'];
						}

						$newPagePath = $this->moveDirectory($Page->path, dirname($Page->path), $prefix, $data['title']);
	
					} else {
			
						// In case the page is the home page, the path is just '/'.
						$newPagePath = '/';
			
					}
	
					// Clear the cache to make sure, the changes get reflected on the website directly.
					$Cache = new Core\Cache();
					$Cache->clear();
	
					// Rebuild Automad object, since the file structure might be different now.
					$Automad = new Core\Automad();

					// Find new URL.
					foreach ($Automad->getCollection() as $key => $Page) {
		
						if ($Page->path == $newPagePath) {
				
							// Just return a redirect URL (might be the old URL), to also reflect the possible renaming in all the GUI's navigation.
							$output['redirect'] = '?context=edit_page&url=' . urlencode($key);
							break;
				
						}
		
					}
	
				} else {
					
					$output['error'] = Text::get('error_permission') . '<p>' . dirname($this->getPageFilePath($Page)) . '</p>';
					
				}
	
			} else {
				
				$output['error'] = Text::get('error_permission') . '<p>' . dirname(dirname($this->getPageFilePath($Page))) . '</p>';
				
			}
	
		} else {
		
			// If the title is missing, just return an error.
			$output['error'] = Text::get('error_page_title');
		
		}
		
		return $output;
		
	}
	
	
}


?>