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
 *	The Automad GUI install page.
 *
 *	Before the users file got added to the config directory, this page gets loaded, to create a users.txt file. 
 *	This file needs to be downloaded and moved to the config folder manually, to make sure, that the installation process can only be finished by the site's owner with ftp/ssh access.
 */


define('AUTOMAD', true);
require 'elements/base.php';


if ($_POST) {
	
	if ($_POST['username'] && $_POST['password1'] && ($_POST['password1'] === $_POST['password2'])) {
		
		$accounts = array();
		$accounts[$_POST['username']] = $G->passwordHash($_POST['password1']);
				
		// Download users.txt
		header('Expires: -1');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Disposition: attachment; filename=' . basename(AM_FILE_ACCOUNTS));
		ob_end_flush();
		echo serialize($accounts);
		die;
		
	} else {
		
		$G->modalDialogContent = 'Make sure to specify a username and twice the same password!';
	
	}
	
}


$G->guiTitle = 'Installation';
$G->element('header_400');


?>

<div class="box">
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input class="item bg input" type="text" name="username" placeholder="Username" />	
		<input class="item bg input" type="password" name="password1" placeholder="Password" /> 
		<input class="item bg input" type="password" name="password2" placeholder="Repeat Password" />
		<input class="item bg button" type="submit" value="Create Accounts File" />
	</form>
	
</div>

<?php


$G->element('footer');


?>