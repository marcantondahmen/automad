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
 *	Copyright (c) 2016-2018 by Marc Anton Dahmen
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
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2018 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Pipe {
	
	
	/**
	 *	Whitelist of standard PHP functions.
	 */
	
	private static $phpFunctions = 	array(
										'ceil',
										'floor',
										'round',
										'strlen',
										'strtolower',
										'strtoupper',
										'ucwords'
									);
	

	/**
	 *	Apply string function to $value.
	 *      
	 * 	@param string $function
	 * 	@param string $paramString
	 * 	@param string $value
	 * 	@return string $value
	 */
	
	private static function stringFunction($function, $paramString, $value) {
		
		// Prepare function parameters.
		if (strlen($paramString)) {
			
			// Relpace single quotes when not escaped with double quotes.
			$csv = preg_replace('/(?<!\\\\)(\')/', '"', $paramString);
			
			// Create $parameters array.
			$parameters = str_getcsv($csv, ',', '"');
			
			// Cast boolean parameters correctly.
			// To use "false" or "true" as strings, they have to be escaped like "\true" or "\false".
			array_walk($parameters, function(&$param) {
				if (in_array($param, array('true', 'false'))) {
					$param = filter_var($param, FILTER_VALIDATE_BOOLEAN);
				}
			});
			
			$parameters = array_map('stripslashes', $parameters);
			
		} else {
			
			$parameters = array();
			
		}
		
		// Add the actual $value to the parameters array as its first element.
		$parameters = array_merge(array(0 => $value), $parameters);
		
		// Call string function.
		if (method_exists('\Automad\Core\Str', $function)) {
			// Call a String class method.
			$value = call_user_func_array('\Automad\Core\Str::' . $function, $parameters);
			Debug::log(array('Result' => $value, 'Parameters' => $parameters), 'Call Str::' . $function);
		} else if (in_array(strtolower($function), Pipe::$phpFunctions)) {
			// Call standard PHP string function.
			$value = call_user_func_array($function, $parameters);
			Debug::log(array('Result' => $value, 'Parameters' => $parameters), 'Call ' . $function);
		} else if (is_numeric($function)) {
			// In case $function is a number, call Str::shorten() method and pass $function as paramter for the max number of characters.
			Debug::log($value, 'Shorten content to max ' . $function . ' characters');
			$value = Str::shorten($value, $function);
		}
		
		return $value;
		
	}


	/**
	 *	Simple math operations.
	 *      
	 * 	@param string $operator
	 * 	@param string $number
	 * 	@param string $value
	 * 	@return number $value
	 */
	
	private static function math($operator, $number, $value) {
		
		$number = floatval($number);
		$value = floatval($value);
		
		switch ($operator) {
			case '+':
				$result = $value + $number;
				break;
			case '-':
				$result = $value - $number;
				break;
			case '*':
				$result = $value * $number;
				break;
			case '/':
				$result = $value / $number;
				break;
			
		}
		
		Debug::log($result, $value . $operator . $number);
		
		return $result;
		
	}


	/**
	 *	Process a chain of functions or mathematical operations to manipulate a given value. The output of each function is passed as the input value to the next one.
	 *
	 *	If a matched string matches a String class method, that method is called, else if that string is in the whitelist of PHP standard functions, that function is called.
	 *	In case a match is just an integer value, the Str::shorten() method is called and the integer value is passed as parameter.    
	 * 	In case a match is not a function name but an operator (+, -, * or /) followed by a number, the math method is called.
	 *	
	 *	@param string $value
	 *	@param string $pipe - (like: | function (parameters) | function (parameters) | ...)
	 *	@return string The modified $value 
	 */

	public static function process($value, $pipe) {
		
		// Match functions.
		preg_match_all('/' . Regex::pipe('pipe') . '/s', $pipe, $matches, PREG_SET_ORDER);
		
		// Process functions.
		foreach ($matches as $match) {
			
			// String function.
			if (!empty($match['pipeFunction'])) {
				
				$function = $match['pipeFunction'];
				$paramString = '';
				
				if (isset($match['pipeParameters'])) {
					$paramString = $match['pipeParameters'];
				}
				
				$value = Pipe::stringFunction($function, $paramString, $value);
				
			}
			
			// Math.
			if (!empty($match['pipeOperator'])) {
				$value = Pipe::math($match['pipeOperator'], $match['pipeNumber'], $value);	
			}
			
		}
			
		return $value;
		
	}
	
	
}
