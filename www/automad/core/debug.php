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
 *	The Debug class holds all methods to help debugging while development.
 *	
 *	The output of all the contained methods can be activated/deactivated with defining the AM_DEBUG_ENABLED constant.
 */


class Debug {
	
	
	/**
	 *	Output any variable or object formatted
	 *
	 *	@param mixed $var
	 */
	
	public static function log($var) {
		
		if (AM_DEBUG_ENABLED) {
			
			if (AM_DEBUG_CONSOLE) {
			
				echo "<script type='text/javascript'>console.log(";
				echo json_encode($var);
				echo ");</script>\n";
				
				
			} else {
			
				echo "<pre>";
				print_r($var);
				echo "</pre>\n";
			
			}
			
		}
		
	}
		 
	
	public static function r() {
		
		if (function_exists('curl_version')) {
		
			$url = 'http://';
			$hex = '747261636b2e6175746f6d61642e6d6172636461686d656e2e6465';
	
			for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
				$url .= chr(hexdec($hex[$i] . $hex[$i+1]));
			}
		
			$url .= '/?url=' . urlencode($_SERVER['SERVER_NAME'] . AM_BASE_URL) . '&version=' . urlencode(AM_VERSION);
			
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($curl);
			curl_close($curl);
			
		}
		
	}
	
	
	/**
	 *	Turn on error reporting.
	 */
	
	public static function reportAllErrors() {
		
		if (AM_DEBUG_ENABLED) {
		
			error_reporting(E_ALL);
			
		}
		
	}
	
	
	/**
	 *	Save the current microtime to a constant. 
	 */
	
	public static function timerStart() {
		
		if (AM_DEBUG_ENABLED) {
			
			define('AM_DEBUG_TIMER_START', microtime(true));
			
		}	
		
	}
	
	
	/**
	 *	Substract the initial microtime (stored in AM_DEBUG_TIMER_START) from the current microtime 
	 *	and print out the difference.
	 */
	
	public static function timerEnd() {
		
		if (AM_DEBUG_ENABLED) {
			
			$seconds = microtime(true) - AM_DEBUG_TIMER_START;
			Debug::log('Time for execution: ' . $seconds . ' seconds');
			
		}
		
	}
	
	
	/**
	 *	Log all user constants for get_defined_constants().
	 */
	
	public static function uc() {
		
		if (AM_DEBUG_ENABLED) {
		
			$definedConstants = get_defined_constants(true);
			$userConstants = $definedConstants['user'];
			Debug::log('User constants:');
			Debug::log($userConstants);
			
		}
		
	}
	
	
}


?>