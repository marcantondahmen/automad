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
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Config class.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2014-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Config {
	

	/**
	 *	The legacy .json file.
	 */

	private static $legacy = AM_BASE_DIR . '/config/config.json';
	

	/**
	 *	Read configuration overrides as JSON string form PHP or JSON file 
	 *	and decode the returned string. Note that now the configuration is stored in 
	 *	PHP files instead of JSON files to make it less easy to access from outside.
	 *	
	 *	@return array The configuration array
	 */
	 
	public static function read() {
		
		$json = false;
		$config = array();

		if (is_readable(AM_CONFIG)) {
			$json = require AM_CONFIG;
		} else if (is_readable(self::$legacy)) {
			// Support legacy configuration files.
			$json = file_get_contents(self::$legacy);
		}

		if ($json) {
			$config = json_decode($json, true); 
		}
		
		return $config;

	}


	/**
	 *	Write the configuration file.
	 *	
	 *	@param array $config 
	 *	@return boolean True on success
	 */

	public static function write($config) {

		$json = json_encode($config, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
		$content = "<?php return <<< JSON\r\n$json\r\nJSON;\r\n";
		$success = FileSystem::write(AM_CONFIG, $content);

		if ($success && is_writable(self::$legacy)) {
			@unlink(self::$legacy);
		}

		if ($success && function_exists('opcache_invalidate')) {
			opcache_invalidate(AM_CONFIG, true);
		}

		return $success;

	}


	/**
	 *	Define constants based on the configuration array.
	 */

	public static function overrides() {

		foreach (self::read() as $name => $value) {
			define($name, $value);	
		}

	}
	
	
	/**
	 * 	Define constant, if not defined already.
	 * 
	 *	@param string $name
	 *	@param string $value
	 */
	 
	public static function set($name, $value) {
	
		if (!defined($name)) {
			define($name, $value);
		}
	
	}
	
	
}
