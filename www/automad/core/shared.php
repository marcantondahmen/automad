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
 *	The Shared class represents a collection of all shared site-wide data.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2016 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Shared {
	
	
	/**
	 *	The shared data array.
	 */
	
	public $data = array();
	
	
	/**
	 *	Parse the shared data file.
	 */
	
	public function __construct() {
		
		// Define default settings.
		// Use the server name as default site name and the first found theme folder as default theme.	
		$themes = 	glob(AM_BASE_DIR . AM_DIR_THEMES . '/*', GLOB_ONLYDIR);	
		$defaults = 	array(	
					AM_KEY_SITENAME => $_SERVER['SERVER_NAME'],
					AM_KEY_THEME => basename(reset($themes))  
				);
		
		// Merge defaults with settings from file.
		$this->data = array_merge($defaults, Parse::textFile(AM_FILE_SHARED_DATA));
		
	}
	
	
	/**
	 *	Return requested value.
	 *
	 *	@param string $key
	 *	@return The requested value
	 */
	
	public function get($key) {
		
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}
		
	}
	
	
}


?>