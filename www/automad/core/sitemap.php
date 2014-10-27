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
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Sitemap class handles the generating process for a site's sitemap.xml.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Sitemap {
	
	
	/**
	 *	The constructor verifies, whether sitemap.xml can be written and initiates the generating process.
	 *	
	 *	@param array $collection 
	 */
	
	public function __construct($collection) {
		
		// Skip sitemap for Proxies.
		if (!isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !isset($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
				
			$sitemap = AM_BASE_DIR . '/sitemap.xml';
		
			// If the base dir is writable without having a sitemap.xml or if sitemap.xml exists and is writable itself.
			if ((is_writable(AM_BASE_DIR) && !file_exists($sitemap)) || is_writable($sitemap)) {
				$this->generate($collection, $sitemap);
			} else {
				Debug::log('Sitemap: Permissions denied!');
			}
			
		} else {
			Debug::log('Sitemap: Skipped generating sitemap.xml! (Proxy)');
		}
		
	}
	
	
	/**
	 *	Generate the XML for the sitemap and write sitemap.xml.
	 *
	 *	@param array $collection
	 *	@param string $sitemap
	 */
	
	private function generate($collection, $sitemap) {
			
		$xml =  '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . 
			'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		
		foreach ($collection as $Page) {
			// Only include "real" URLs and not aliases.
			if (strpos($Page->url, '/') === 0) {
				$xml .= '<url><loc>http://' . $_SERVER['SERVER_NAME'] . AM_BASE_URL . AM_INDEX . $Page->url . '</loc></url>' . "\n";
			}
		}
		
		$xml .= '</urlset>';
		
		if (@file_put_contents($sitemap, $xml)) {
			Debug::log('Sitemap: Successfully generated "' . $sitemap . '"');
		}
		
	}
	
	
}

?>