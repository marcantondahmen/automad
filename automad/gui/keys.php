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
 *	The Keys class provides all methods to search all kind of content variables (keys of the data array) used in templates. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
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
	 *	Find all variable keys in the currently used template and all included snippets (and ignore those keys in $this->reserved).
	 *	
	 *	@return array Keys in the currently used template (without reserved keys)
	 */
	
	public function inCurrentTemplate() {
		
		$Page = $this->Automad->Context->get();
		// Don't use $Page->getTemplate() to prevent exit on errors.
		$file = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $Page->get(AM_KEY_THEME) . '/' . $Page->template . '.php';
		
		return $this->inTemplate($file);
		
	}


	/**
	 *	Find all variable keys in a template and all included snippets (and ignore those keys in $this->reserved).
	 *	
	 *	@param string $file
	 *	@return array Keys in a given template (without reserved keys)
	 */
	
	public function inTemplate($file) {
		
		$keys = array();
	
		if (file_exists($file)) {
			
			// Find all variable keys in the template file.
			$content = file_get_contents($file);
			// Remove ~ characters to match includes correctly.
			$content = str_replace(
				array(AM_DEL_STATEMENT_OPEN . '~', '~' . AM_DEL_STATEMENT_CLOSE), 
				array(AM_DEL_STATEMENT_OPEN, AM_DEL_STATEMENT_CLOSE), 
				$content
			);
			preg_match_all('/' . Core\Regex::variableKeyGUI() . '/is', $content, $matches);
			$keys = $matches['varName'];
			
			// Match markup to get includes recursively.
			preg_match_all('/' . Core\Regex::markup() . '/is', $content, $matches, PREG_SET_ORDER);
		
			foreach ($matches as $match) {
			
				// Recursive include.
				if (!empty($match['file'])) {
	
					$include = dirname($file) . '/' . $match['file'];

					if (file_exists($include)) {
						$keys = array_merge($keys, $this->inTemplate($include));
					} 
		
				}
			
			}
			
			$keys = $this->sortAndFilter($keys);
		
		}
			
		return $keys;
		
	}


	/**
	 *	Find all variable keys in templates of a given theme.
	 *	
	 * 	@param object $Theme
	 * 	@return array Keys in all templates of the given Theme (without reserved keys)
	 */
	
	public function inTheme($Theme) {
		
		$keys = array();
		
		foreach ($Theme->templates as $file) {
			$keys = array_merge($keys, $this->inTemplate($file));
		}
		
		return $this->sortAndFilter($keys);
		
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
	
		// Place all block and text keys in the beginning of the array
		// and only sort all non-text keys alphabetically.
		$textKeys = array_filter($keys, function($key) {
			return preg_match('/^(text|\+)/', $key);
		});
		
		$nonTextKeys = array_filter($keys, function($key) {
			return (preg_match('/^(text|\+)/', $key) == false);
		});
	
		sort($nonTextKeys);
		
		$keys = array_merge($textKeys, $nonTextKeys);
	
		return array_unique($keys);
		
	}
	
		
}
