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
 *	Copyright (c) 2016-2017 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Extension class provides an interface for calling an extension from a template file.
 *
 *	An extension is basically called by the subdirectory name within the "/extensions" directory. 
 *	The file name within that subdirectory must have the basename of that directory followed by ".php".
 *	Both, class and method name, must be the basename of the directory as well.    
 *
 *	The namespace must start with "Extensions".     
 *	In case of extensions grouped in a subdirectory of "/extensions", the name of that directory has to be added to the namespace as well, 
 *	in that way that the namespace reflects the actual directory structure without the last directory containing the actual .php file.    
 *          
 *	Example 1 - Single extension:     
 *	An extension call like {@ example1 @} would load the file "/extensions/example1/example1.php", 
 *	create an instance of the class "\Extensions\example1" ($object) and call the method "$object->example1()" of that class.
 *	The namespace would just be "Extensions".  
 *	The full naming scheme would be:    
 *	- namespace:	Extensions
 *	- directory:	/extensions/example1 (must be lowercase)
 *	- file:		/extensions/example1/example1.php (must be lowercase)
 *	- class:	Example1
 *	- method:	Example1
 *          
 *	Example 2 - Extension in a subdirectory (possibly grouped with others):     
 *	An extension call like {@ group/example2 @} would load the file "/extensions/group/example2/example2.php",
 *	create an instance of the class "\Extensions\group\example2" ($object) and call the method "$object->example2()" of that class.
 *	The namespace in this case would be "Extensions\group". 
 *	The full naming scheme would be:    
 *	- namespace:	Extensions\Group   
 *	- directory:	/extensions/group/example2 (must be lowercase)
 *	- file:		/extensions/group/example2/example2.php (must be lowercase)
 *	- class:	Example2
 *	- method:	Example2
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2017 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Extension {
	
	
	/**
	 *	The array of found assets.
	 */
	
	private $assets = array();
	
	
	/**
	 *	The retured output of the extension.
	 */
	
	private $output = false;
	
	
	/**
	 *	Create an instance of the given extension, call the extension method and look for all needed assets.
	 *
	 *	@param string $extension
	 *	@param array $options
	 *	@param object $Automad
	 */
	
	public function __construct($extension, $options, $Automad) {
		
		Debug::log($extension);
		
		// Building the class name.
		$class = AM_NAMESPACE_EXTENSIONS . '\\' . str_replace('/', '\\', $extension);
		
		// Extract the basename of the given $extension to be used as the method name, in case the extension is grouped with other extensions in a subdirectory.
		$method = basename($extension);
		
		// Building the extension's file path.
		$file = AM_BASE_DIR . strtolower(str_replace('\\', '/', $class) . '/' . $method) . '.php';
		
		if (file_exists($file)) {
							
			// Load class.				
			Debug::log($file, 'Loading');
			require_once $file;
			
			if (class_exists($class, false)) {
				
				// Create instance of class dynamically.
				$object = new $class();
				Debug::log($class, 'New instance created of');
				
				if (method_exists($object, $method)) {
					
					// Collect assets.
					$this->collectAssets($extension);
					
					// Call method dynamically and pass $options & Automad. The returned output will be stored in $this->output.
					Debug::log($options, 'Calling method "' . $method . '" and passing the following options');
					$this->output = $object->$method($options, $Automad);
		
				} else {
					
					Debug::log($method, 'Method not existing in class "' . $class . '"');	
				
				}
		
			} else {
				
				Debug::log($class, 'Class not existing');		
			
			}
		
		} else {
			
			Debug::log($file, 'File not found');
		
		}
				
	}
	
	
	/**
	 * 	Collect all assets (CSS & JS files) belonging to $extension and store them in $this->assets.
	 *	
	 *	@param string $extension
	 */
	
	private function collectAssets($extension) {
		
		$path = AM_BASE_DIR . strtolower(str_replace('\\', '/', AM_NAMESPACE_EXTENSIONS) . '/' . $extension);
		
		Debug::log($path, 'Getting assets for "' . $extension . '" in');
		
		foreach (array('.css', '.js') as $type) {
			
			foreach (glob($path . '/*' . $type) as $file) {
				
				// Only add the non-minified version, if no minified version exists.
				if (!file_exists(str_replace($type, '.min' . $type, $file))) {
					
					// Remove base directory from file path.
					$file = str_replace(AM_BASE_DIR, '', $file);
					
					// Use $file also as key to keep elemtens unique.
					$this->assets[$type][$file] = $file;
					Debug::log($file, 'Adding ' . basename($file) . ' for ' . $extension);
					
				} else {
					
					Debug::log($file, 'Skipping ' . basename($file) . ' for ' . $extension . ' due to minified version');
					
				}
						
			}
			
		}
		
	}
	
		
	/**
	 *	Return an array of assets of the extension.
	 *
	 *	@return array The array of files
	 */
	
	public function getAssets() {
		
		return $this->assets;
		
	}
	
	
	/**
	 *	Return the output of the extension.
	 *
	 *	@return string The output returned by the extension
	 */
	
	public function getOutput() {
		
		return $this->output;
		
	}
	
		
}
