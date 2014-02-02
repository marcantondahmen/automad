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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	Class GUI. 
 */


class GUI {


	/**
	 *	Page title (used within elements/header.php).
	 */
	
	public $pageTitle = '';
	
	
	/**
	 *	Content for the modalDialog() method, which gets called in elements/footer.php. 
	 */
	
	public $modalMessage = '';


	/**
	 *	Load GUI element from automad/gui/elements.
	 *
	 *	@param string $element
	 */
	
	public function element($element) {
		
		require AM_BASE_DIR . '/automad/gui/elements/' . $element . '.php';
		
	}


	/**
	 *	Echo confirm window for form submissions. (Jquery UI Dialog)
	 *
	 *	@param string $id (#...)
	 *	@param string $message
	 */

	public function modalConfirm($id, $message) {
		
		echo 	'<script>' .
			'$("' . $id . '")' . 
			'.submit(function (e) {e.preventDefault(); $("<div>' . $message . '</div>")' .
			'.dialog({title: "Automad", width: 300, position: { my: "center", at: "center top+35%", of: window }, resizable: false, modal: true, buttons: {Yes: function () {e.target.submit();}, No: function () {$(this).dialog("close");}}});});' .
			'</script>';
		
	}

	
	/**
	 *	Echo dialog window with $this->modalMessage as content. (Jquery UI Dialog)
	 */
		
	public function modalDialog() {
		
		if ($this->modalMessage) {
			
			echo 	'<script>' .
				'$("<div>' . $this->modalMessage . '</div>")' .
				'.dialog({title: "Automad", width: 300, position: { my: "center", at: "center top+35%", of: window }, resizable: false, modal: true, buttons: {Ok: function() {$(this).dialog("close");}}});</script>';
			
		}
		
	}


	/**
	 *	Create hash from password to store in accounts.txt.
	 *
	 *	@param string $password
	 *	@return Hashed/salted password
	 */

	public function passwordHash($password) {
		
		$salt = '$2y$10$' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 22);
		
		return crypt($password, $salt);
		
	}


	/**
	 *	Verify if a password matches its hashed version.
	 *
	 *	@param string $password (clear text)
	 *	@param string $hash (hashed password)
	 *	@return true/false 
	 */

	public function passwordVerified($password, $hash) {
		
		return ($hash === crypt($password, $hash));
		
	}

	
	/**
	 *	Save the user accounts as serialized array to config/accounts.txt.
	 */
	
	public function saveAccounts($array) {
		
		return file_put_contents(AM_BASE_DIR . AM_FILE_ACCOUNTS, serialize($array));
		
	}
	

	/**
	 *	Return the Site's name.
	 *
	 *	@return Site's name
	 */
	
	public function siteName() {
		
		return $_SERVER['SERVER_NAME'];
		
	}


	/**
	 *	Return the currently logged in user.
	 * 
	 *	@return username
	 */

	public function user() {
		
		if (isset($_SESSION['username'])) {
			return $_SESSION['username'];
		}
		
	}
	
	
}


?>