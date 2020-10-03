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
 *	Copyright (c) 2013-2020 by Marc Anton Dahmen
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
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2013-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Debug {
	
	
	/**
	 *	Log buffer.
	 */
	
	private static $buffer = array();
	
	
	/**
	 *	Timestamp when script started.
	 */
	
	private static $time = NULL;
	
	
	/**
	 *	Stop timer, calculate execution time, get user & server constants 
	 *	and return a console log item for every item in the buffer array.
	 *
	 *	@return string The Javascript console log
	 */
	
	public static function consoleLog() {
		
		if (AM_DEBUG_ENABLED) {
			
			// Stop timer.	
			self::timerStop();	
			
			// Memory usage.
			self::memory();
			
			// Get user & server constants.
			self::uc();
			self::log($_SERVER, 'Server');

			// Get last error.
			self::log(error_get_last(), 'Last error');
			
			$html = '<script type="text/javascript">' . "\n";

			foreach (self::$buffer as $key => $item) {
				$html .= 'console.log(' . json_encode($item) . ');' . "\n";
			}

			$html .= '</script>' . "\n";

			return $html;
			
		}
		
	}

	
	/**
	 *	Enable full error reporting, when debugging is enabled.
	 */
	
	public static function errorReporting() {
		
		if (AM_DEBUG_ENABLED) {
			error_reporting(E_ALL);
		}
		
	}
	

	/**
	 * 	Return the buffer array.
	 * 
	 *	@return array The log buffer array
	 */

	public static function getLog() {

		return self::$buffer;

	}


	/**
	 *	Log any kind of variable and append it to the $buffer array.
	 *
	 *	@param mixed $element (The actual content to log)
	 *	@param string $description (Basic info, class, method etc.)
	 */
	
	public static function log($element, $description = '') {
		
		if (AM_DEBUG_ENABLED) {
			
			// Start timer. self::timerStart() only saves the time on the first call.
			self::timerStart();
			
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
			
			self::$buffer[] = array($description => $element);
			
		}
		
	}
	

	/**
	 *	Provide info about memory usage.
	 */
	
	private static function memory() {
		
		self::log((memory_get_peak_usage(true) / 1048576) . 'M of ' . ini_get('memory_limit'), 'Memory used');
		
	}
	
	
	/**
	 *	Start the timer on the first call to calculate the execution time when consoleLog() gets called. 
	 */
	
	private static function timerStart() {
		
		// Only save time on first call.	
		if (!self::$time) {
			self::$time = microtime(true);
			self::log(date('d. M Y, H:i:s'));
		}
		
	}
	
	
	/**
	 * 	Stop the timer and log the execution time.
	 */
	
	private static function timerStop() {
		
		if (self::$time) {
			$executionTime = microtime(true) - self::$time;
			self::log($executionTime . ' seconds', 'Time for execution');
		}
		
	}
	
	
	/**
	 *	Log all user constants for get_defined_constants().
	 */
	
	private static function uc() {
		
		$definedConstants = get_defined_constants(true);
		$userConstants = $definedConstants['user'];
		self::log($userConstants, 'Automad constants');	
		
	}
	
	
}
