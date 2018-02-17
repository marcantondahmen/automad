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
 *	Copyright (c) 2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Themelist class.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2018 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Themelist {
	
	
	/**
	 * 	The Theme objects array.
	 */
	
	private $themes = false;
	
	
	/**
	 * 	The constructor.
	 */
	
	public function __construct() {
		
		Core\Debug::log($this, 'New instance created');
		
	}


	/**
	 * 	Collect installed themes recursively.
	 *
	 *	A theme must be located below the "themes" directory.   
	 *	It is possible to group themes in subdirectories, like "themes/theme" or "themes/subdir/theme".
	 * 
	 *	To be a valid theme, a diretcory must contain a "theme.json" file and at least one ".php" file.
	 *      
	 *	@param string $path
	 *  @return array An array containing all themes as objects.
	 */
	
	private function collectThemes($path = false) {
		
		if (!$path) {
			$path = AM_BASE_DIR . AM_DIR_THEMES;
		}
		
		$themes = array();

		foreach (glob($path . '/*', GLOB_ONLYDIR) as $dir) {
			
			$themeJSON = $dir . '/theme.json';
			$templates = glob($dir . '/*.php');
			
			if (is_readable($themeJSON) && is_array($templates) && $templates) {
				
				// If a theme.json file and at least one .php file exist, use that directoy as a theme.
				$path = Core\Str::stripStart(dirname($themeJSON), AM_BASE_DIR . AM_DIR_THEMES . '/');
				$themes[$path] = new Theme($themeJSON);
				
			} else {
				
				// Else check subdirectories for theme.json files.
				$themes = array_merge($themes, $this->collectThemes($dir));
				
			}
			
		}
		
		return $themes;
		
	}
	
	
	/**
	 * 	Return the Theme objects array. 
	 * 	
	 * 	In case the method is called the first time, 
	 * 	all themes get collected from the theme directory first.
	 *
	 * 	@return array The array of Theme objects
	 */
	
	public function getThemes() {
		
		if (!$this->themes) {
			$this->themes = $this->collectThemes();
			Core\Debug::log($this->themes);
		}
		
		return $this->themes;
		
	}
	
	
}
