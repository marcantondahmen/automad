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
 *	Add an user.
 */


$output = array();


if (isset($_POST['username']) && $_POST['username'] && isset($_POST['password1']) && $_POST['password1'] && isset($_POST['password2']) && $_POST['password2']) {
	
	// Check if password1 equals password2.
	if ($_POST['password1'] == $_POST['password2']) {
		
		// Get all accounts from file.
		$accounts = unserialize(file_get_contents(AM_FILE_ACCOUNTS));
		
		// Check, if user exists already.
		if (!isset($accounts[$_POST['username']])) {
		
			// Add user to accounts array.
			$accounts[$_POST['username']] = $this->passwordHash($_POST['password1']);
			ksort($accounts);
			
			if (is_writable(AM_FILE_ACCOUNTS)) {
				
				// Write array with all accounts back to file.
				if (file_put_contents(AM_FILE_ACCOUNTS, serialize($accounts))) {
					$output['success'] = 'Successfully added <strong>' . $_POST['username'] . '</strong>';
				}
				
			} else {
				
				$output['error'] = 'Error saving new user!';
				
			}
			
		} else {
		
			$output['error'] = 'User <strong>' . $_POST['username'] . '</strong> already exists!';	
			
		}
		
	} else {
		
		$output['error'] = 'Please enter twice the same password!';
		
	}
	
} else {
	
	$output['error'] = 'All fields are required!';
	
}


echo json_encode($output);


?>