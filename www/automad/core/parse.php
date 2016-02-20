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
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');
 
 
/**
 *	The Parse class holds all parsing methods.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */
 
class Parse {
	

	/**
	 *	Return an array with the allowed file types.
	 *
	 *	@return Array of file types
	 */

	public static function allowedFileTypes() {
		
		// Split string
		$types = explode(AM_PARSE_STR_SEPARATOR, AM_ALLOWED_FILE_TYPES);
		// Trim items
		$types = array_map(function($type) {
				return trim($type);
		         }, $types);
		
		return $types;
		
	}


	/**
	 *	Read a file's caption file and parse contained markdown syntax.
	 *
	 *	The caption filename is build out of the actual filename with the appended ".caption" extension, like "image.jpg.caption".
	 *	
	 *	@param string $file
	 *	@return the caption string
	 */
	
	public static function caption($file) {
		
		// Build filename of the caption file.
		$captionFile = $file . '.' . AM_FILE_EXT_CAPTION;
		Debug::log($captionFile);
		
		if (is_readable($captionFile)) {
			return file_get_contents($captionFile);
		}
		
	}


	/**
	 *	Parse a file declaration string where multiple glob patterns can be separated by a comma and return an array with the resolved file paths.
	 *	If $stripBaseDir is true, the base directory will be stripped from the path and each path gets resolved to be relative to the Automad installation directory.
	 * 
	 *	@param string $str
	 *	@param object $Page (current page)
	 *	@param boolean $stripBaseDir
	 *	@return Array with resolved file paths
	 */

	public static function fileDeclaration($str, $Page, $stripBaseDir = false) {
		
		$files = array();
		
		foreach (explode(AM_PARSE_STR_SEPARATOR, $str) as $glob) {
					
			if ($f = glob(Resolve::filePath($Page->path, trim($glob)))) {
				$files = array_merge($files, $f);
			}
			
		}
		
		array_walk($files, function(&$file) use ($stripBaseDir) { 
			
			$file = realpath($file); 
			
			if ($stripBaseDir) {
				$file = str_replace(AM_BASE_DIR, '', $file);
			}
			
		});
		
		return $files;
		
	}


	/**
	 *	Tests if a string is a file name (with an allowed file extension).
	 *
	 *	Basically a possibly existing file extension is checked against the array of allowed file extensions.
	 *
	 *	"/url/file.jpg" will return true, "/url/file" or "/url/file.something" will return false.
	 *	
	 *	@param string $str
	 *	@return boolean
	 */
	
	public static function isFileName($str) {
		
		// Remove possible query string
		$str = preg_replace('/\?.*/', '', $str);
		
		// Get just the basename
		$str = basename($str);
		
		// Possible extension		
		$extension = strtolower(pathinfo($str, PATHINFO_EXTENSION));
		
		if (in_array($extension, self::allowedFileTypes())) {
			return true;
		} else {
			return false;
		}
		
	}
	

	/**
	 *	Parse a (dirty) JSON string and return an associative, filtered array
	 *
	 *	@param string $str
	 *	@return $options - associative array
	 */

	public static function jsonOptions($str) {
		
		$options = array();
		
		if ($str) {
			
			$debug['String'] = $str;
			
			// Clean up "dirty" JSON by replacing single with double quotes and
			// wrapping all keys in double quotes.
			$str = str_replace("'", '"', $str);
			$str = preg_replace('/([{,]+)\s*([^":\s]+)\s*:/i', '\1"\2":', $str);
				
			// Decode JSON.
			$options = json_decode($str, true);
			
			// Remove all undefined items (empty string). 
			// It is not possible to use array_filter($options, 'strlen') here, since an array item could be an array itself and strlen() only expects strings.
			if (is_array($options)) {
				$options = 	array_filter($options, function($value) {
							return ($value !== '');
						});
			} else {
				$options = array();
			}
			
			$debug['JSON'] = $options;
			Debug::log($debug);
						
		}
		
		return $options;
		
	}
	
		
	/**
	 *	Get the query string, if existing.
	 *
	 *	@return $query
	 */
	
	public static function queryArray() {
		
		// First get existing query string to prevent overwriting existing settings passed already
		// and store its data in $query.
		if (isset($_GET)) {
			$query = $_GET;
		} else {
			$query = array();
		}
		
		return $query;
		
	}
	
	
	/**
	 *	Return value of a query string parameter or any empty string, if that parameter doesn't exist.
	 *	Note: Since this method always returns a string, it should not be used to test whether a parameter exists in the query string, 
	 * 	because a non-existing parameter and an empty string as a parameter's value will return the same.
	 * 
	 *	@param string $key
	 *	@return $queryKey
	 */
	
	public static function queryKey($key) {
	
		if (isset($_GET[$key])) {
			$queryKey = $_GET[$key];
		} else {
			$queryKey = '';
		}
		
		return $queryKey;
	
	}
		

	/**
	 *	Return the URL of the currently requested page.
	 *	
	 *	@return The requested URL
	 */

