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


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	As part of the GUI, this file is only to be included via the GUI class.
 * 	The installer creates a file called "accounts.txt" to be installed in /config.
 */


$error = Accounts::install();


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('install_title');
$this->element('header');


?>

		<div class="column content">
			<div class="inner">
				<div class="alert alert-info"><?php echo Text::get('install_help'); ?></div>
				<?php if (!empty($error)) { ?><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><?php echo $error; ?></div><?php } ?> 
				<form role="form" method="post">
					<div class="form-group">
						<label for="username"><?php echo Text::get('sys_user_add_name'); ?></label>
						<input id="username" class="form-control" type="text" name="username" required />	
					</div>
					<div class="form-group">
						<label for="password1"><?php echo Text::get('sys_user_add_password'); ?></label>
						<input id="password1" class="form-control" type="password" name="password1" required /> 
					</div>
					<div class="form-group">
						<label for="password2"><?php echo Text::get('sys_user_add_repeat'); ?></label>
						<input id="password2" class="form-control" type="password" name="password2" required />
					</div>	
					<br />
					<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-save"></span> <?php echo Text::get('btn_accounts_file'); ?></button>
				</form>
			</div>
		</div>

<?php


$this->element('footer');


?>