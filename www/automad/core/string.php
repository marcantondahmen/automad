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


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 * 	The String class holds all string methods.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2016 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class String {
	
	
	/**
	 *	Set a default value for $str.
	 *	
	 *	@param string $str
	 *	@param string $default
	 *	@return The string or the default value.
	 */

	public static function defaultValue($str, $defaultValue) {
				
		if (trim($str) === '') {
			$str = $defaultValue;
		}
		
		return $str;
		
	}


	/**
	 * 	Escape a string to be used within a JSON string.
	 *	
	 *	@param string $str
	 *	@return Escaped, JSON-safe string
	 */

	public static function jsonEscape($str) {
		
		$search = array('"',   "'",  "\n", "\r");
		$replace = array('\"', "\'", ' ',  ' ');
		
		return str_replace($search, $replace, $str);
		
	}
	
	
	/**
	 *	Parse a markdown string. Optionally skip parsing in case $str is a single line string.
	 *
	 *	@param string $str
	 *	@param boolean $multilineOnly
	 *	@return The parsed string
	 */
	
	public static function markdown($str, $multilineOnly = false) {
		
		// In case $str has no line breaks and $multilineOnly is enabled, skip parsing.
		if (strpos($str, "\n") === false && $multilineOnly) { 
			return $str;
		} else {
			return \Michelf\MarkdownExtra::defaultTransform($str);
		}
		
	}
	
		
	/**
	 *	Cleans up a string to be used as URL, directory or file name. 
	 *	The returned string constists of the following characters: a-z, A-Z, - and optional dots (.)
	 *	That means, this method is safe to be used with filenames as well, since it keeps by default the dots as suffix separators.
	 *
	 *	Note: To produce fully safe prefixes and directory names, 
	 *	possible dots should be removed by setting $removeDots = true. 
	 *
	 *	@param string $str
	 *	@param boolean $removeDots	
	 *	@return the sanitized string
	 */
	
	public static function sanitize($str, $removeDots = false) {
			
		// If dots should be removed from $str, replace them with '-', since URLify::filter() only removes them fully without replacing.
		if ($removeDots) {
			$str = str_replace('.', '-', $str);
		}
		
		// Convert slashes separately to avoid issues with regex in URLify.
		$str = str_replace('/', '-', $str);
		
		// Configure URLify. 
		// Add non-word chars and reset the remove list.
		// Note: $maps gets directly manipulated without using URLify::add_chars(). 
		// Using the add_chars() method would extend $maps every time, String::sanitize() gets called. 
		// Adding a new array to $maps using a key avoids that and just overwrites that same array after the first call without adding new elements.
		\JBroadway\URLify::$maps['nonWordChars'] = array('=' => '-', '&' => '-and-', '+' => '-plus-', '@' => '-at-', '|' => '-', '*' => '-x-');
		\JBroadway\URLify::$remove_list = array();
		
		// Since all possible dots got removed already above (if $removeDots is true), 
		// $str should be filtered as filename to keep dots if they are still in $str and $removeDots is false. 
		return \JBroadway\URLify::filter($str, 100, '', true);
		
	}
	
	
	/**
	 *	Shortens a string keeping full words. Note that this method also first strips all tags from the given string.
	 *	
	 *	@param string $str
	 *	@param number $maxChars
	 *	@param string $ellipsis
	 *	@return The shortened string
	 */
	
	public static function shorten($str, $maxChars, $ellipsis = ' ...') {
		
		$str = String::stripTags($str);
		$str = preg_replace('/[\n\r]+/s', ' ', $str);
		
		// Shorten $text to maximal characters (full words).
		if (strlen($str) > $maxChars) {
			// Cut $str to max chars
			$str = substr($str, 0, $maxChars);
			// Find last space and get position
			$pos = strrpos($str, ' ');
			// Cut $str again at last space's position (< $maxChars)
			$str = substr($str, 0, $pos) . $ellipsis;
		}
		
		return trim($str);
		
	}
	
	
	/**
	 *	Removes all HTML and Markdown (!) tags.
	 *
	 *	@param string $str
	 *	@return The clean string
	 */
	
	public static function stripTags($str) {
		
		return strip_tags(String::markdown($str));
		
	}
		
	
}


?>