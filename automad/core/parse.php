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
				// Trim Tags
				$tags = array_map(function($tag) {
						return trim($tag); 
					}, $tags);
				
			}		
			
		}
		
		return $tags;
		
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
