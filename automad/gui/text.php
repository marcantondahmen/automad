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
 *	Copyright (c) 2016-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Text class provides all methods related to the text modules used in the GUI. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Text {
	
	
	/**
	 *	Array of GUI text modules.
	 */
	
	private static $modules = array();
	
	
	/**
	 *  Short version of echo Text::get().
	 *
	 *	@param string $key
	 */
	
	public static function e($key) {
		
		echo Text::get($key);
		
	}
	
	
	/**
	 *	Parse the text modules file and store all modules in Text::$modules.
	 *	In case AM_FILE_GUI_TRANSLATION is defined, the translated text modules 
	 *	will be merged into Text:$modules.
	 */
	
	public static function parseModules() {
		
		Text::$modules = Core\Parse::textFile(AM_FILE_GUI_TEXT_MODULES);
		
		if (AM_FILE_GUI_TRANSLATION) {
			
			$translationFile = AM_BASE_DIR . AM_FILE_GUI_TRANSLATION;
			
			if (is_readable($translationFile)) {
				
				$translation = Core\Parse::textFile($translationFile);
				
				if (is_array($translation)) {
					Text::$modules = array_merge(Text::$modules, $translation);
				}
				
			}
			
		}
		
		array_walk(Text::$modules, function(&$item) {
			$item = Core\Str::markdown($item, true);
			// Remove all line breaks to avoid problems when using text modules in JS notify.
			$item = str_replace(array("\n", "\r"), '', $item);
		});
			
	}
	
	
	/**
	 *	Return the requested text module.
	 *
	 *	@param string $key
	 *	@return string The requested text module
	 */
	
	public static function get($key) {
		
		if (isset(Text::$modules[$key])) {
			return Text::$modules[$key];
		}
		
	}


	/**
	 * 	Return the modules as object to be used in heredoc strings.
	 * 
	 *	@return object The modules array as object
	 */

	public static function getObject() {

		return (object) self::$modules;

	}
	
	
}
