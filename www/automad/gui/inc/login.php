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
 *	The GUI Login Page. As part of the GUI, this file is only to be included via the GUI class.
 */


if ($_POST) {
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	$accounts = $this->accountsGetArray();
	
	if (isset($accounts[$username]) && $this->passwordVerified($password, $accounts[$username])) {
		
		session_regenerate_id(true);
		$_SESSION['username'] = $username;
		header('Location: ' . $_SERVER['REQUEST_URI']);
		die;
		
	} else {
		
		$error = $this->tb['error_login'];
		
	}
		
}


$this->guiTitle = $this->guiTitle . ' / ' . $this->tb['login_title'];
$this->element('header');


?>

		<div class="column content">
			<div class="inner">
				<?php if (isset($error)) { ?><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><?php echo $error; ?></div><?php } ?> 
	      			<form role="form" method="post">
					<div class="form-group">
						<label for="username">Username</label>
						<input id="username" class="form-control" type="text" name="username" />
					</div>
					<div class="form-group">
						<label for="password">Password</label>	
						<input id="password" class="form-control" type="password" name="password" />
					</div>
					<br />	
					<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-arrow-right"></span> <?php echo $this->tb['btn_login']; ?></button>
				</form>
			</div>
		</div>	
					
<?php


$this->element('footer');


?>		