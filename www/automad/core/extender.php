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
 *	The Extender class serves as an interface for calling extension methods via the template syntax.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Extender {

	
	/**
	 *	The Automad object.
	 */
	
	private $Automad;

	
	/**
	 *	The constructor just makes the Automad object available.
	 */

	public function __construct($Automad) {
		
		$this->Automad = $Automad;
		
	}

	
	/**
	 *	Scan $output for extensions and add all additional CSS/JS files to the page's <head>.
	 *	First, all the used extensions get collected in the $extensions[1] array.
	 *	Second, the directory of each extension gets scanned for .css and .js files. These files get collected in $css and $js.
	 *	Third, each item in $css and $js get appended to the opening <head> tag of $output.
	 *
	 *	@param string $output
	 *	@return $output - The full HTML including the linked css/js files.
	 */

	public function addHeaderElements($output) {
		
		$css = array();
		$js = array();
		$html = '';
		
		// Find extensions in $output.
		preg_match_all(AM_REGEX_XTNSN, $output, $extensions);
			
		// Collect all css/js files in each extension directory.
		foreach ($extensions[1] as $extension) {
			
			// Extension directory	
			$path = AM_BASE_DIR . strtolower(str_replace('\\', '/', AM_NAMESPACE_EXTENSIONS) . '/' . $extension);
			
			Debug::log('Extender: Getting CSS/JS for "' . $extension . '" in: ' . $path);
			
			// Get CSS files
			if ($c = glob($path . '/*.css')) {
				$css = array_merge($css, $c);
			}
			
			// Get JS files
			if ($j = glob($path . '/*.js')) {
				$js = array_merge($js, $j);
			}
			
		}
		
		// Clean up arrays
		$css = array_unique($css);
		$js = array_unique($js);
		
		// Add the HTML for all items to the $html string.
		foreach ($css as $item) {
			
			// Test for a minified version.
			// If a minified is existing, the uncompressed file gets skipped.
			// If $item is already the minified version, the condition will be false, because it will test "filename.min.min.css", 
			// and the "filename.min.css" will be added just fine.
			if (!file_exists(str_replace('.css', '.min.css', $item))) {
			
				$html .= "\t" . '<link type="text/css" rel="stylesheet" href="' . str_replace(AM_BASE_DIR, '', $item) . '" />' . "\n";
				Debug::log('Extender: Added "' . $item . '" to header');	
			
			} else {
				
				Debug::log('Extender: Skipped "' . $item . '" - Loading minified version instead');
				
			}
	
	
		}
				
		foreach ($js as $item) {
			
			// Test for a minified version. 
			// Just like testing for ".min.css" above.
			if (!file_exists(str_replace('.js', '.min.js', $item))) {
				
				$html .= "\t" . '<script type="text/javascript" src="' . str_replace(AM_BASE_DIR, '', $item) . '"></script>' . "\n";
				Debug::log('Extender: Added "' . $item . '" to header');
			
			} else {
				
				Debug::log('Extender: Skipped "' . $item . '" - Loading minified version instead');
				
			}
		}
		
		// Prepend all items ($html) to the closing </head> tag.
		return str_replace('</head>', $html . '</head>', $output);
		
	}
	
	
	/**
	 *	Call extension method from template dynamically.
	 *
	 *	@param string $name
	 *	@param array $options
	 *	@return The returned value from the called method
	 */
	
	public function callExtension($name, $options) {
		
		// Adding the extension namespace to the called class here, to make sure,
		// that only classes from the /extensions directory and within the \Extension namespace get used.
		$class = AM_NAMESPACE_EXTENSIONS . '\\' . $name;
		
		// Building the extension's file path.
		$file = AM_BASE_DIR . strtolower(str_replace('\\', '/', $class) . '/' . $name) . '.php';
		
		if (file_exists($file)) {
							
			// Load class.				
			Debug::log('Extender: Require once ' . $file);
			require_once $file;
			
			if (class_exists($class, false)) {
				
				// Create instance of class dynamically.
				$object = new $class();
				Debug::log('Extender: Created instance of class "' . $class . '"');
		
				if (method_exists($object, $name)) {
					
					// Call method dynamically and pass $options & Automad.
					Debug::log('Extender: Calling method "' . $name . '" and passing the following options:');
					Debug::log($options);
					return $object->$name($options, $this->Automad);
		
				} else {
					
					Debug::log('Extender: Method "' . $name . '" not existing!');	
				
				}
		
			} else {
				
				Debug::log('Extender: Class "' . $class . '" not existing!');		
			
			}
		
		} else {
			
			Debug::log('Extender: ' . $file . ' not found!');
		
		}
		
	}
	
	
}


?>