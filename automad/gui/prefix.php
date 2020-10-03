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


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Prefix class provides helper to use standard UIkit classes without needing to prefix them manually to avoid conflicts (in page editing). 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2017-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Prefix {
	
	
	/**
	 *	The custom prefix to replace 'uk-'.
	 *	
	 *	The prefix can not contain 'uk-' since selectors like [class*="uk-icon-"]
 	 *	would also match prefixed classes like am-uk-icon-*.
	 */
	
	private static $prefix = 'am-u-';
	
	
	/**
	 *  Replace the prefixes of UIkit classes and data attributes in a string.
	 *      
	 *	@param string $str
	 *	@return string The processed $str
	 */
	
	public static function attributes($str) {
		
		// Also match escaped quotes to handle AJAX requests.
		$regex = '/(class=[\\\\"\']+[\w\s\-]+|data\-[\w\-]+(=[\\\\"\']+(\{[^\}]+|[^\\\\"\']+))?)/is';
		return preg_replace_callback($regex, function($matches) {	
			return str_replace('uk-', self::$prefix, $matches[0]);
		}, $str);
		
	}
	
	
	/**
	 *  Search for HTML tags in a string and replace the prefixes of UIkit classes and data attributes.
	 *      
	 *	@param string $str
	 *	@return string The processed $str
	 */
	
	public static function tags($str) {
		
		// Only replace prefixes within real HTML tags (not converted to HTML entities) and therefore avoid possible collisions with user content.
		return preg_replace_callback('/<\w+[^>]*>/is', function($matches) {	
			return self::attributes($matches[0]);
		}, $str);
		
	}
	
	
}
