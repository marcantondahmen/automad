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
 *	Copyright (c) 2019-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The GUI\Headless class provides helpers for the headless mode 
 *	and extends the Core\Headless class.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2019-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Headless extends Core\Headless { 


	/**	
	 * 	Get the content of the template in use.
	 * 
	 *	@return string The content of the template in use
	 */

	public static function loadTemplate() {

		$file = self::getTemplate();

		if (is_readable($file)) {
			return file_get_contents($file);
		}

	}


	/**
	 * 	Reset the headless template by deleting the custom template file.
	 * 
	 *	@return boolean Returns true it the file existed and was deleted
	 */

	public static function resetTemplate() {

		return FileSystem::deleteFile(AM_BASE_DIR . AM_HEADLESS_TEMPLATE_CUSTOM);

	}
	

}