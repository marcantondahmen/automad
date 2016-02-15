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


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The GUI class provides all methods used for the GUI. 
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class GUI {
	
	
	/**
	 *	The Content object.
	 */
	
	private $Content;
	
	
	/**
	 *	The Html object.
	 */
	
	private $Html;
	
	
	/**
	 *	The Keys object.
	 */
	
	private $Keys;
	
	
	/**
	 *	Automad's collection.
	 */	
		
	private $collection;
	
	
	/**
	 *	The GUI page's title.
	 */
	
	private $guiTitle = 'Automad';
	
	
	/**
	 *	The GUI's buffered HTML.
	 */
	
	public $output;
		
		
	/**
	 *	The Site's data and settings.
	 */
	
	private $siteData;
	

	/**
	 *	Load the site's global data and include modules according to the current context.
	 *
	 *	When a the GUI gets initialize, at first it gets verified, if an user is logged in or not. That has the highest priority.
	 *	If no user is logged in, the existance of "config/accounts.txt" gets checked and either the login or the install module gets loaded.
	 *
	 *	If an user is logged in, the Automad object gets created and the current "context" gets determined from the GET parameters.
	 *	According that context, the matching module gets loaded after verifying its exsistance. 
	 *	When there is no context passed via get, it gets checked for "ajax", to possibly call a matching ajax module.
	 *	If both is negative, the start page's module gets included.
	 *
	 *	Example Context: 	http://domain.com/gui?context=edit_page will include 	automad/gui/context/edit_page.php
	 *	Example Ajax:		http://domain.com/gui?ajax=page_data will include 	automad/gui/ajax/page_data.php
	 *
	 *	Since every request for the gui (pages and ajax) gets still routed over "/index.php" > "/automad/init.php" > new GUI(), 
	 *	all the session/login checking needs only to be done here once, simply because all modules get includede here.   
	 */
	
	public function __construct() {
		
		// Get Site data.
		$this->siteData = Core\Parse::siteData();
		
		// Load text modules.
		Text::parseModules();
		
		// Start Session.
		session_start();
		
		// Check if an user is logged in.
		if ($this->user()) {
	
			// If user is logged in, continue with getting the Automad object and the collection.
			$Automad = new Core\Automad();
			$this->collection = $Automad->getCollection();
			
			// Create objects.
			$this->Content = new Content($Automad);
			$this->Html = new Html($Automad);
			$this->Keys = new Keys($Automad);
					
			// Check if context/ajax matches an existing .php file.
			// If there is no (or no matching context), load the start page.
			if (in_array(AM_BASE_DIR . AM_DIR_GUI_INC . '/context/' . Core\Parse::queryKey('context') . '.php', glob(AM_BASE_DIR . AM_DIR_GUI_INC . '/context/*.php'))) {		
				$inc = 'context/' . Core\Parse::queryKey('context');
			} else if (in_array(AM_BASE_DIR . AM_DIR_GUI_INC . '/ajax/' . Core\Parse::queryKey('ajax') . '.php', glob(AM_BASE_DIR . AM_DIR_GUI_INC . '/ajax/*.php'))) {		
				$inc = 'ajax/' . Core\Parse::queryKey('ajax');
			} else {
				$inc = 'start';
			}
	
		} else {
	
			// If no user is logged in, check if accounts.txt exists. If yes, set $inc to the login page, else to the installer.
			if (file_exists(AM_FILE_ACCOUNTS)) {
				$inc = 'login';
			} else {
				$inc = 'install';
			}

		}
		
		// Buffer the HTML to merge the output with the debug log in init.php.
		ob_start();
		
		// Load page according to the current context.
		require AM_BASE_DIR . AM_DIR_GUI_INC . '/' . $inc . '.php';	
		
		$this->output = ob_get_contents();
		ob_end_clean();
		
	}
	
	
	/**
	 *	Generate the PHP code for the accounts file. Basically the code returns the unserialized serialized array with all users.
	 *	That way, the accounts array can be stored as PHP.
	 *	The accounts file has to be a PHP file for security reasons. When trying to access the file directly via the browser, 
	 *	it gets executed instead of revealing any user names.
	 *	
	 *	@param array $accounts
	 *	@return The PHP code as string
	 */
	
	private function accountsGeneratePHP($accounts) {
		
		return 	"<?php defined('AUTOMAD') or die('Direct access not permitted!');\n" .
			'return unserialize(\'' . serialize($accounts) . '\');' .
			"\n?>";
			
	} 
	
	
	/**
	 *	Get the accounts array by including the accounts PHP file.
	 *
	 *	@return The accounts array
	 */
	
	private function accountsGetArray() {
		
		return (include AM_FILE_ACCOUNTS);
		
	}
	
	
	/**
	 *	Save the accounts array as PHP to AM_FILE_ACCOUNTS.
	 *
	 *	@return Success (true/false)
	 */

	private function accountsSaveArray($accounts) {
		
		return @file_put_contents(AM_FILE_ACCOUNTS, $this->accountsGeneratePHP($accounts));
		
	}
	
	
	/**
	 *	Load GUI element from automad/gui/elements.
	 *
	 *	@param string $element
	 */
	
	private function element($element) {
		
		require AM_BASE_DIR . AM_DIR_GUI_INC . '/elements/' . $element . '.php';
		
	}


	/**
	 *	Create hash from password to store in accounts.txt.
	 *
	 *	@param string $password
	 *	@return Hashed/salted password
	 */

	private function passwordHash($password) {
		
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

	private function passwordVerified($password, $hash) {
		
		return ($hash === crypt($password, $hash));
		
	}

			
	/**
	 *	Return the Site's name.
	 *
	 *	@return Site's name
	 */
	
	private function siteName() {
		
		return $this->siteData[AM_KEY_SITENAME];
		
	}


	/**
	 *	Return the currently logged in user.
	 * 
	 *	@return username
	 */

	private function user() {
		
		if (isset($_SESSION['username'])) {
			return $_SESSION['username'];
		}
		
	}
	

}


?>