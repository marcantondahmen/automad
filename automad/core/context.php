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
 *	Copyright (c) 2015-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Context represents the current page within statements (loops) or just the requested page.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2015-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Context {
	
	
	/**
	 * 	The context Page.
	 */
	
	private $Page;
	
	
	/**
	 * 	The constructor.
	 *
	 *	@param object $Page
	 */
	
	public function __construct($Page) {
		
		$this->set($Page);
		
	}
	
	
	/**
	 * 	Return $Page.
	 *
	 *	@return object $Page
	 */
	
	public function get() {
		
		return $this->Page;
		
	}
	
	
	/**
	 * 	Set the context.
	 *
	 *	@param object $Page
	 */
	
	public function set($Page) {
		
		// Test whether $Page is empty - that can happen, when accessing the GUI.
		if (!empty($Page)) {
			$this->Page = $Page;
			Debug::log($Page, 'Set context to ' . $Page->url);
		}
		
	}
	
	
}
