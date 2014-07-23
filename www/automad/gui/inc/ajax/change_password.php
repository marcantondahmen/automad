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


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	Change password of currently logged in user.
 */


$output = array();


if (isset($_POST['current-password']) && $_POST['current-password'] && isset($_POST['new-password1']) && $_POST['new-password1'] && isset($_POST['new-password2']) && $_POST['new-password2']) {
	
	if ($_POST['new-password1'] == $_POST['new-password2']) {
		
		if ($_POST['current-password'] != $_POST['new-password1']) {
			
			// Get all accounts from file.
			$accounts = $this->accountsGetArray();
			
			if ($this->passwordVerified($_POST['current-password'], $accounts[$this->user()])) {
				
				// Change entry for current user with accounts array.
				$accounts[$this->user()] = $this->passwordHash($_POST['new-password1']);
					
				// Write array with all accounts back to file.
				if ($this->accountsSaveArray($accounts)) {
					
					$output['success'] = $this->tb['success_password_changed']; 
					
				} else {
					
					$output['error'] = $this->tb['error_permission'] . '<p>' . AM_FILE_ACCOUNTS . '</p>';
				
				}
				
			} else {
				
				$output['error'] = $this->tb['error_form'];
				
			}
						
		} else {
			
			$output['error'] = $this->tb['error_form'];;
			
		}
		
	} else {
		
		$output['error'] = $this->tb['error_form'];
		
	}
	
} else {
	
	$output['error'] = $this->tb['error_form'];
	
}


echo json_encode($output);


?>