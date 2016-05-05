<?php
/*
 *	BOOSTRAP/CSS
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
 *	The Bootstrap/CSS extension adds a link tag to load the minified Bootstrap CSS library. 
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2016 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class CSS {
	
	
	/**
	 *	Return the link tag for minified Bootstrap CSS.
	 *
	 *	@return The link tag
	 */
	
	public function CSS() {
		
		return '<link type="text/css" rel="stylesheet" href="/extensions/bootstrap/dist/css/bootstrap.min.css" />';
		
	}
	
	
}

?>