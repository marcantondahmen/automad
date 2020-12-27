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
 *	Copyright (c) 2013-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Resolve class holds all methods to modulate URLs and file paths.
 *	Since all page URLs are not URLs to real directories, all non-absolute links and file paths have to be resolved, 
 *	to point to a valid location. 
 *	For example a relative file name to an image wouldn't be a valid link, since the URL of the page is not
 *	the real file system path to the page's files.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2013-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
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
	 *	@return string The resolved file path 
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
	 *	Resolve relative URLs (starting with a character or .) to be absolute URLs, 
	 *	using the base directory (where Automad is installed) as root.
	 *
	 *	Example:    
	 *	image.png -> /pages/path/image.png or
	 *	subpage   -> /parent/subpage or 
	 *	../       -> /parent
	 *
	 *	@param string $url
	 *	@param object $Page
	 *	@return string The resolved URL
	 */

	public static function relativeUrlToBase($url, $Page) {
		
		// Skip any protocol, mailto, tel and skype links.
		if (preg_match('/(\:\/\/|^[a-z]+\:)/is', $url)) {
			
			return $url;
			
		}

		// Check if $url is relative.
		if (preg_match('/^[\w\.]/', $url)) {
			
			if (FileSystem::isAllowedFileType($url)) {
				
				$url = AM_DIR_PAGES . $Page->path . $url;
				
			} else {
				
				// Even though all trailing slashes get stripped out of beauty reasons, any page must still be understood as a directory instead of a file.
				// Therefore it should be possible to link to a subpage with just href="subpage". Due to the missing trailing slash, that link would actually link to
				// a page called subpage, but being a sibling of the current page instead of really being a child.
				// Exampe: 
				// The current page is "http://domain.com/page" and has a link href="subpage". 
				// Just returning that link would reslove to "http://domain.com/subpage", which is wrong. It should be "http://domain.com/page/subpage".
				// Therefore resolving that URL is also necessary.		
				$url = rtrim($Page->origUrl, '/') . '/' . $url;
				
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
			return '/' . trim($url, '/');
			
		} 
			
		return $url;
		
	}


	/**
	 *	Resolve absolute URLs (starting with a slash) to root in case Automad is installed within a subdirectory.
	 *
	 *	Example:    
	 *	/page -> /base-url/index.php/page or
	 *	/page -> /base-url/page
	 *
	 *	@param string $url
	 *	@return string The resolved URL
	 */

	public static function absoluteUrlToRoot($url) {
		
		// Skip URLs starting with "//".
		if (strpos($url, '//') === 0) {
			
			return $url;
			
		}
		
		// All URLs starting with only one slash.
		if (strpos($url, '/') === 0) {
			
			// Relative to root	
			if (FileSystem::isAllowedFileType($url) || $url == '/' || strpos($url, '/?') === 0 || strpos($url, '/#') === 0) {
				// Skip adding a possible '/index.php' when linking to files and to the homepage (possibly including a query string or anchor link), 
				// also if rewriting is disabled.
				return AM_BASE_URL . $url;
			} else {	
				return AM_BASE_INDEX . $url;	
			}
									
		} 
		
		return $url;
		
	}

	
}
