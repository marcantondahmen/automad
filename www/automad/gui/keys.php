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
 *	Copyright (c) 2016 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Keys class provides all methods to search all kind of content variables (keys of the data array) used in templates. 
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2016 Marc Anton Dahmen <hello@marcdahmen.de>
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
	 */
	
	public function __construct($Automad) {
		
		$this->Automad = $Automad;
		
	}
	
	
	/**
	 *	Find all variable keys in the currently used template and all included templates (and ignore those keys in $this->reserved).
	 *	
	 *	@param string $file
	 *	@return Array with keys in the currently used template (without reserved keys)
	 */
	
	public function inCurrentTemplate($file = false) {
		
		$keys = array();
		
		// Since this is a recursive method, initially there should not be any file defined and the template from the requested page should be used instead.
		if (!$file) {
			$Page = $this->Automad->getRequestedPage();
			$file = $Page->getTemplate();
		}
		
		$directory = dirname($file);

		preg_replace_callback('/' . \Automad\Core\Regex::markup() . '/is', function($matches) use ($directory, &$keys) {
		
			// Variable key.
			if (!empty($matches['var'])) {
				preg_match('/' . \Automad\Core\Regex::contentVariable('var') . '/s' ,$matches['var'], $var);
				$keys[] = $var['varName'];
			}
			
			// Recursive include.
			if (!empty($matches['file'])) {
	
				$file = $directory . '/' . $matches['file'];

				if (file_exists($file)) {
					$keys = array_merge($keys, $this->inCurrentTemplate($file));
				} 
		
			}
		
		}, $this->Automad->loadTemplate($file));
		
		// Remove system vars and query string parameters.
		$keys = array_filter($keys, function($key) {
			return (strpos($key, ':') !== 0 && strpos($key, '?') !== 0);
		});
		
		// Remove reserved keys.
		$keys = array_diff($keys, $this->reserved);
		
		sort($keys);
		
		return array_unique($keys);
		
	}


	/**
	 *	Find all variable keys in all templates (and ignore those keys in $this->reserved).
	 *	
	 *	@return Array with keys in the currently used template (without reserved keys)
	 */

	public function inAllTemplates() {
		
		$keys = array();
		
		// Collect all .php files below "/themes"
		$dir = AM_BASE_DIR . AM_DIR_THEMES;	
		$arrayDirs = array();
		$arrayFiles = array();
		
		while ($dirs = glob($dir . '/*', GLOB_ONLYDIR)) {
			$dir .= '/*';
			$arrayDirs = array_merge($arrayDirs, $dirs);
		}
		
		foreach ($arrayDirs as $d) {
			if ($f = glob($d . '/*.php')) {
				$arrayFiles = array_merge($arrayFiles, $f);
			}
		}
		
		// Search each template and add matches to the $keys array.
		foreach ($arrayFiles as $file) {
			$content = file_get_contents($file);
			preg_match_all('/' . \Automad\Core\Regex::contentVariable('var') . '/is', $content, $matches);
			$keys = array_merge($keys, $matches['varName']);
		}
 		
		// Remove system vars and query string parameters.
		$keys = array_filter($keys, function($key) {
			return (strpos($key, ':') !== 0 && strpos($key, '?') !== 0);
		});
		
		// Remove reserved keys.
		$keys = array_diff($keys, $this->reserved);
		
		sort($keys);
		
		return array_unique($keys);
		
	}
	
	
	/**
	 *	Find all variable keys in all other templates but the current (and ignore those keys in $this->reserved).
	 *	
	 *	@return Array with keys in the currently used template (without reserved keys)
	 */
	
	public function inOtherTemplates() {
		
		return array_diff($this->inAllTemplates(), $this->inCurrentTemplate());
		
	}
	
	
}


?>