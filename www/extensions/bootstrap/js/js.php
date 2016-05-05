<?php
/*
 *	BOOSTRAP/JS
 *	Extension for Automad
 *
 *	Copyright (c) 2016 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Extensions\Bootstrap;


/**
 *	The Bootstrap/JS extension adds a script tag to load the minified Bootstrap JS library. 
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2016 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class JS {
	
	
	/**
	 *	The script tag for the minified Bootstrap JS file.
	 */
	
	public function JS() {
		
		return '<script type="text/javascript" src="/extensions/bootstrap/dist/js/bootstrap.min.js"></script>';
		
	}
	
	
}

?>