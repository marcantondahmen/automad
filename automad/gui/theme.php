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
 *	The Theme class.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2018 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Theme {
	
	
	/**
	 * 	Theme data.
	 */
	
	public $data = array();
	
	
	/**
	 * 	The constructor.
	 *
	 * 	@param string $path
	 */
	
	public function __construct($themeJSON) {
		
		$json = false;
		$path = Core\Str::stripStart(dirname($themeJSON), AM_BASE_DIR . AM_DIR_PACKAGES . '/');
		$defaults = array(
						'name' => $path, 
						'description' => false, 
						'author' => false, 
						'version' => false, 
						'license' => false,
						'tooltips' => array()
					);
		
		if (is_readable($themeJSON)) {
			$json = @json_decode(file_get_contents($themeJSON), true);
		}
		
		if (!is_array($json)) {
			$json = array();
		}
		
		$templates = glob(dirname($themeJSON) . '/*.php');
		
		// Remove the 'page not found' template from the array of templates. 
		$templates = array_filter($templates, function($file) {
			return false === in_array(basename($file), array(AM_PAGE_NOT_FOUND_TEMPLATE . '.php'));
		});
		
		$this->data = array_merge(
						$defaults, 
						$json, 
						array(
							'path' => $path,
							'templates' => $templates
						)
					);
		
	}
	
	
	/**
	 *	Make theme data accessible as page properties.
	 *      
	 * 	@param string $key The property name
	 * 	@return string The returned value from the data array
	 */
	
	public function __get($key) {
		
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		} 
		
	}
	
	
	/**
	 * 	Return the tooltip for the requested variable name (key in the data array).
	 *
	 * 	@param string $key
	 * 	@return string The tooltip text
	 */
	
	public function getTooltip($key) {
		
		if (array_key_exists($key, $this->data['tooltips'])) {
			return htmlentities(htmlentities($this->data['tooltips'][$key]));
		}
		
	}
	
	
}
