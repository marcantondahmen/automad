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
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Extension class serves as an interface for calling extension methods via the template syntax.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Extension {

	
	
	/**
	 * 	Multidimensional array of collected assets grouped by type (CSS/JS).
	 */
	
	private static $assets = array();
	

	/**
	 * 	Create the HTML tags for each file in $assest and prepend them to the closing </head> tag.
	 *	
	 *	@param string $str
	 *	@return $str
	 */
	
	public static function createAssetTags($str) {
		
		Debug::log('Extension: Assets: ' . var_export(Extension::$assets, true));
		
		$html = '';
		
		if (isset(Extension::$assets['css'])) {
			
			foreach (Extension::$assets['css'] as $file) {
			
				$html .= "\t" . '<link type="text/css" rel="stylesheet" href="' . str_replace(AM_BASE_DIR, '', $file) . '" />' . "\n";
				Debug::log('Extension: Added "' . $file . '" to header');	
			
			}
			
		}
		
		if (isset(Extension::$assets['js'])) {
			
			foreach (Extension::$assets['js'] as $file) {
		
				$html .= "\t" . '<script type="text/javascript" src="' . str_replace(AM_BASE_DIR, '', $file) . '"></script>' . "\n";
				Debug::log('Extension: Added "' . $file . '" to header');
		
			}
			
		}
		
		// Prepend all items ($html) to the closing </head> tag.
		return str_replace('</head>', $html . '</head>', $str);
		
	}
	
	
	/**
	 * 	Collect all assets (CSS & JS files) belonging to $extension and store them in $assets.
	 *	
	 *	@param string $extension
	 */
	
	private static function collectAssets($extension) {
			
		$path = AM_BASE_DIR . strtolower(str_replace('\\', '/', AM_NAMESPACE_EXTENSIONS) . '/' . $extension);
		
		Debug::log('Extension: Getting assets for "' . $extension . '" in: ' . $path);
		
		foreach (glob($path . '/*.css') as $file) {
			
			// Only add the minified version, if existing.
			if (!file_exists(str_replace('.css', '.min.css', $file))) {
			
				// Use $file also as key to keep elemtens unique.
				Extension::$assets['css'][$file] = $file;
			
			}
			
		}
		
		foreach (glob($path . '/*.js') as $file) {
			
			// Only add the minified version, if existing.
			if (!file_exists(str_replace('.js', '.min.js', $file))) {
			
				// Use $file also as key to keep elemtens unique.
				Extension::$assets['js'][$file] = $file;
			
			}
			
		}
		
	}
	

	/**
	 *	Call extension method from template dynamically.
	 *
	 *	@param string $name
	 *	@param array $options
	 *	@param object $Automad
	 *	@return The returned value from the called method
	 */
	
	public static function call($name, $options, $Automad) {
		
		// Collect assets.
		Extension::collectAssets($name);
		
		// Adding the extension namespace to the called class here, to make sure,
		// that only classes from the /extensions directory and within the \Extension namespace get used.
		$class = AM_NAMESPACE_EXTENSIONS . '\\' . $name;
		
		// Building the extension's file path.
		$file = AM_BASE_DIR . strtolower(str_replace('\\', '/', $class) . '/' . $name) . '.php';
		
		if (file_exists($file)) {
							
			// Load class.				
			Debug::log('Extension: Require once ' . $file);
			require_once $file;
			
			if (class_exists($class, false)) {
				
				// Create instance of class dynamically.
				$object = new $class();
				Debug::log('Extension: Created instance of class "' . $class . '"');
		
				if (method_exists($object, $name)) {
					
					// Call method dynamically and pass $options & Automad.
					Debug::log('Extension: Calling method "' . $name . '" and passing the following options:');
					Debug::log($options);
					return $object->$name($options, $Automad);
		
				} else {
					
					Debug::log('Extension: Method "' . $name . '" not existing!');	
				
				}
		
			} else {
				
				Debug::log('Extension: Class "' . $class . '" not existing!');		
			
			}
		
		} else {
			
			Debug::log('Extension: ' . $file . ' not found!');
		
		}
		
	}
	
	
}


?>