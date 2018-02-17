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
 *	Copyright (c) 2014-2018 by Marc Anton Dahmen
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
 *	@copyright Copyright (c) 2014-2018 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Config {
	
	
	/**
	 * 	Read configuration overrides form JSON file and set constants accordingly.
	 *
	 * 	@param string $file
	 */
	 
	public static function json($file) {
		
		if (file_exists($file)) {
			foreach (json_decode(file_get_contents($file), true) as $name => $value) {
				define($name, $value);	
			}
		}
		
	}
	
	
	/**
	 * 	Define constant, if not defined already.
	 * 
	 * 	@param string $name
	 * 	@param string $value
	 */
	 
	public static function set($name, $value) {
	
		if (!defined($name)) {
			define($name, $value);
		}
	
	}
	
	
}
