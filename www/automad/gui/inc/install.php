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
 *	As part of the GUI, this file is only to be included via the GUI class.
 * 	The installer creates a file called "accounts.txt" to be installed in /config.
 */


if ($_POST) {
	
	if ($_POST['username'] && $_POST['password1'] && ($_POST['password1'] === $_POST['password2'])) {
		
		$accounts = array();
		$accounts[$_POST['username']] = $this->passwordHash($_POST['password1']);
		
		// Download accounts.php
		header('Expires: -1');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Disposition: attachment; filename=' . basename(AM_FILE_ACCOUNTS));
		ob_end_flush();
		echo $this->accountsGeneratePHP($accounts);
		die;
		
	} else {
		
		$error = $this->tb['error_form'];
	
	}
	
}


$this->guiTitle = $this->guiTitle . ' / ' . $this->tb['install_title'];
$this->element('header');


?>

		<div class="column content">
			<div class="inner">
				<div class="alert alert-info"><?php echo $this->tb['install_help']; ?></div>
				<?php if (isset($error)) { ?><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><?php echo $error; ?></div><?php } ?> 
				<form role="form" method="post">
					<div class="form-group">
						<label for="username"><?php echo $this->tb['sys_user_add_name']; ?></label>
						<input id="username" class="form-control" type="text" name="username" required />	
					</div>
					<div class="form-group">
						<label for="password1"><?php echo $this->tb['sys_user_add_password']; ?></label>
						<input id="password1" class="form-control" type="password" name="password1" required /> 
					</div>
					<div class="form-group">
						<label for="password2"><?php echo $this->tb['sys_user_add_repeat']; ?></label>
						<input id="password2" class="form-control" type="password" name="password2" required />
					</div>	
					<br />
					<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-save"></span> <?php echo $this->tb['btn_accounts_file']; ?></button>
				</form>
			</div>
		</div>

<?php


$this->element('footer');


?>