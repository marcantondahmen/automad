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
 *	Copyright (c) 2017-2020 by Marc Anton Dahmen
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
 *	@copyright Copyright (c) 2017-2018 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class FileSystem {
	

	/**
	 *	Delete a file.
	 *	
	 *	@param string $file
	 *	@return bool Return true if the file was deleted succsessfully
	 */

	public static function deleteFile($file) {

		if (is_file($file)) {
			if (is_writable($file) && is_writable(dirname($file))) {
				return unlink($file);
			}
		}

	}
	

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
	 * 	Get file extension for images based on mime types.
	 * 
	 *	@param string $file
	 *	@return	string The extension or false
	 */

	public static function getImageExtensionFromMimeType($file) {

		try {
			
			$getimagesize = getimagesize($file);
			$type = $getimagesize['mime'];
			
			switch($type) {

				case 'image/jpeg':
					$extension = '.jpg';
					break;
				case 'image/gif':
					$extension = '.gif';
					break;
				case 'image/png':
					$extension = '.png';
					break;
				default: 
					$extension = '';
		    	    break;

			}

			return $extension;

		} catch (\Exception $e) {
			
			return false;

		}

	}
	

	/**
	 * 	Return the path of the temp dir if it is writable by the webserver.
	 *  In any case, '/tmp' is the preferred directory, because of automatic cleanup at reboot, 
	 *  while other locations like '/var/tmp' do not get purged by the system.
	 *  But since '/tmp' is only available on macos and linux, 
	 *  sys_get_temp_dir() is used as fallback.
	 *
	 *	@return string The path to the temp dir
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
	 *	A wrapper for PHP's built-in glob function. 
	 *	This method always returns an array, even though glob() returns false 
	 *	on some systems instead of empty arrays.
	 *
	 *	@param string $pattern
	 *	@param integer $flags
	 *	@return array The list of matching files
	 */

	public static function glob($pattern, $flags = 0) {

		$files = glob($pattern, $flags);

		if (!$files) {
			return array();
		}

		return	array_map(function($path) {
					return self::normalizeSlashes($path);
				}, $files);

	}


	/**
	 *	Find files by using the glob() method and filter the resulting array by a regex pattern.
	 *	Note that this method should basically replace the usage of GLOB_BRACE to be fully 
	 *	compatible to systems where this constant is not defined. Instead of a glob pattern 
	 *	like "/path/*.{jpg,png}" it is more safe to use a generic pattern like "/path/*.*" 
	 *	filtered by a regex like "/\.(jpg|png)$/i" without using the GLOB_BRACE flag.
	 *
	 *	@param string $pattern
	 *	@param string $regex
	 *	@param integer $flags
	 *	@return array The filtered list of matching files
	 */

	public static function globGrep($pattern, $regex, $flags = 0) {

		return array_values(preg_grep($regex, self::glob($pattern, $flags)));

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
	 *	Create directory if not existing.
	 *
	 *	@param string $path
	 *	@return boolean True on success, else false
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
	 *	Replace all backslashes in a given path with forward slashes.
	 *	
	 *	@param string $path
	 *	@return string The processed path with only forward slashes
	 */

	public static function normalizeSlashes($path) {

		return str_replace('\\', '/', $path);

	}

	
	/**
	 *	Write content to a file and create the parent directory if needed.
	 *
	 *	@param string $file
	 *	@param string $content
	 *	@return boolean True on success, else false
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
