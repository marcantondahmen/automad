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
 *	Change password of currently logged in user.
 */


$output = array();


if (isset($_POST['current-password']) && $_POST['current-password'] && isset($_POST['new-password1']) && $_POST['new-password1'] && isset($_POST['new-password2']) && $_POST['new-password2']) {
	
	if ($_POST['new-password1'] == $_POST['new-password2']) {
		
		if ($_POST['current-password'] != $_POST['new-password1']) {
			
			// Get all accounts from file.
			$accounts = unserialize(file_get_contents(AM_FILE_ACCOUNTS));
			
			if ($this->passwordVerified($_POST['current-password'], $accounts[$this->user()])) {
				
				// Change entry for current user with accounts array.
				$accounts[$this->user()] = $this->passwordHash($_POST['new-password1']);

				if (is_writable(AM_FILE_ACCOUNTS)) {
					
					// Write array with all accounts back to file.
					if (file_put_contents(AM_FILE_ACCOUNTS, serialize($accounts))) {
						$output['success'] = 'Your password got changed successfully!<br /><br /><a class="alert-link" href="?context=logout"><strong>Log out</strong></a>';
					}
					
				} else {
					
					$output['error'] = 'Error while saving your password!';
					
				}
				
			} else {
				
				$output['error'] = 'Please enter your current password!';
				
			}
						
		} else {
			
			$output['error'] = 'You can not use your current password!';
			
		}
		
	} else {
		
		$output['error'] = 'Please enter twice the same new password!';
		
	}
	
} else {
	
	$output['error'] = 'All fields are required!';
	
}


echo json_encode($output);


?>