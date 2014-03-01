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
 *	As part of the GUI, this file is only to be included via the GUI class.
 * 	The installer creates a file called "accounts.txt" to be installed in /config.
 */


if ($_POST) {
	
	if ($_POST['username'] && $_POST['password1'] && ($_POST['password1'] === $_POST['password2'])) {
		
		$accounts = array();
		$accounts[$_POST['username']] = $this->passwordHash($_POST['password1']);
				
		// Download accounts.txt
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
		
		$error = 'Make sure to specify a <b>username</b> and twice the same <b>password</b>!';
	
	}
	
}


$this->guiTitle = $this->guiTitle . ' / Install';
$this->element('header');


?>

	<div class="row">
		
		<div class="col-md-4 col-md-offset-4">
			
			<?php $this->element('title'); ?>
			
			<?php if (isset($error)) { ?><div class="alert alert-danger"><?php echo $error; ?></div><?php } ?>

			<div class="list-group">
				
				<div class="list-group-item">
					<h4>Installation</h4>
				</div>
	      
				<div class="list-group-item clearfix">
				      
					<form role="form" method="post">
			
						<div class="input-group">
							<span class="input-group-addon">Username</span>
							<input class="form-control" type="text" name="username" placeholder="Username" />	
						</div>
						<div class="input-group">
							<span class="input-group-addon">Password</span>
							<input class="form-control" type="password" name="password1" placeholder="Password" /> 
						</div>
						<div class="input-group">
							<span class="input-group-addon">Password</span>
							<input class="form-control" type="password" name="password2" placeholder="Repeat Password" />
						</div>
						<br />
						<div class="pull-right">
							<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-save"></span> Download Accounts File</button>
						</div>
						
					</form>
		
				</div>
	
			</div>
	
		</div>

	</div>

<?php


$this->element('footer');


?>