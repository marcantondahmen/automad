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


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Extension class provides an interface for calling an extension from a template file.
 *	There are two options for autoloading extension classes:
 *
 *	1. Composer autoloading:
 *	Composer packages are autoloaded according to the given settings in the package's composer.json file.
 * 
 *	2. Local extensions with simple autoloading:    
 *	A local extension is basically called by the subdirectory name within the "/packages" directory. 
 *	The file name within that subdirectory must have the basename of that directory followed by ".php".
 *	Both, class and method name, must be the basename of the directory as well.    
 *	In case of extensions grouped in a subdirectory of "/packages", the name of that directory has to be the namespace, 
 *	in that way that the namespace reflects the actual directory structure without the last directory containing the actual .php file.    
 *        		
 *	Example 1 - Single extension:     
 *	An extension call like <@ example1 @> would load the file "/packages/example1/example1.php", 
 *	create an instance of the class "\example1" ($object) and call the method "$object->example1()" of that class.
 *	The namespace would just be "\".  
 *	The full naming scheme would be:    
 *	- namespace:	\
 *	- directory:	/packages/example1 (must be lowercase)
 *	- file:			/packages/example1/example1.php (must be lowercase)
 *	- class:		Example1
 *	- method:		Example1
 *          
 *	Example 2 - Extension in a subdirectory (like a vendor name):     
 *	An extension call like <@ vendor/example2 @> would load the file "/packages/vendor/example2/example2.php",
 *	create an instance of the class "\vendor\example2" ($object) and call the method "$object->example2()" of that class.
 *	The namespace in this case would be "\vendor". 
 *	The full naming scheme would be:    
 *	- namespace:	\vendor   
 *	- directory:	/packages/vendor/example2 (must be lowercase)
 *	- file:			/packages/vendor/example2/example2.php (must be lowercase)
 *	- class:		Example2
 *	- method:		Example2
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
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
		$class = '\\' . str_replace('/', '\\', $extension);
		
		// Extract the basename of the given $extension to be used as the method name, in case the extension is grouped with other extensions in a vendor directory.
		$method = basename($extension);
		
		// Check if class is autoloaded.
		if (!class_exists($class)) {
			
			// Load class file in case extension is not a Composer packages.
			// Building the extension's file path.
			$file = AM_BASE_DIR . AM_DIR_PACKAGES . strtolower(FileSystem::normalizeSlashes($class) . '/' . $method) . '.php';
			
			if (file_exists($file)) {
				
				// Load class.				
				Debug::log($file, 'Class is not autoloaded. Loading');
				require_once $file;
				
			} else {
				
				Debug::log($file, 'File not found');
			
			}
			
		} else {
			
			Debug::log($class, 'Class is a Composer package');
			
		}
		
		// Check again if the class exists after autoloading has finished.
		if (class_exists($class, false)) {
			
			// Create instance of class dynamically.
			$object = new $class();
			Debug::log($class, 'New instance created of');
			
			if (method_exists($object, $method)) {
				
				// Collect assets.
				$this->collectAssets($class);
				
				// Call method dynamically and pass $options & Automad. The returned output will be stored in $this->output.
				Debug::log($options, 'Calling method ' . $method . ' and passing the following options');
				$this->output = $object->$method($options, $Automad);
	
			} else {
				
				Debug::log($method, 'Method not existing in class ' . $class);	
			
			}
	
		} else {
			
			Debug::log($class, 'Class not existing');		
		
		}
				
	}
	
	
	/**
	 * 	Collect all assets (CSS & JS files) belonging to $class and store them in $this->assets.
	 *	
	 *	@param string $class
	 */
	
	private function collectAssets($class) {
		
		$Reflection = new \ReflectionClass($class);
		$path = dirname($Reflection->getFileName());
		Debug::log($path, 'Getting assets for ' . $class . ' in');
		
		foreach (array('.css', '.js') as $type) {
			
			foreach (FileSystem::glob($path . '/*' . $type) as $file) {
				
				// Only add the non-minified version, if no minified version exists.
				if (!file_exists(str_replace($type, '.min' . $type, $file))) {
					
					// Remove base directory from file path.
					$file = Str::stripStart($file, AM_BASE_DIR);
					
					// Use $file also as key to keep elemtens unique.
					$this->assets[$type][$file] = $file;
					Debug::log($file, 'Adding ' . basename($file) . ' for ' . $class);
					
				} else {
					
					Debug::log($file, 'Skipping ' . basename($file) . ' for ' . $class . ' due to minified version');
					
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
