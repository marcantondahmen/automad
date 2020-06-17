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
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Request class.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Request {
	
	
	/**
	 *	Return the URL of the currently requested page.
	 *	
	 *	@return string The requested URL
	 */

	public static function page() {
		
		$request = '';

		if (!isset($_SERVER['QUERY_STRING'])) {
			$_SERVER['QUERY_STRING'] = '';
		}
		
		// Check if the query string starts with a '/'. 
		// That is the case if the requested page gets passed as part of the query string when rewriting is enabled (.htaccess or nginx.conf).
		if (strncmp($_SERVER['QUERY_STRING'], '/', 1) === 0) {
			
			// The requested page gets passed as part of the query string when pretty URLs are enabled and requests get rewritten like:
			// domain.com/page -> domain.com/index.php?/page 
			// Or with a query string: 
			// domain.com/page?key=value -> domain.com/index.php?/page&key=value
			$query = explode('&', $_SERVER['QUERY_STRING'], 2);
			$request = $query[0];
			Debug::log($query, 'Getting request from QUERY_STRING ' . $_SERVER['QUERY_STRING']);
			
			// In case there is no real query string except the requested page.
			if (!isset($query[1])) {
				$query[1] = '';
			}
			
			// Remove request from QUERY_STRING.
			$_SERVER['QUERY_STRING'] = $query[1];
			
			// Remove request from global arrays.
			unset($_GET[$request]);
			unset($_REQUEST[$request]);
			Debug::log($_GET, '$_GET');
			Debug::log($_REQUEST, '$_REQUEST');
			
		} else {
				
			// The requested page gets passed 'index.php/page/path'.
			// That can be the case if rewriting is disabled and AM_BASE_INDEX ends with '/index.php'.
			if (isset($_SERVER['PATH_INFO'])) {
		
				$request = $_SERVER['PATH_INFO'];
				Debug::log('Getting request from PATH_INFO');
	
			} else if (isset($_SERVER['ORIG_PATH_INFO'])) {	
	
				$request = $_SERVER['ORIG_PATH_INFO'];
				Debug::log('Getting request from ORIG_PATH_INFO');
	
			} else if (isset($_SERVER['REQUEST_URI'])) {
		
				$request = Str::stripEnd($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
				$request = Str::stripStart($request, AM_BASE_URL);
				Debug::log('Getting request from REQUEST_URI');
	
			} else if (isset($_SERVER['REDIRECT_URL'])) {
	
				$request = Str::stripStart($_SERVER['REDIRECT_URL'], AM_BASE_URL);
				Debug::log('Getting request from REDIRECT_URL');
			
			} else if (isset($_SERVER['PHP_SELF'])) {
	
				$request = Str::stripStart($_SERVER['PHP_SELF'], AM_BASE_URL);
				$request = Str::stripStart($request, '/index.php');
				Debug::log('Getting request from PHP_SELF');
				
			}
			
			$request = Str::stripStart($request, '/index.php');
			
		}
	
		// Remove trailing slash from URL to keep relative links consistent.
		if (substr($request, -1) == '/' && $request != '/') {
			header('Location: ' . AM_BASE_INDEX . rtrim($request, '/'), false, 301);
			die;
		}
		
		$request = '/' . trim($request, '/');
		
		Debug::log($request, 'Requested page');
		
		return $request; 
		
	}


	/**
	 *	Return a sanitized value of a query string parameter or any empty string, if that parameter doesn't exist.
	 *	Note: Since this method always returns a string, it should not be used to test whether a parameter exists in the query string, 
	 * 	because a non-existing parameter and an empty string as a parameter's value will return the same.
	 * 
	 *	@param string $key
	 *	@return string The value for the requested query key
	 */
	
	public static function query($key) {
	
		if (isset($_GET[$key])) {
			return htmlspecialchars($_GET[$key]);
		} else {
			return '';
		}
		
	}
	

	/**
	 *	Return value by key in the $_POST array or any empty string, if that key doesn't exist.
	 *	Note: Since this method always returns a string, it should not be used to test whether a key exists in $_POST. 
	 * 
	 *	@param string $key
	 *	@return string The value for the requested key
	 */

	public static function post($key) {

		if (isset($_POST[$key])) {
			return $_POST[$key];
		} else {
			return '';
		}

	}


}
