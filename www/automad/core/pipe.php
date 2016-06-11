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
 *	The Pipe class handles the chain of processes to manipulate variable values. 
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2016 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Pipe {
	
	
	/**
	 *	Whitelist of standard PHP string functions.
	 */
	
	private static $phpStringFunctions = array('strlen', 'strtolower', 'strtoupper', 'ucwords');
	

	/**
	 *	Process a chain of functions to manipulate a given value. The output of each function is passed as the input value to the next one.
	 *
	 *	If a function name matches a String class method, that method is called, else if a function name is in the whitelist of PHP standard functions, that function is called.
	 *	In case a function name is an integer value, the String::shorten() method is called and the integer value is passed as parameter.
	 *	
	 *	@param string $value
	 *	@param string $pipe - (like: | function (parameters) | function (parameters) | ...)
	 *	@return the modified $value 
	 */

	public static function process($value, $pipe) {
		
		// Match functions.
		preg_match_all('/' . Regex::pipe('function') . '/s', $pipe, $matches, PREG_SET_ORDER);
		
		// Process functions.
		foreach ($matches as $match) {
			
			$function = $match['functionName'];
			$parameters = array();
			
			// Prepare function parameters.
			if (isset($match['functionParameters'])) {
				
				// Relpace single quotes when not escaped with double quotes.
				$csv = preg_replace('/(?<!\\\\)(\')/', '"', $match['functionParameters']);
				
				// Create $parameters array.
				$parameters = str_getcsv($csv, ',', '"');
				$parameters = array_map('trim', $parameters);
				
				// Cast boolean parameters correctly.
				// To use "false" or "true" as strings, they have to be escaped like "\true" or "\false".
				array_walk($parameters, function(&$param) {
					if (in_array($param, array('true', 'false'))) {
						$param = filter_var($param, FILTER_VALIDATE_BOOLEAN);
					}
				});
				
				$parameters = array_map('stripslashes', $parameters);
				
			} 
			
			// Add the actual $value to the parameters array as its first element.
			$parameters = array_merge(array(0 => $value), $parameters);
			
			// Call string function.
			if (method_exists('\Automad\Core\String', $function)) {
				// Call a String class method.
				$value = call_user_func_array('\Automad\Core\String::' . $function, $parameters);
				Debug::log($parameters, 'Call String::' . $function);
			} else if (in_array(strtolower($function), Pipe::$phpStringFunctions)) {
				// Call standard PHP string function.
				$value = call_user_func_array($function, $parameters);
				Debug::log($parameters, 'Call ' . $function);
			} else if (is_numeric($function)) {
				// In case $function is a number, call String::shorten() method and pass $function as paramter for the max number of characters.
				Debug::log($value, 'Shorten content to max ' . $function . ' characters');
				$value = String::shorten($value, $function);
			}
				
		}
			
		return $value;
		
		
	}
	
	
}


?>