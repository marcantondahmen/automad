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
 *	The User class provides all methods related to a user account. 
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2016 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class User {
	
	
	/**
	 *	Return the currently logged in user.
	 * 
	 *	@return username
	 */

	public static function get() {
		
		if (isset($_SESSION['username'])) {
			return $_SESSION['username'];
		}
		
	}
	
	
	/**
	 *	Verify login information.
	 *
	 *	@return Error message in case of an error.
	 */
	
	public static function login() {
		
		if (!empty($_POST)) {
			
			if (!empty($_POST['username']) && !empty($_POST['password'])) {
	
				$username = $_POST['username'];
				$password = $_POST['password'];
				$accounts = Accounts::get();
	
				if (isset($accounts[$username]) && Accounts::passwordVerified($password, $accounts[$username])) {
		
					session_regenerate_id(true);
					$_SESSION['username'] = $username;
					header('Location: ' . $_SERVER['REQUEST_URI']);
					die;
		
				} else {
		
					return Text::get('error_login');
		
				}
		
			} else {
		
				return Text::get('error_login');
		
			}
			
		}
		
	}
	
	
	/**
	 *	Log out user.
	 *
	 *	@return true on success.
	 */
	
	public static function logout() {
		
		unset($_SESSION);
		$success = session_destroy();
		
		if (!isset($_SESSION) && $success) {
			return true;
		} else {
			return false;
		}
		
	}
	
	
}


?>