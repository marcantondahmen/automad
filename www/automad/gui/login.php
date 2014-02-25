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
 *	The GUI Login Page. As part of the GUI, this file is only to be included via GUI::context().
 */


if ($_POST) {
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	$accounts = unserialize(file_get_contents(AM_FILE_ACCOUNTS));
	
	if (isset($accounts[$username]) && $this->passwordVerified($password, $accounts[$username])) {
		
		$_SESSION['username'] = $username;
		header('Location: http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
		die;
		
	} else {
		
		$error = 'Invalid username or password!';
		
	}
		
}


$this->guiTitle = $this->guiTitle . ' / Login';
$this->element('header');


?>

	<div class="row">
	
		<div class="col-md-4 col-md-offset-4">
		
			<div class="list-group">
				
				<?php $this->element('title'); ?>
				
				<div class="list-group-item list-group-item-info">
					<h4>Login</h4>			
				</div>
      
      			  	<?php if (isset($error)) { ?><div class="list-group-item list-group-item-danger"><?php echo $error; ?></div><?php } ?>
      
				<div class="list-group-item clearfix">
			      
					<form role="form" method="post">
					
						<div class="input-group">
							<span class="input-group-addon">Username</span>
							<input class="form-control" type="text" name="username" placeholder="Username" />
						</div>
						<div class="input-group">
							<span class="input-group-addon">Password</span>	
							<input class="form-control" type="password" name="password" placeholder="Password" />
						</div>		
						<br />
						<div class="pull-right">
							<button type="submit" class="btn btn-primary">Log In</button>
						</div>
						
					</form>
	
				</div>

			</div>

		</div>

	</div>	
					
<?php


$this->element('footer');


?>		