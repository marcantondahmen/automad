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
				self::log('TIMER END');
				self::log('Time for execution: ' . $executionTime . ' seconds');
			}	
			
			// Get user & server constants.
			self::uc();
			self::log('Server:');
			self::log($_SERVER);
			
			return self::$buffer;
			
		}
		
	}
	
	
	/**
	 *	Log any kind of variable and append it as JS console.log item to $buffer.
	 *
	 *	@param mixed $var
	 */
	
	public static function log($var) {
		
		if (AM_DEBUG_ENABLED) {
			
			// Start timer on first call.
			if (!self::$time) {
				self::$time = microtime(true);
				self::log('TIMER START');
			}
			
			self::$buffer .= "<script type='text/javascript'>console.log(" . json_encode($var) . ");</script>\n";
			
		}
		
	}
	
	
	/**
	 *	Log all user constants for get_defined_constants().
	 */
	
	public static function uc() {
		
		$definedConstants = get_defined_constants(true);
		$userConstants = $definedConstants['user'];
		self::log('Automad constants:');
		self::log($userConstants);	
		
	}
	
	
}


?>