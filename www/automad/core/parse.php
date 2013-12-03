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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');
 
 
/**
 *	The Parse class holds all parsing methods.
 */
 
 
class Parse {
	

 	/**
 	 *	Extracts the tags string out of a given array and returns an array with these tags.
 	 *
 	 *	@param array $data
 	 *	@return array $tags
 	 */
	
	public static function extractTags($data) {
		
		$tags = array();
		
		foreach ($data as $key => $value) {
		
			if ($key == AM_PARSE_TAGS_KEY) {
	
				// All tags are splitted into an array
				$tags = explode(AM_PARSE_TAG_SEPARATOR, $value);
				// Trim & strip tags
				$tags = array_map(function($tag) {
						return trim(strip_tags($tag)); 
					}, $tags);
				
			}		
			
		}
		
		return $tags;
		
	}
	
	
	/**
	 *	Tests if a string is a file name.
	 *
	 *	Basically a possibly existing file extension is checked against the array of registered file extensions.
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
		
		// Explode to name / type
		// If there is actually an extension will be checked below
		$parts = explode('.', $str);
		
		// Check if an extensions exists and if that extensions is on of the registered (known) extensions.
		if (count($parts > 1)) {
			
			$str = end($parts);	
			$fileExtensions = unserialize(AM_PARSE_REGISTERED_FILE_EXTENSIONS);
			
			if (in_array($str, $fileExtensions)) {
				
				return true;
				
			} else {
				
				return false;
				
			}
			
		} else {
			
			return false;
			
		}
		
	}
	
	
	/**
	 *	Parses a text file including markdown syntax. 
	 *	
	 *	If a variable in that file has a multiline string as its value, that string will be then parsed as markdown.
	 *	If the variable string is just a single line, markdown parsing is skipped.
	 *	In a second step, all URLs are checked and fixed, in case they point to the website's root.
	 *	
	 *	@param string $file
	 *	@return Array of variables
	 */
	
	public static function markdownFile($file) {
		
		$vars = self::textFile($file);
			
		$vars = array_map(function($var) {
			 
				if (strpos($var, "\n") !== false) {
					// If $var has line breaks (is multiline)
					return \Lib\Michelf\MarkdownExtra::defaultTransform($var); 
				} else {
					// If $var is a single line, skip parsing
					return $var;
				}
				
			}, $vars);
					
		return $vars;
		
	}
	
	
	/**
	 *	Turn numeric string into a float value.
	 *
	 *	@param string $str
	 *	@return $str (string or float)
	 */
	
	public static function numToFloat($str) {
		
		if (is_numeric($str)) {	
			$str = floatval($str);
		}
		
		return $str;
		
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
	 *	Test if a key exists in the query string and return that key.
	 *
	 *	@param string $key
	 *	@return $queryKey
	 */
	
	public static function queryKey($key) {
	
		// Save currently passed filter query to determine current filter/sort_dir when generating list
		if (isset($_GET[$key])) {
			$queryKey = $_GET[$key];
		} else {
			$queryKey = '';
		}
		
		return $queryKey;
	
	}
		
	
	/**
	 *	Cleans up a string to be used as URL.
	 *
	 *	@param string $str
	 *	@return $str
	 */
	
	public static function sanitize($str) {
		
		$search  = array(" ","&"  ,"/","=","*","+"  ,"ä","ö","ü","å","ø","á","à","é","è","Ä","Ö","Ü","Å","Ø","Á","À","É","È");
		$replace = array("-","and","-","_","x","and","a","o","u","a","o","a","a","e","e","A","O","U","A","O","A","A","E","E");
		
		return strtolower(str_replace($search, $replace, html_entity_decode($str)));

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
		
		// split $file into data blocks
		$pairs = explode(AM_PARSE_BLOCK_SEPARATOR, file_get_contents($file));
		
		// split $pairs into an array of vars
		$vars = array();
		foreach ($pairs as $pair) {
		
			list($key, $value) = explode(AM_PARSE_PAIR_SEPARATOR, $pair, 2);
			$vars[trim($key)] = trim($value);	
			
		}
		
		return $vars;
		
	}
 
 
	/**
	 *	Parse $optionStr and return a (mixed) array of options
	 *
	 *	@param string $optionStr
	 *	@return $parsedOptions
	 */

	public static function toolOptions($optionStr) {
		
		$parsedOptions = array();
		
		if ($optionStr) {
		
			$options = explode(AM_PARSE_OPTION_SEPARATOR, $optionStr);
		
			foreach ($options as $option) {
			
				if (strpos($option, AM_PARSE_PAIR_SEPARATOR) !== false) {
			
					// If it is a pair of $key: $value, it goes like that into the new array.
					list($key, $value) = explode(AM_PARSE_PAIR_SEPARATOR, $option, 2);
					$parsedOptions[trim($key)] = Parse::numToFloat(trim($value));
				
				} else {
				
					// Else the whole string goes into the array and gets just an index and not a key.
					$parsedOptions[] = Parse::numToFloat(trim($option));
				
				}
			
			}
		
		}
		
		return $parsedOptions;
		
	}
 	
 
}
 
 
?>
