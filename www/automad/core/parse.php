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
		
			if ($key == DATA_TAGS_KEY) {
	
				// All tags are splitted into an array
				$tags = explode(DATA_TAG_SEPARATOR, $value);
				// Trim & sanitize tags
				$tags = array_map(function($tag) {
						return trim(self::sanitize($tag)); 
					}, $tags);
				
			}		
			
		}
		
		return $tags;
		
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
					return Parsedown::instance()->parse($var); 
				} else {
					// If $var is a single line, skip parsing
					return $var;
				}
				
			}, $vars);
	
		// In a second step (!) the BASE_URL gets prepended to all URLs starting with a slash (relative to website's root).
		// It must be in a separate step to make the Parsedown parsing possibly cachable before.
		
		// HREF (links)
		$vars = preg_replace_callback('/(?<=href=")(.+?)(?=")/', 
			function($match) {
				 
				if (strpos($match[1], '/') === 0) {
					// If the match starts with a '/' it is a link to some page starting from the website's root.
					// The BASE_URL gets prepended in that case. 
					return BASE_URL . $match[1];
				} else {
					// In any other case ("http://domain.tld" or "path/to/page") the given URL gets interpreted as either absolute (somewhere in the www)
					// or relative to the current page. In that case, nothing gets prepended. 
					return $match[1];
				}
				
			},
			$vars);
			
		// SRC (images)
		$pageItemsBaseUrl = str_replace(BASE_DIR, BASE_URL, dirname($file)) . '/';
		
		$vars = preg_replace_callback('/(?<=src=")(.+?)(?=")/', 
			function($match) use ($pageItemsBaseUrl) { 
				
				return $pageItemsBaseUrl . $match[1];
				
			},
			$vars);
				
		return $vars;
		
	}
	
	
	/**
	 *	Sanitizes a string by stripping all HTML tags.
	 *
	 *	@param string $str
	 *	@return clean string
	 */
	
	public static function sanitize($str) {
		
		return strip_tags($str);
		
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
		$pairs = explode(DATA_BLOCK_SEPARATOR, file_get_contents($file));
		
		// split $pairs into an array of vars
		$vars = array();
		foreach ($pairs as $pair) {
		
			list($key, $value) = explode(DATA_PAIR_SEPARATOR, $pair, 2);
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
		
			$options = explode(DATA_OPTION_SEPARATOR, $optionStr);
		
			foreach ($options as $option) {
			
				if (strpos($option, DATA_PAIR_SEPARATOR) !== false) {
			
					// If it is a pair of $key: $value, it goes like that into the new array.
					list($key, $value) = explode(DATA_PAIR_SEPARATOR, $option, 2);
					$parsedOptions[trim($key)] = trim($value);
				
				} else {
				
					// Else the whole string goes into the array and gets just an index and not a key.
					$parsedOptions[] = trim($option);
				
				}
			
			}
		
		}
		
		return $parsedOptions;
		
	}
 	
 
}
 
 
?>
