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
 *	Copyright (c) 2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');
 
 
/**
 *	The SessionData class handles setting and getting items of $_SESSION['data'].
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2018 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */
 
class SessionData {
	
	
		/**
		 *	Get modification time of the session data array.
		 *	
		 * 	@return integer The UNIX timestamp of the modification time or 0
		 */

		public static function getMTime() {
			
			if (isset($_SESSION['mtime'])) {
				return $_SESSION['mtime'];
			}
			
			return 0;
			
		}

		
		/**
		 *	Set the session data array modification time.
		 */

		private static function setMTime() {
			
			$_SESSION['mtime'] = time();
			
		}

		
		/**
		 *	Set a key/value pair in the session data array.
		 *
		 * 	@param string $key
		 * 	@param string $value
		 */

		public static function set($key, $value) {
			
			if (!isset($_SESSION['data'])) {
				$_SESSION['data'] = array();
			}
			
			$_SESSION['data'][$key] = $value;
			self::setMTime();
			
		}
		
		
		/**
		 *	Get the session data array or just one value in case $key is defined.
		 *	
		 * 	@param string $key
		 * 	@return mixed The data array or a single value
		 */
		
		public static function get($key = false) {
			
			if (!isset($_SESSION['data'])) {
				$_SESSION['data'] = array();
			}
			
			if ($key) {
				if (array_key_exists($key, $_SESSION['data'])) {
					return $_SESSION['data'][$key];
				}
			} else {
				return $_SESSION['data'];
			}
			
			return false;
			
		}
		
		
}
