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
 *	Copyright (c) 2017-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Runtime class handles all variables generated during runtime.
 *	Those variables are created by Automad at runtime and independent from the Context and Shared objects.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2017-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Runtime {
	
	
	/**
	 *	The Automad object.
	 */
	
	private $Automad;
	
	
	/**
	 *	The runtime data array.
	 */
	
	private $data = array();
	
	
	/**
	 *	The constructor.
	 *
	 *	@param object $Automad
	 */
	
	public function __construct($Automad) {
		
		$this->Automad = $Automad;
		Debug::log('Created new instance');
		
	}
	
	
	/**
	 *	Return the requested runtime variable. 
	 *
	 *	@param string $key
	 *	@return string The value of $key or NULL if the requested variable doesn't exsist
	 */
	
	public function get($key) {
		
		// Check whether $key is generated within a loop and therefore stored in $this->data or
		// if $key is related to the context, filelist or pagelist object.
		if (array_key_exists($key, $this->data)) {
			
			return $this->data[$key];
			
		} else {
			
			switch ($key) {
				
				case AM_KEY_NOW:
					// The current date/time.
					return date(DATE_ATOM);
					
				case AM_KEY_FILELIST_COUNT:
					// The filelist count represents the number of files within the last defined filelist. 
					return count($this->Automad->getFilelist()->getFiles());
					
				case AM_KEY_PAGELIST_COUNT:
					// The pagelist count represents the number of pages within the last defined pagelist, ignoring limit and pagination. 
					return count($this->Automad->getPagelist()->getPages(true));
				
				case AM_KEY_PAGELIST_DISPLAY_COUNT:
					// The pagelist display count represents the number of pages that will be displayed. 
					return count($this->Automad->getPagelist()->getPages(false));	

				case AM_KEY_PAGINATION_COUNT:
					// The last page number of the pagination of the current pagelist.
					return $this->Automad->getPagelist()->getPaginationCount();
				
				case AM_KEY_CAPTION:
					// Get the caption for the currently used ":file".
					// In case ":file" is "image.jpg", the parsed caption file is "image.jpg.caption" and the returned value is stored in ":caption".
					if (isset($this->data[AM_KEY_FILE])) {
						return Parse::caption(AM_BASE_DIR . $this->data[AM_KEY_FILE]);
					} else {
						return false;
					}
				
				default:
					// Return NULL in case the requested $key doesn't match any runtime variable
					// to distinguish false values from not existing keys.
					return NULL;
					
			}
				
		}
	
	}

	
	/**
	 *	Set a runtime variable.
	 *	
	 *	@param string $key
	 *	@param mixed $value
	 */

	public function set($key, $value) {
		
		$this->data[$key] = $value;
		
	}
	
	
	/**
	 *	Get the $data array to be shelved and restored at a later point.
	 *
	 *	@return array The runtime data array
	 */
	
	public function shelve() {
		
		Debug::log($this->data, 'Shelving');
		return $this->data;
		
	}
	
	
	/**
	 *	Restore shelved $data array by replacing it with the passed $shelf array.
	 *
	 *	@param array $shelf
	 */
	
	public function unshelve($shelf) {
		
		$this->data = $shelf;
		Debug::log($this->data, 'Unshelving');
		
	}


}
