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
 *	Copyright (c) 2016-2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Keys class provides all methods to search all kind of content variables (keys of the data array) used in templates. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2018 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Keys {
	
	
	/**
	 *	The Automad object.
	 */
	
	private $Automad;
	
	
	/**
	 *	Array with reserved variable keys.
	 */
	
	public $reserved = array(AM_KEY_HIDDEN, AM_KEY_TAGS, AM_KEY_THEME, AM_KEY_TITLE, AM_KEY_SITENAME, AM_KEY_URL);
	
	
	/**
	 *	Set $this->Automad when creating an instance.
	 *
	 *	@param object $Automad
	 */
	
	public function __construct($Automad) {
		
		$this->Automad = $Automad;
		
	}
	
	
	/**
	 *	Find all variable keys in the currently used template and all included templates (and ignore those keys in $this->reserved).
	 *	
	 *	@param string $file
	 *	@return array with keys in the currently used template (without reserved keys)
	 */
	
	public function inCurrentTemplate($file = false) {
		
		$keys = array();
		
		// Since this is a recursive method, initially there should not be any file defined and the template from the requested page should be used instead.
		if (!$file) {
			$Page = $this->Automad->Context->get();
			// Don't use $Page->getTemplate() to prevent exit on errors.
			$file = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $Page->get(AM_KEY_THEME) . '/' . $Page->template . '.php';
		}
		
		if (file_exists($file)) {
			
			// Find all variable keys in the template file.
			$content = file_get_contents($file);
			preg_match_all('/' . Core\Regex::variableKeyGUI() . '/is', $content, $matches);
			$keys = $matches['varName'];
			
			// Match markup to get includes recursively.
			preg_match_all('/' . Core\Regex::markup() . '/is', $this->Automad->loadTemplate($file), $matches, PREG_SET_ORDER);
		
			foreach ($matches as $match) {
			
				// Recursive include.
				if (!empty($match['file'])) {
	
					$include = dirname($file) . '/' . $match['file'];

					if (file_exists($include)) {
						$keys = array_merge($keys, $this->inCurrentTemplate($include));
					} 
		
				}
			
			}
			
			$keys = $this->sortAndFilter($keys);
		
		}
			
		return $keys;
		
	}


	/**
	 *	Find all variable keys in all templates (and ignore those keys in $this->reserved).
	 *	
	 *	@return array with keys in the currently used template (without reserved keys)
	 */

	public function inAllTemplates() {
		
		$keys = array();
		$dir = AM_BASE_DIR . AM_DIR_PACKAGES;	
		$arrayDirs = array();
		$arrayFiles = array();
		
		// Collect all directories in "/packages" recursively.
		while ($dirs = glob($dir . '/*', GLOB_ONLYDIR)) {
			$dir .= '/*';
			$arrayDirs = array_merge($arrayDirs, $dirs);
		}
		
		// Filter out directories.
		$arrayDirs = array_filter($arrayDirs, function($array) {
			return preg_match('/\/(dist|js|less|node_modules)(\/|$)/', $array) == 0;
		});
	
		// Collect all .php files.
		foreach ($arrayDirs as $d) {
			if ($f = glob($d . '/*.php')) {
				$arrayFiles = array_merge($arrayFiles, $f);
			}
		}
		
		// Search each template and add matches to the $keys array.
		foreach ($arrayFiles as $file) {
			$content = file_get_contents($file);
			preg_match_all('/' . Core\Regex::variableKeyGUI() . '/is', $content, $matches);
			$keys = array_merge($keys, $matches['varName']);
		}
		
		return $this->sortAndFilter($keys);
		
	}
	
	
	/**
	 *	Find all variable keys in all other templates but the current (and ignore those keys in $this->reserved).
	 *	
	 *	@return array with keys in the currently used template (without reserved keys)
	 */
	
	public function inOtherTemplates() {
		
		return array_diff($this->inAllTemplates(), $this->inCurrentTemplate());
		
	}
	
	
	/**
	 *	Sorts and filters a keys array. All text variable keys get placed in the
	 *	beginning of the returned array and are not sorted. All non-text variable keys 
	 *	are sorted alphabetically.
	 * 
	 * 	@param array $keys
	 * 	@return array The sorted and filtered keys array
	 */
	
	private function sortAndFilter($keys) {
	
		// Remove reserved keys.
		$keys = array_diff($keys, $this->reserved);
	
		// Place all text keys in the beginning of the array
		// and only sort all non-text keys alphabetically.
		$textKeys = array_filter($keys, function($array) {
			return strpos($array, 'text') === 0;
		});
		
		$nonTextKeys = array_filter($keys, function($array) {
			return strpos($array, 'text') !== 0;
		});
	
		sort($nonTextKeys);
		
		$keys = array_merge($textKeys, $nonTextKeys);
	
		return array_unique($keys);
		
	}
	
		
}
