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
 *	The output of all the contained methods can be activated/deactivated with defining the DEBUG_MODE constant.
 */


class Debug {
	
	
	/**
	 *	Output any variable or object formatted
	 *
	 *	@param mixed $var
	 */
	
	public static function pr($var) {
		
		if (DEBUG_MODE) {
			
			echo '<pre>';
			print_r($var);
			echo '</pre>';
			
		}
		
	}
	
	
	/**
	 *	Turn on error reporting.
	 */
	
	public static function reportAllErrors() {
		
		if (DEBUG_MODE) {
		
			error_reporting(E_ALL);
			
		}
		
	}
	
}


?>