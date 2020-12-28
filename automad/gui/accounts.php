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
 *	Copyright (c) 2016-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core\Request as Request;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Accounts class provides all methods for creating and loading user accounts. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Accounts {
	
	
	/**
	 *	Add user account based on $_POST.
	 *
	 *	@return array $output (error/success)
	 */
	
	public static function add() {
		
		$output = array();
		$username = Request::post('username');
		$password1 = Request::post('password1');
		$password2 = Request::post('password2');

		if ($username && $password1 && $password2) {
	
			// Check if password1 equals password2.
			if ($password1 == $password2) {
		
				// Get all accounts from file.
				$accounts = Accounts::get();
		
				// Check, if user exists already.
				if (!isset($accounts[$username])) {
		
					// Add user to accounts array.
					$accounts[$username] = Accounts::passwordHash($password1);
					ksort($accounts);
				
					// Write array with all accounts back to file.
					if (Accounts::write($accounts)) {
				
						$output['success'] = Text::get('success_added') . ' "' . $username . '"';
				
					} else {
	
						$output['error'] = Text::get('error_permission') . '<p>' . AM_FILE_ACCOUNTS . '</p>';
				
					}
			
				} else {
		
					$output['error'] = '"' . $username . '" ' . Text::get('error_existing');	
			
				}
		
			} else {
		
				$output['error'] = Text::get('error_password_repeat');
		
			}
	
		} else {
	
			$output['error'] = Text::get('error_form');
	
		}
		
		return $output;
		
	}
	
	
	/**
	 *	Delete one ore more user accounts.
	 *
	 *	@param array $users
	 *	@return array $output (error/success)
	 */
	
	public static function delete($users) {
	
		$output = array();
	
		if (is_array($users)) {
			
			// Only delete users from list, if accounts.txt is writable.
			// It is important, to verify write access here, to make sure that all accounts stored in account.txt are also returned in the HTML.
			// Otherwise, they would be deleted from the array without actually being deleted from the file, in case accounts.txt is write protected.
			// So it is not enough to just check, if file_put_contents was successful, because that would be simply too late.
			if (is_writable(AM_FILE_ACCOUNTS)) {
				
				$accounts = Accounts::get();
				$deleted = array();
	
				foreach ($users as $userToDelete) {
		
					if (isset($accounts[$userToDelete])) {
			
						unset($accounts[$userToDelete]);
						$deleted[] = $userToDelete;
			
					}
		
				}

				// Write array with all accounts back to file.
				if (Accounts::write($accounts)) {
					$output['success'] = Text::get('success_remove') . ' "' . implode('", "', $deleted) . '"';
				}
		
			} else {
		
				$output['error'] = Text::get('error_permission') . '<p>' . AM_FILE_ACCOUNTS . '</p>';
		
			}
			
		}
	
		return $output;
		
	}
	
	
	/**
	 *	Install the first user account.
	 *
	 *	@return string Error message in case of an error.
	 */
	
	public static function install() {
		
		if (!empty($_POST)) {
	
			$username = Request::post('username');
			$password1 = Request::post('password1');
			$password2 = Request::post('password2');

			if ($username && $password1 && ($password1 === $password2)) {
		
				$accounts = array();
				$accounts[$username] = Accounts::passwordHash($password1);
		
				// Download accounts.php
				header('Expires: -1');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Type: application/octet-stream');
				header('Content-Transfer-Encoding: binary');
				header('Content-Disposition: attachment; filename=' . basename(AM_FILE_ACCOUNTS));
				ob_end_flush();
				echo Accounts::generatePHP($accounts);
				die;
		
			} else {
		
				return Text::get('error_form');
	
			}
	
		}
			
	}
	

	/**
	 *	Generate the PHP code for the accounts file. Basically the code returns the unserialized serialized array with all users.
	 *	That way, the accounts array can be stored as PHP.
	 *	The accounts file has to be a PHP file for security reasons. When trying to access the file directly via the browser, 
	 *	it gets executed instead of revealing any user names.
	 *	
	 *	@param array $accounts
	 *	@return string The PHP code
	 */
	
	public static function generatePHP($accounts) {
		
		return 	"<?php defined('AUTOMAD') or die('Direct access not permitted!');\n" .
			'return unserialize(\'' . serialize($accounts) . '\');' .
			"\n?>";
			
	} 
	
	
	/**
	 *	Get the accounts array by including the accounts PHP file.
	 *
	 *	@return array The registered accounts
	 */
	
	public static function get() {
		
		return (include AM_FILE_ACCOUNTS);
		
	}
	
	
	/**
	 *	Create hash from password to store in accounts.txt.
	 *
	 *	@param string $password
	 *	@return string Hashed/salted password
	 */

	public static function passwordHash($password) {
		
		$salt = '$2y$10$' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 22);
		
		return crypt($password, $salt);
		
	}


	/**
	 *	Verify if a password matches its hashed version.
	 *
	 *	@param string $password (clear text)
	 *	@param string $hash (hashed password)
	 *	@return boolean true/false 
	 */

	public static function passwordVerified($password, $hash) {
		
		return ($hash === crypt($password, $hash));
		
	}
	
	
	/**
	 *	Save the accounts array as PHP to AM_FILE_ACCOUNTS.
	 *
	 *	@return boolean Success (true/false)
	 */

	public static function write($accounts) {
		
		$success = FileSystem::write(AM_FILE_ACCOUNTS, Accounts::generatePHP($accounts));
		
		if ($success && function_exists('opcache_invalidate')) {
			opcache_invalidate(AM_FILE_ACCOUNTS, true);
		}

		return $success;

	}


}
