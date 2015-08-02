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
 *	The Resolve class holds all methods to modulate URLs and file paths.
 *	Since all page URLs are not URLs to real directories, all non-absolute links and file paths have to be modulated, 
 *	to point to a valid location. 
 *	For example a relative file name to an image wouldn't be a valid link, since the URL of the page is not
 *	the real file system path to the page's files.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Resolve {
	

	/**
	 *	Resolve a file path or glob pattern according to its type (root relative or relative).
	 *
	 *	If a file path begins with a '/', it is treated like a root relative path and the only AM_BASE_DIR gets prepended.
	 *	In all other cases, the full path to the page gets prepended.
	 *	For example a file called 'image.jpg' becomes '/basedir/pages/pagedir/image.jpg' and 
	 *	'/shared/image.jpg' becomes '/basedir/shared/image.jpg'.
	 *
	 *	@param string $pagePath
	 *	@param string $filePath
	 *	@return The modulated file path 
	 */

	public static function filePath($pagePath, $filePath) {
		
		if (strpos($filePath, '/') === 0) {
			
			// Relative to root
			return AM_BASE_DIR . $filePath;
			
		} else {
			
			// Relative to page
			return AM_BASE_DIR . AM_DIR_PAGES . $pagePath . $filePath;
			
		}
		
	}


	/**
	 *	Resolve an URL according to its type.
	 * 
	 *	Absolute URLs, query strings, anchors or mails:	not modified
	 *	Root-relative URLs: 				AM_BASE_URL is prepended (and AM_INDEX in case of pages)
	 *	Relative URLs:					the full path gets prepended and all '../' and './' get resolved
	 *	
	 *	@param object $Page
	 *	@param string $url
	 *	@return The modulated URL
	 */

	public static function url($Page, $url) {
		
		if (strpos($url, '://') !== false || strpos($url, '//') === 0 || strpos($url, '?') === 0 || strpos($url, '#') === 0 || strpos($url, 'mailto:') === 0 || strpos($url, '&#') === 0) {
									
			// Absolute URL (contains '://' or starts with '//'), query string ('?'), anchor link ('#') or mailto link ('mailto:' and obfuscated '&#...').
			return $url;
			
		} else if (strpos($url, '/') === 0) {
			
			// Relative to root	
			if (Parse::isFileName($url) || $url == '/' || strpos($url, '/?') === 0 || strpos($url, '/#') === 0) {
				// Skip adding a possible '/index.php' when linking to files and to the homepage (possibly including a query string or anchor link), 
				// also if rewriting is disabled.
				return AM_BASE_URL . $url;
			} else {	
				return AM_BASE_URL . AM_INDEX . $url;	
			}
									
		} else {
			
			// Relative URL
			if (Parse::isFileName($url)) {
				$url = $Page->path . $url;
			} else {
				// Even though all trailing slashes get stripped out of beauty reasons, any page must still be understood as a directory instead of a file.
				// Therefore it should be possible to link to a subpage with just href="subpage". Due to the missing trailing slash, that link would actually link to
				// a page called subpage, but being a sibling of the current page instead of really being a child.
				// Exampe: 
				// The current page is "http://domain.com/page" and has a link href="subpage". 
				// Just returning that link would reslove to "http://domain.com/subpage", which is wrong. It should be "http://domain.com/page/subpage".
				// Therefore resolving that URL is also necessary.
				$url = rtrim($Page->url, '/') . '/' . $url;
			}
			
			// Resolve '../' and './'
			$parts = explode('/', $url);
			$resolvedParts = array();
		
			foreach ($parts as $part) {
				if ($part == '..') {
					array_pop($resolvedParts);
				} else {
					if ($part != '.') {
						$resolvedParts[] = $part;
					}
				}
			}
			
			$url = implode('/', $resolvedParts);
	
			// Remove slashes preceding query string or anchor links.
			$url = str_replace(array('/?', '/#'), array('?', '#'), $url);
	
			// Trim trailing slashes, but always keep a leading one.
			$url = '/' . trim($url, '/');
	
			// Prepend base.
			if (Parse::isFileName($url)) {
				return AM_BASE_URL . AM_DIR_PAGES . $url;
			} else {
				return AM_BASE_URL . AM_INDEX . $url;
			}
				
		}
		
	}

	
}


?>