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
 *	The Debug class holds all methods to help debugging while development.
 *	The output of all the contained methods can be activated/deactivated with defining the AM_DEBUG_ENABLED constant.
 *	All logged information will be stored in $buffer as JS's console.log() items.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Debug {
	
	
	/**
	 *	Log buffer.
	 */
	
	private static $buffer = '';
	
	
	/**
	 *	Timestamp when script started.
	 */
	
	private static $time = NULL;
	
	
	/**
	 *	Enable full error reporting, when debugging is enabled.
	 */
	
	public static function errorReporting() {
		
		if (AM_DEBUG_ENABLED) {
			error_reporting(E_ALL);
		}
		
	}
	
	
	/**
	 *	Stop timer, calculate execution time, get user & server constants and return the log buffer.
	 */
	
	public static function getLog() {
		
		if (AM_DEBUG_ENABLED) {
			
			// Stop timer.	
			if (self::$time) {
				$executionTime = microtime(true) - self::$time;
				self::log($executionTime . ' seconds', 'TIMER END - Time for execution');
			}	
			
			// Get user & server constants.
			self::uc();
			self::log($_SERVER, 'Server');
			
			return '<script type="text/javascript">' . "\n" . self::$buffer . '</script>' . "\n";
			
		}
		
	}
	
	
	/**
	 *	Log any kind of variable and append it as JS console.log item to $buffer.
	 *
	 *	@param mixed $element (The actual content to log)
	 *	@param string $description (Basic info, class, method etc.)
	 */
	
	public static function log($element, $description = '') {
		
		if (AM_DEBUG_ENABLED) {
			
			// Get backtrace.
			$backtraceAll = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
			
			// Remove all backtrace items without any class defined (standard PHP functions) and the items with the functions Debug::log() and {closure} 
			// To get a clean array with only the relevant Automad methods in the backtrace.
			$ignoreFunctions = array('log', __NAMESPACE__ . '\{closure}');
			$backtrace = 	array_filter($backtraceAll, function($item) use ($ignoreFunctions) {	
						return (isset($item['class'], $item['type'], $item['function']) && !in_array($item['function'], $ignoreFunctions));
					});
						
			// If class, type & method exist, use them to build the description prefix. Else use just the file name from the full backtrace. 
			if (count($backtrace) > 0) {
				// When the backtrace array got reduced to the actually relevant items in the backtrace, take the first element (the one calling Debug::log()).
				$backtrace = array_shift($backtrace);		
				$prefix = basename(str_replace('\\', '/', $backtrace['class'])) . $backtrace['type'] . $backtrace['function'] . '(): ';
			} else {
				$prefix = basename($backtraceAll[0]['file']) . ': ';
			}
			
			// Prepend the method to $description.
			$description = trim($prefix . $description, ': ');
			
			self::$buffer .= "console.log(" . json_encode(array($description => $element)) . ");\n";
			
		}
		
	}
	
	
	/**
	 *	Start the timer to calculate the execution time when getLog() gets called. 
	 */
	
	public static function timer() {
		
		if (AM_DEBUG_ENABLED) { 
			
			if (!self::$time) {
				self::$time = microtime(true);
				self::log('TIMER START');
			}
		
		}
		
	}
	
	
	/**
	 *	Log all user constants for get_defined_constants().
	 */
	
	public static function uc() {
		
		$definedConstants = get_defined_constants(true);
		$userConstants = $definedConstants['user'];
		self::log($userConstants, 'Automad constants');	
		
	}
	
	
}


?>