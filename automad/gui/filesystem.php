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
 *	The FileSystem class provides all methods related to file system operations. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2018 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class FileSystem extends Core\FileSystem {
	
	
	/**
	 *	Append a suffix to a path just before the trailing slash.
	 *      
	 *  @param string $path
	 *  @param string $suffix
	 *  @return string The path with appended suffix
	 */
	
	public static function appendSuffixToPath($path, $suffix) {
		
		return rtrim($path, '/') . $suffix . '/';
		
	}
	
	
	/**
	 * 	Open a data text file under the given path, read the data, 
	 *  append a suffix to the title variable and write back the data.
	 *      
	 *  @param string $path   
	 *  @param string $suffix 
	 */
	
	public static function appendSuffixToTitle($path, $suffix) {
		
		if ($suffix) {
			
			$path = self::fullPagePath($path);
			$files = glob($path . '*.' . AM_FILE_EXT_DATA);
			
			if (!empty($files)) {
				
				$file = reset($files);
				$data = Core\Parse::textFile($file);
				$data[AM_KEY_TITLE] .= ucwords(str_replace('-', ' ', $suffix));
				self::writeData($data, $file);
						
			}
			
		} 
		
	}
	

	/**
	 *  Unlike FileSystem::movePageDir(), this method only copies all files within a page directory without (!) any subdirectories.
	 *      
	 *  @param string $source
	 *  @param string $dest
	 */
	
	public static function copyPageFiles($source, $dest) {
		
		// Sanatize dirs.
		$source = self::fullPagePath($source);
		$dest = self::fullPagePath($dest);
		
		// Get files in directory to be copied.
		$files = glob($source . '*');
		$files = array_filter($files, 'is_file');
		
		// Create directoy and copy files.
		self::makeDir($dest);
		
		foreach ($files as $file) {
			$copy = $dest . basename($file);
			copy($file, $copy);
			chmod($copy, AM_PERM_FILE);
		}
		
	}
	
	
	/**
	 *  Deletes a file and its caption (if existing).
	 *      
	 *  @param string $file
	 *  @return string Only error messages - false in case no errors occured!
	 */
	
	public static function deleteMedia($file) {
		
		if (is_writable($file)) {
			
			if (unlink($file)) {
				 
				$captionFile = $file . '.' . AM_FILE_EXT_CAPTION;
				
				if (file_exists($captionFile)) {
					if (is_writable($captionFile)) {
						if (!unlink($captionFile)) {
							return Text::get('error_permission') . ' "' . basename($captionFile) . '"';
						}
					} else {
						return Text::get('error_permission') . ' "' . basename($captionFile) . '"';
					}
				}
				
			} else {
				
				return Text::get('error_permission') . ' "' . basename($file) . '"';
				
			}
			
		} else {
			
			return Text::get('error_permission') . ' "' . basename($file) . '"';
			
		}
			
	}


	/**
	 *  Get the full file system path for the given path.
	 *      
	 *  @param string $path
	 *  @return string The full path
	 */

	public static function fullPagePath($path) {
		
		if (strpos($path, AM_BASE_DIR . AM_DIR_PAGES) !== 0) {
			$path = AM_BASE_DIR . AM_DIR_PAGES . $path;
		}
		
		return rtrim($path, '/') . '/';
		
	}


	/**
	 * 	Return the path of the temp dir if it is writable by the webserver.
	 *  In any case, '/tmp' is the preferred directory, because of automatic cleanup at reboot, 
	 *  while other locations like '/var/tmp' do not get purged by the system.
	 *  But since '/tmp' is only available on macos and linux, 
	 *  sys_get_temp_dir() is used as fallback.
	 *
	 *  @return string The path to the temp dir
	 */
	
	public static function getTmpDir() {
		
		$tmp = '/tmp';
		
		if (is_writable($tmp)) {
			return $tmp;
		}
		
		if (is_writable(sys_get_temp_dir())) {
			return rtrim(sys_get_temp_dir(), '/');
		}
		
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
	 *	@return string $newPath
	 */

	public static function movePageDir($oldPath, $newParentPath, $prefix, $title) {
		
		// Normalize parent path. In case of a 1st level page, dirname(page) will return '\' on windows.
		// Therefore it is needed to convert all backslashes.
		$newParentPath = str_replace('\\', '/', $newParentPath);
		$newParentPath = '/' . ltrim(trim($newParentPath, '/') . '/', '/');
		
		// Not only sanitize strings, but also remove all dots, to make sure a single dot will work fine as a prefix.title separator.
		$prefix = ltrim(Core\Str::sanitize($prefix, true, AM_DIRNAME_MAX_LEN) . '.', '.');
		$title = Core\Str::sanitize($title, true, AM_DIRNAME_MAX_LEN);
		
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
			
			// Get suffix in case the path is already taken.
			$suffix = self::uniquePathSuffix($newPath);
			$newPath = self::appendSuffixToPath($newPath, $suffix); 
			
			// Move dir.
			self::makeDir(self::fullPagePath($newParentPath));
			rename(self::fullPagePath($oldPath), self::fullPagePath($newPath));
			
			// Update the page title in the .txt file to reflect the actual path suffix.
			self::appendSuffixToTitle($newPath, $suffix);
		
		}
		
		return $newPath;
		
	}
	

	/**
	 * 	Move all items in /cache to the PHP temp directory.
	 *
	 *  @return string $tmp
	 */

	public static function purgeCache() {
		
		// Check if the temp dir is actually writable.
		if ($tmp = self::getTmpDir()) {
			
			$tmpSubDir = '/automad-trash';
			$trash = $tmp . $tmpSubDir;
			$n = 0;
			
			// Create unique subdirectory in temp.
			while (is_dir($trash)) {
				$n++;
				$trash = $tmp . $tmpSubDir . '-' . $n;
			}
			
			if (self::makeDir($trash)) {
			
				// Collect items to be removed.
				$cacheItems = array_merge(
					glob(AM_BASE_DIR . AM_DIR_CACHE . '/*', GLOB_ONLYDIR),
					glob(AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '*')
				);
				
				foreach ($cacheItems as $item) {
					if (!rename($item, $trash . '/' . basename($item))) {
						return false;
					}
				}
			
				// Return $trash on success.
				return $trash;
				
			}	
			
		}
				
	}


	/**
	 *  Renames a file and its caption (if existing).
	 *      
	 *  @param string $oldFile
	 *  @param string $newFile
	 *  @return string Only error messages - false in case no errors occured!          
	 */
	
	public static function renameMedia($oldFile, $newFile) {
		
		if (is_writable(dirname($oldFile))) {
			
			if (is_writable($oldFile)) {
				
				if (!file_exists($newFile)) {
				
					if (rename($oldFile, $newFile)) {
						
						// Set new mtime to force refresh of page cache in case the new name was belonging to a delete file before.
						touch($newFile);
						
						$oldCaptionFile = $oldFile . '.' . AM_FILE_EXT_CAPTION;
						$newCaptionFile = $newFile . '.' . AM_FILE_EXT_CAPTION;
						
						if (file_exists($oldCaptionFile)) {
							if (is_writable($oldCaptionFile) && (is_writable($newCaptionFile) || !file_exists($newCaptionFile))) {
								rename($oldCaptionFile, $newCaptionFile);
							} else {
								return Text::get('error_permission') . ' "' . basename($newCaptionFile) . '"';
							}
						} 
						
					} else {
						
						return Text::get('error_permission') . ' "' . basename($oldFile) . '"';
						
					}
				
				} else {
					
					return '"' . $newFile . '" ' . Text::get('error_existing');
					
				}
			
			} else {
				
				return Text::get('error_permission') . ' "' . basename($oldFile) . '"';
				
			}
			
		} else {
			
			return Text::get('error_permission') . ' "' . basename(dirname($oldFile)) . '"';
			
		}
				
	}

	
	/**
	 *  Creates an unique suffix for a path to avoid conflicts with existing directories.
	 *      
	 *  @param string $path
	 *  @param string $prefix (prepended to the numerical suffix)
	 *  @return string The suffix
	 */
	
	public static function uniquePathSuffix($path, $prefix = '') {
		
		$i = 1;
		$suffix = $prefix;
		
		while (file_exists(self::appendSuffixToPath(self::fullPagePath($path), $suffix))) {
			$suffix = $prefix . '-' . $i++;
		}
	
		return $suffix;
		
	}
	

	/**
	 *  Format, filter and write the data array a text file.
	 *      
	 *  @param array $data
	 *  @param string $file
	 */
	
	public static function writeData($data, $file) {
		
		$pairs = array();
		$data = array_filter($data, 'strlen');
		
		foreach ($data as $key => $value) {
			
			// Only keep variables keys starting with a letter. 
			// (ignore any kind of system variable)
			if (preg_match('/^' . Core\Regex::$charClassTextFileVariables . '+$/', $key)) {
				$pairs[] = $key . AM_PARSE_PAIR_SEPARATOR . ' ' . $value;
			}
			
		} 
	
		$content = implode("\r\n\r\n" . AM_PARSE_BLOCK_SEPARATOR . "\r\n\r\n", $pairs);
		self::write($file, $content);
		
	}
	
	
}
