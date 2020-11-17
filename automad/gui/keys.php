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
	 *	Array with reserved variable keys.
	 */
	
	public static $reserved = array(
		AM_KEY_DATE, 
		AM_KEY_HIDDEN, 
		AM_KEY_PRIVATE,
		AM_KEY_TAGS, 
		AM_KEY_THEME, 
		AM_KEY_TITLE, 
		AM_KEY_SITENAME, 
		AM_KEY_URL
	);
	

	/**
	 * 	Get text variable keys from an array of keys.
	 * 
	 *	@param array $keys
	 *	@return array The array with only text variables.
	 */

	public static function filterTextKeys($keys) {

		return array_filter($keys, function($key) {
			return preg_match('/^(text|\+)/', $key);
		});

	}


	/**
	 * 	Get settings variable keys from an array of keys.
	 * 
	 *	@param array $keys
	 *	@return array The array with only settings variables.
	 */

	public static function filterSettingKeys($keys) {

		sort($keys);

		return array_filter($keys, function($key) {
			return (preg_match('/^(text|\+)/', $key) == false);
		});

	}

	
	/**
	 *	Find all variable keys in the currently used template and all included snippets (and ignore those keys in $this->reserved).
	 *	
	 *	@param object $Page
	 *	@param object $Theme
	 *	@return array Keys in the currently used template (without reserved keys)
	 */
	
	public static function inCurrentTemplate($Page, $Theme) {
		
		// Don't use $Page->getTemplate() to prevent exit on errors.
		$file = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $Page->get(AM_KEY_THEME) . '/' . $Page->template . '.php';
		$keys = self::inTemplate($file);
		
		return self::cleanUp($keys, $Theme->getMask('page'));
		
	}


	/**
	 *	Find all variable keys in a template and all included snippets (and ignore those keys in $this->reserved).
	 *	
	 *	@param string $file
	 *	@return array Keys in a given template (without reserved keys)
	 */
	
	public static function inTemplate($file) {
		
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
						$keys = array_merge($keys, self::inTemplate($include));
					} 
		
				}
			
			}
			
			$keys = self::cleanUp($keys);
		
		}
			
		return $keys;
		
	}


	/**
	 *	Find all variable keys in templates of a given theme.
	 *	
	 *	@param object $Theme
	 *	@return array Keys in all templates of the given Theme (without reserved keys)
	 */
	
	public static function inTheme($Theme) {
		
		$keys = array();
		
		foreach ($Theme->templates as $file) {
			$keys = array_merge($keys, self::inTemplate($file));
		}

		return self::cleanUp($keys, $Theme->getMask('shared'));

	}
	
	
	/**
	 *	Cleans up an array of keys. All reserved and duplicate keys get removed 
	 *	and the optional UI mask is applied.
	 * 
	 *	@param array $keys
	 *	@param array $mask
	 *	@return array The sorted and filtered keys array
	 */

	private static function cleanUp($keys, $mask = array()) {
	
		if (!empty($mask)) {
			$keys = array_filter($keys, function($key) use ($mask) {
				return !in_array($key, $mask);
			});
		}
		
		return array_unique(array_diff($keys, self::$reserved));
		
	}
	
		
}