	public static function request() {
		
		$request = '';

		if (!isset($_SERVER['QUERY_STRING'])) {
			$_SERVER['QUERY_STRING'] = '';
		}
		
		// Check if the query string starts with a '/'. 
		// That is the case if the requested page gets passed as part of the query string when rewriting is enabled (.htaccess or nginx.conf).
		if (strncmp($_SERVER['QUERY_STRING'], '/', 1) === 0) {
			
			// The requested page gets passed as part of the query string when pretty URLs are enabled and requests get rewritten like:
			// domain.com/page -> domain.com/index.php?/page 
			// Or with a query string: 
			// domain.com/page?key=value -> domain.com/index.php?/page&key=value
			// Depending on the rewrite rules and environment, the query string can also look like:
			// domain.com/page?key=vaule -> domain.com/index.php?/page?key=value (note the 2nd "?"!)
			$query = preg_split('/[&\?]/', $_SERVER['QUERY_STRING'], 2);
			$request = $query[0];
			Debug::log($query, 'Getting request from QUERY_STRING "' . $_SERVER['QUERY_STRING'] . '"');
			
			// In case there is no real query string except the requested page.
			if (!isset($query[1])) {
				$query[1] = '';
			}
			
			// Rebuild correct $_GET array without requested page.
			parse_str($query[1], $_GET);
			
			// Remove request from QUERY_STRING.
			$_SERVER['QUERY_STRING'] = $query[1];
			
			Debug::log($_GET, '$_GET');
			
		} else {
				
			// The requested page gets passed 'index.php/page/path'.
			// That can be the case if rewriting is disabled and AM_INDEX equals '/index.php'.
			if (isset($_SERVER['PATH_INFO'])) {
		
				$request = $_SERVER['PATH_INFO'];
				Debug::log('Getting request from PATH_INFO');
	
			} else if (isset($_SERVER['ORIG_PATH_INFO'])) {	
	
				$request = $_SERVER['ORIG_PATH_INFO'];
				Debug::log('Getting request from ORIG_PATH_INFO');
	
			} else if (isset($_SERVER['REQUEST_URI'])) {
		
				$request = trim(str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), '?');
				Debug::log('Getting request from REQUEST_URI');
	
			} else if (isset($_SERVER['REDIRECT_URL'])) {
	
				$request = $_SERVER['REDIRECT_URL'];
				Debug::log('Getting request from REDIRECT_URL');
			
			} else if (isset($_SERVER['PHP_SELF'])) {
	
				$request = $_SERVER['PHP_SELF'];
				Debug::log('Getting request from PHP_SELF');
	
			}
			
		}
	
		// Remove unwanted components from the request.
		$request = str_replace(AM_BASE_URL, '', $request);
		$request = str_replace('/index.php', '', $request);
		
		// Remove trailing slash from URL to keep relative links consistent.
		if (substr($request, -1) == '/' && $request != '/') {
			header('Location: ' . AM_BASE_URL . AM_INDEX . rtrim($request, '/'), false, 301);
			die;
		}
		
		$request = '/' . trim($request, '/');
		
		Debug::log($request, 'Requested page');
		
		return $request; 
		
	}


	/**
	 * 	Parse Site Data to replace defaults.
	 *
	 *	Get all sitewide settings (like site name, the theme etc.) from the main settings file 
	 *	in the root of the /shared directory.
	 *
	 *	@return Array with the site's settings
	 */
	
	public static function siteData() {
		
		// Define default settings.
		// Use the server name as default site name and the first found theme folder as default theme.	
		$themes = glob(AM_BASE_DIR . AM_DIR_THEMES . '/*', GLOB_ONLYDIR);	
		$defaults = 	array(	
					AM_KEY_SITENAME => $_SERVER['SERVER_NAME'],
					AM_KEY_THEME => basename(reset($themes))  
				);
		
		// Merge defaults with settings from file.
		return array_merge($defaults, self::textFile(AM_FILE_SITE_SETTINGS));
		
	}

	
	/**
	 *	Loads and parses a text file.
	 *
	 *	First it separates the different blocks into simple key/value pairs.
	 *	Then it creates an array of vars by splitting the pairs. 
	 * 
	 *	@param string $file
	 *	@return array $vars
	 */
	 
	public static function textFile($file) {
		
		$vars = array();
			
		// Get file content and normalize line breaks.
		$content = preg_replace('/\r\n?/', "\n", file_get_contents($file));	
			
		// Split $content into data blocks on every line only containing one or more AM_PARSE_BLOCK_SEPARATOR and whitespace, followed by a key in a new line. 
		$pairs = preg_split('/\n' . preg_quote(AM_PARSE_BLOCK_SEPARATOR) . '+\s*\n(?=' . Regex::$charClassTextFileVariables . '+' . preg_quote(AM_PARSE_PAIR_SEPARATOR) . ')/s', $content, NULL, PREG_SPLIT_NO_EMPTY);
		
		// Split $pairs into an array of vars.
		foreach ($pairs as $pair) {
		
			list($key, $value) = explode(AM_PARSE_PAIR_SEPARATOR, $pair, 2);
			$vars[trim($key)] = trim($value);	
			
		}
		
		// Remove undefined (empty) items.
		$vars = array_filter($vars, 'strlen');
		
		return $vars;
		
	}
 

}
 
 
?>
