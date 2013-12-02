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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Extender class servers as an interface for calling extension methods via the template syntax.
 *
 *	
 */


class Extender {

	
	/**
	 *	The Site object.
	 */
	
	private $S;

	
	/**
	 *	The constructor just makes $S available.
	 */

	public function __construct($site) {
		
		$this->S = $site;
		
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
		preg_match_all('/' . preg_quote(AM_TMPLT_DEL_XTNSN_L) . '(.+)::.*' . preg_quote(AM_TMPLT_DEL_XTNSN_R) . '/', $output, $extensions);
			
		// Collect all css/js files in each extension directory.
		foreach ($extensions[1] as $extension) {
			
			Debug::log('Extender: Getting CSS/JS for "' . $extension . '"');	
			
			// Extension directory	
			$path = dirname(AM_BASE_DIR . AM_DIR_EXTENSIONS . '/' . str_replace('\\', '/', $extension));
			
			// Get files
			$css = array_merge($css, glob($path . '/*.css'));
			$js = array_merge($js, glob($path . '/*.js'));	
			
		}
		
		// Clean up arrays
		$css = array_unique($css);
		$js = array_unique($js);
		
		// Add the HTML for all items to the $html string.
		foreach ($css as $item) {
			$html .= "\t" . '<link type="text/css" rel="stylesheet" href="' . str_replace(AM_BASE_DIR, '', $item) . '" />' . "\n";
			Debug::log('Extender: Added "' . $item . '" to header');	
		}
				
		foreach ($js as $item) {	
			$html .= "\t" . '<script type="text/javascript" src="' . str_replace(AM_BASE_DIR, '', $item) . '"></script>' . "\n";
			Debug::log('Extender: Added "' . $item . '" to header');
		}
		
		// Prepend all items ($html) to the closing </head> tag.
		return str_replace('</head>', $html . '</head>', $output);
		
	}
	
	
	/**
	 *	Call extension method from template dynamically.
	 *
	 *	@param string $class
	 *	@param string $method
	 *	@param string $optionStr
	 *	@return The returned value from the called method
	 */
	
	public function callMethod($class, $method, $optionStr) {
		
		// Building the extension's file path.
		$file = AM_BASE_DIR . AM_DIR_EXTENSIONS . '/' . strtolower(str_replace('\\', '/', $class)) . '.php';
		
		if (file_exists($file)) {
							
			// Load class.				
			Debug::log('Extender: Loading ' . $file);
			require_once $file;
			
			if (class_exists($class)) {
				
				// Create instance of class dynamically.
				// The Site object gets passed to be available within the extension.
				$extension = new $class($this->S);
				Debug::log('Extender: Created instance of class "' . $class . '"');
		
				if (method_exists($extension, $method)) {
					
					// Parse options
					$options = Parse::toolOptions($optionStr);
					
					// Call method dynamically and pass $options.
					Debug::log('Extender: Calling method "' . $method . '"');
					return $extension->$method($options);
		
				} else {
					Debug::log('Extender: Method "' . $method . '" not existing!');	
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