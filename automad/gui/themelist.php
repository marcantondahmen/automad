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
 *	Copyright (c) 2018-2020 by Marc Anton Dahmen
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
 *	@copyright Copyright (c) 2018-2019 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Themelist {
	
	
	/**
	 *	An array of installed Composer packages.
	 */
	
	private $composerInstalled;
	
	
	/**
	 * 	The Theme objects array.
	 */
	
	private $themes;
	
	
	/**
	 * 	The constructor.
	 */
	
	public function __construct() {
		
		$this->composerInstalled = $this->getComposerInstalled();
		$this->themes = $this->collectThemes();
		Core\Debug::log($this->themes, 'New instance created');
		
	}


	/**
	 * 	Collect installed themes recursively.
	 *
	 *	A theme must be located below the "themes" directory.   
	 *	It is possible to group themes in subdirectories, like "themes/theme" or "themes/subdir/theme".
	 * 
	 *	To be a valid theme, a directory must contain a "theme.json" file and at least one ".php" file.
	 *      
	 *	@param string $path
	 *	@return array An array containing all themes as objects.
	 */
	
	private function collectThemes($path = false) {
		
		if (!$path) {
			$path = AM_BASE_DIR . AM_DIR_PACKAGES;
		}
		
		$themes = array();

		foreach (FileSystem::glob($path . '/*', GLOB_ONLYDIR) as $dir) {
			
			$themeJSON = $dir . '/theme.json';
			$templates = FileSystem::glob($dir . '/*.php');
			
			if (is_readable($themeJSON) && is_array($templates) && $templates) {
				
				// If a theme.json file and at least one .php file exist, use that directoy as a theme.
				$path = Core\Str::stripStart(dirname($themeJSON), AM_BASE_DIR . AM_DIR_PACKAGES . '/');
				$themes[$path] = new Theme($themeJSON, $this->composerInstalled);
				
			} else {
				
				// Else check subdirectories for theme.json files.
				$themes = array_merge($themes, $this->collectThemes($dir));
				
			}
			
		}
		
		return $themes;
		
	}
	
	
	/**
	 *	Get installed Composer packages.
	 *	
	 *	@return array An associative array of installed Composer packages
	 */
	
	private function getComposerInstalled() {
		
		$installedJSON = AM_BASE_DIR . '/vendor/composer/installed.json';
		$installed = array();

		if (is_readable($installedJSON)) {
			
			$decoded = @json_decode(file_get_contents($installedJSON), true);
			
			if (is_array($decoded) && !empty($decoded['packages'])) {
				$packages = $decoded['packages'];
				foreach ($packages as $package) {
					if (array_key_exists('name', $package)) {
						$name = $package['name'];
						$installed[$name] = $package;
					}
				}
			}
			
		} 

		return $installed;
				
	}
	
	
	/**
	 * 	Get the theme object by the key in the themelist array 
	 * 	corresponding to the AM_KEY_THEME variable.
	 *
	 *	@param string $key
	 *	@return object The requested theme object
	 */
	
	public function getThemeByKey($key) {
		
		if ($key && array_key_exists($key, $this->themes)) {
			return $this->themes[$key];
		} 
		
	}
	
	
	/**
	 * 	Return the Theme objects array. 
	 *
	 *	@return array The array of Theme objects
	 */
	
	public function getThemes() {
		
		return $this->themes;
		
	}
	
	
}
