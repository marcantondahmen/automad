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
 *	Copyright (c) 2013-2018 by Marc Anton Dahmen
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
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2013-2018 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */
 
class Parse {
	

	/**
	 *	Return an array with the allowed file types.
	 *
	 *	@return array An array of file types
	 */

	public static function allowedFileTypes() {
		
		return self::csv(AM_ALLOWED_FILE_TYPES);
		
	}


	/**
	 *	Read a file's caption file and parse contained markdown syntax.
	 *
	 *	The caption filename is build out of the actual filename with the appended ".caption" extension, like "image.jpg.caption".
	 *	
	 *	@param string $file
	 *	@return string The caption string
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
     *  Split and trim comma separated string.
     *  
     *  @param string $str
     *  @return array The array of separate and trimmed strings
     */
    
    public static function csv($str) {
        
        $array = explode(AM_PARSE_STR_SEPARATOR, $str);
        $array = array_filter($array, 'strlen');
        return array_map('trim', $array);
        
    }


	/**
	 *	Parse a file declaration string where multiple glob patterns can be separated by a comma and return an array with the resolved file paths.
	 *	If $stripBaseDir is true, the base directory will be stripped from the path and each path gets resolved to be relative to the Automad installation directory.
	 * 
	 *	@param string $str
	 *	@param object $Page (current page)
	 *	@param boolean $stripBaseDir
	 *	@return array An array with resolved file paths
	 */

	public static function fileDeclaration($str, $Page, $stripBaseDir = false) {
		
		$files = array();
		
		if ($str) {
			
			foreach (explode(AM_PARSE_STR_SEPARATOR, $str) as $glob) {
						
				if ($f = glob(Resolve::filePath($Page->path, trim($glob)))) {
					$files = array_merge($files, $f);
				}
				
			}
			
			array_walk($files, function(&$file) use ($stripBaseDir) { 
				
				$file = realpath($file); 
				
				if ($stripBaseDir) {
					$file = Str::stripStart($file, AM_BASE_DIR);
				}
				
                // Windows compatibility.
                $file = str_replace('\\', '/', $file);
                
			});	
			
		}
		
		return $files;
		
	}


    /**
     *  Parse a filename to check whether a file is an image or not.
     *
     *  @param string $file
     *  @return boolean True if $file is an image file
     */

    public static function fileIsImage($file) {
        
        return (in_array(FileSystem::getExtension($file), array('jpg', 'jpeg', 'png', 'gif')));
        
    } 


	/**
	 *	Parse a (dirty) JSON string and return an associative, filtered array
	 *
	 *	@param string $str
	 *	@return array $options - associative array
	 */

	public static function jsonOptions($str) {
		
		$options = array();
		
		if ($str) {
			
			$debug['String'] = $str;
			
            // Remove all tabs and newlines.
            $str = str_replace(array("\n", "\r", "\t"), ' ', $str);
            
			// Clean up "dirty" JSON by replacing single with double quotes and
			// wrapping all keys in double quotes.
            $pairs = array();
            preg_match_all('/' . Regex::keyValue() . '/s', $str, $matches, PREG_SET_ORDER);
            
            foreach ($matches as $match) {
                
                $key = '"' . trim($match['key'], '"') . '"';
                $value = preg_replace('/^([\'"])(.*)\1$/s', '$2', trim($match['value']));
                
                if (!is_numeric($value) && $value !== 'true' && $value !== 'false') {  
        			$value = str_replace('\"', '"', $value);
        			$value = addcslashes($value,'"');
        			$value = '"' . $value . '"';
        		}
                
                $pairs[] = $key . ':' . $value; 
                
            }
            
            // Build valid JSON string.        
            $str = '{' . implode(',', $pairs) . '}';
            
			$debug['Clean'] = $str;
				
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
	 *	Return value of a query string parameter or any empty string, if that parameter doesn't exist.
	 *	Note: Since this method always returns a string, it should not be used to test whether a parameter exists in the query string, 
	 * 	because a non-existing parameter and an empty string as a parameter's value will return the same.
	 * 
	 *	@param string $key
	 *	@return string The value for the requested query key
	 */
	
	public static function query($key) {
	
		if (isset($_GET[$key])) {
			return $_GET[$key];
		} else {
			return '';
		}
		
	}
		

	/**
	 *	Return the URL of the currently requested page.
	 *	
	 *	@return string The requested URL
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
			$query = explode('&', $_SERVER['QUERY_STRING'], 2);
			$request = $query[0];
			Debug::log($query, 'Getting request from QUERY_STRING "' . $_SERVER['QUERY_STRING'] . '"');
			
			// In case there is no real query string except the requested page.
			if (!isset($query[1])) {
				$query[1] = '';
			}
			
			// Remove request from QUERY_STRING.
			$_SERVER['QUERY_STRING'] = $query[1];
			
			// Remove request from global arrays.
			unset($_GET[$request]);
			unset($_REQUEST[$request]);
			Debug::log($_GET, '$_GET');
			Debug::log($_REQUEST, '$_REQUEST');
			
		} else {
				
			// The requested page gets passed 'index.php/page/path'.
			// That can be the case if rewriting is disabled and AM_BASE_INDEX ends with '/index.php'.
			if (isset($_SERVER['PATH_INFO'])) {
		
				$request = $_SERVER['PATH_INFO'];
				Debug::log('Getting request from PATH_INFO');
	
			} else if (isset($_SERVER['ORIG_PATH_INFO'])) {	
	
				$request = $_SERVER['ORIG_PATH_INFO'];
				Debug::log('Getting request from ORIG_PATH_INFO');
	
			} else if (isset($_SERVER['REQUEST_URI'])) {
		
				$request = Str::stripEnd($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
				$request = Str::stripStart($request, AM_BASE_URL);
				Debug::log('Getting request from REQUEST_URI');
	
			} else if (isset($_SERVER['REDIRECT_URL'])) {
	
				$request = Str::stripStart($_SERVER['REDIRECT_URL'], AM_BASE_URL);
				Debug::log('Getting request from REDIRECT_URL');
			
			} else if (isset($_SERVER['PHP_SELF'])) {
	
				$request = Str::stripStart($_SERVER['PHP_SELF'], AM_BASE_URL);
				$request = Str::stripStart($request, '/index.php');
				Debug::log('Getting request from PHP_SELF');
				
			}
			
			$request = Str::stripStart($request, '/index.php');
			
		}
	
		// Remove trailing slash from URL to keep relative links consistent.
		if (substr($request, -1) == '/' && $request != '/') {
			header('Location: ' . AM_BASE_INDEX . rtrim($request, '/'), false, 301);
			die;
		}
		
		$request = '/' . trim($request, '/');
		
		Debug::log($request, 'Requested page');
		
		return $request; 
		
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
		
		if (!file_exists($file)) {
			return $vars;
		}
			
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
