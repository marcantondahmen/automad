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
 *	Copyright (c) 2017 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Prefix class provides helper to use standard UIkit classes without needing to prefix them manually to avoid conflicts (in page editing). 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2017 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Prefix {
	
	
	/**
	 *	Array of GUI text modules.
	 */
	
	private static $prefix = 'am';
	
	
	/**
	 *      Add the prefix to all UIkit classes and data attributes.
	 *      
	 *      @param string $str
	 *      @return string The processed $str
	 */
	
	public static function add($str) {
		
		$str = preg_replace('/(uk-[a-z\d\-]+)/', self::$prefix . '-$1', $str);
		$str = preg_replace('/(data-uk-)/', 'data-' . self::$prefix . '-uk-', $str);
		
		return $str;
		
	}
	
	
}
