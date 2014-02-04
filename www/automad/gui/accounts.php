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

/**
 *	Manage user accounts and change password.
 */


define('AUTOMAD', true);
require 'elements/base.php';


$accounts = unserialize(file_get_contents(AM_BASE_DIR . AM_FILE_ACCOUNTS));


// Change password of logged in user
if (isset($_POST['changepassword'])) {
	
	$user = $G->user();
	$current = $_POST['changepassword']['currentpassword'];
	$new1 = $_POST['changepassword']['newpassword1'];
	$new2 = $_POST['changepassword']['newpassword2'];
	
	if ($G->passwordVerified($current, $accounts[$user])) {
		
		if ($new1 === $new2) {
			
			$accounts[$user] = $G->passwordHash($new1);
			
			if ($G->saveAccounts($accounts)) {
				$G->modalDialogContent = 'Successfully changed your password!';
			} else {
				$G->modalDialogContent = 'Error while saving account data...';
			}
			
		} else {
			
			$G->modalDialogContent = 'Please enter twice the same value for the new password!';
			
		}
		
	} else {
		
		$G->modalDialogContent = 'Invalid password!';
		
	}
	
}


// Add new user
if (isset($_POST['new'])) {
	
	$new = $_POST['new'];
	
	// Only if account doesn't exsist yet...
	if (!isset($accounts[$new['username']])) {

		// If the submitted data is complete...
		if ($new['username'] && $new['password1'] && ($new['password1'] === $new['password2'])) {
		
			$accounts[$new['username']] = $G->passwordHash($new['password1']);
			
			ksort($accounts);
			
			if ($G->saveAccounts($accounts)) {
				$G->modalDialogContent = 'Successfully added <b>\"' . $new['username'] . '\"</b>!';
			} else {
				$G->modalDialogContent = 'Error while saving account data!';
			}
		
		} else {
		
			$G->modalDialogContent = 'Please fill in all required fields to add a new user!';
		
		}
	
	} else {
		
		$G->modalDialogContent = 'User <b>\"' . $new['username'] . '\"</b> already exists!';
		
	}
	
}


// Delete users
if (isset($_POST['delete'])) {
	
	foreach ($_POST['delete'] as $user) {
		
		unset($accounts[$user]);
		
	}
	
	if ($G->saveAccounts($accounts)) {
		$G->modalDialogContent = 'Successfully deleted:<br /><b>\"' . implode('\", \"', $_POST['delete']) . '\"</b>';
	} else {
		$G->modalDialogContent = 'Error while saving account data!';
	}
	
}


$G->guiTitle = 'User Accounts';
$G->element('header-1200');


?>

<div class="box">
	<h2 class="section">Change Your Password</h2>
	<form class="section" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="password" name="changepassword[currentpassword]" placeholder="Current Password" />	
		<input type="password" name="changepassword[newpassword1]" placeholder="New Password" />
		<input type="password" name="changepassword[newpassword2]" placeholder="Repeat New Password" />
		<input type="submit" value="Change Password" />
	</form>
</div>

<div class="box">
	<h2 class="section">Add User</h2>
	<form class="section" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="text" name="new[username]" placeholder="Username" />	
		<input type="password" name="new[password1]" placeholder="Password" />
		<input type="password" name="new[password2]" placeholder="Repeat Password" />
		<input type="submit" value="Add New User" />
	</form>
</div>

<?php
if (count($accounts) > 1) {
?>
<div class="box">
	<h2 class="section">Delete Users</h2>
	<form class="section" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="delete">
		<?php	
		foreach (array_keys($accounts) as $user) {
			if ($user != $G->user()) {
				echo '<div class="checkbox"><input type="checkbox" name="delete[]" value="' . $user . '"> ' . ucwords($user) . '</div>';
			}
		}
		?>
		<input type="reset" value="Clear">
		<input type="submit" value="Delete Selected" />
	</form>
	<?php $G->modalConfirm('#delete', 'Do you really want to delete the selected users?'); ?> 
</div>

<?php
}


$G->element('footer');


?>