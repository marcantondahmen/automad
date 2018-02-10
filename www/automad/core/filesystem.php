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
 *	Copyright (c) 2017-2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The FileSystem class.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2017 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class FileSystem {
	
	
	/**
	 *	Return the extension for a given file.
	 *      
	 *	@param string $file
	 *	@return string The extension
	 */
	
	public static function getExtension($file) {
		
		$pathInfo = pathinfo($file);
		
		if (!empty($pathInfo['extension'])) {
			return $pathInfo['extension'];
		}
		
	}
	
	
	/**
	 * 	Get all installed themes.
	 *
	 *	A theme must be located below the "themes" directory.   
	 *	It is possible to group themes in subdirectories, like "themes/theme" or "themes/subdir/theme".
	 * 
	 *	To be a valid theme, a diretcory must contain a "theme.json" file and at least one ".php" file.
	 *      
	 *	@param  string $path
	 *  @return array An array containing all themes as objects.
	 */
	
	public static function getThemes($path = false) {
		
		if (!$path) {
			$path = AM_BASE_DIR . AM_DIR_THEMES;
		}
		
		$themes = array();
		$defaults = array(
			'name' => false, 
			'description' => false, 
			'author' => false, 
			'version' => false, 
			'license' => false
		);
		
		foreach (glob($path . '/*', GLOB_ONLYDIR) as $dir) {
			
			$themeFile = $dir . '/theme.json';
			$templates = glob($dir . '/*.php');
			
			if (is_readable($themeFile) && is_array($templates) && $templates) {
				
				// If a theme.json file and at least one .php file exist, use that directoy as a theme.
				$json = @json_decode(file_get_contents($themeFile), true);
				$path = Str::stripStart(dirname($themeFile), AM_BASE_DIR . AM_DIR_THEMES . '/');
				
				// Set fallback name.
				if (!is_array($json)) {
					$json = array('name' => $path);
				}
				
				// Remove the 'page not found' template from the array of templates. 
				$templates = array_filter($templates, function($file) {
					return false === in_array(basename($file), array(AM_PAGE_NOT_FOUND_TEMPLATE . '.php'));
				});
				
				// Add theme.
				$themes[$path] = (object) array_merge(
							$defaults, 
							$json, 
							array(
								'path' => $path,
								'templates' => $templates
							)
						);
				
			} else {
				
				// Else check subdirectories for theme.json files.
				$themes = array_merge($themes, self::getThemes($dir));
				
			}
			
		}
		
		return $themes;
		
	}
	
	
	/**
	 *	Tests if a string is a file name with an allowed file extension.
	 *
	 *	Basically a possibly existing file extension is checked against the array of allowed file extensions.
	 *
	 *	"/url/file.jpg" will return true, "/url/file" or "/url/file.something" will return false.
	 *	
	 *	@param string $str
	 *	@return boolean
	 */
	
	public static function isAllowedFileType($str) {
		
		// Remove possible query string
		$str = preg_replace('/\?.*/', '', $str);
		
		// Get just the basename
		$str = basename($str);
		
		// Possible extension		
		$extension = strtolower(pathinfo($str, PATHINFO_EXTENSION));
		
		if (in_array($extension, Parse::allowedFileTypes())) {
			return true;
		} else {
			return false;
		}
		
	}
	
	
	/**
	 *      Create directory if not existing.
	 *
	 *      @param string $path
	 *      @return boolean True on success, else false
	 */
	
	public static function makeDir($path) {
		
		if (!file_exists($path)) {
			$umask = umask(0);
			$return = mkdir($path, AM_PERM_DIR, true);
			umask($umask);
			Debug::log($path, 'Created');
			return $return;
		}
		
	}
	
	
	/**
	 *      Write content to a file and create the parent directory if needed.
	 *
	 *      @param string $file
	 *      @param string $content
	 *      @return boolean True on success, else false
	 */
	
	public static function write($file, $content) {
		
		self::makeDir(dirname($file));
		
		if (!file_exists($file)) {
			@touch($file);
			@chmod($file, AM_PERM_FILE);
			Debug::log($file, 'Created');
		}
		
		if (is_writable($file)) {
			return @file_put_contents($file, $content, LOCK_EX);
		}
		
	}
	
	
}
