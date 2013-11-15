<?php defined('AUTOMAD') or die('Direct access not permitted!');
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
			
				echo '<script>console.log(';
				echo json_encode($var);
				echo ');</script>';
				
				
			} else {
			
				echo '<pre>';
				print_r($var);
				echo '</pre>';
			
			}
			
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
	
	
}


?>