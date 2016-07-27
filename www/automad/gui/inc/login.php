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
 *	Copyright (c) 2014-2016 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	The GUI Login Page. As part of the GUI, this file is only to be included via the GUI class.
 */

$error = User::login();

$this->guiTitle = $this->guiTitle . ' / ' . Text::get('login_title');
$this->element('header');


?>
		
		<div class="uk-width-medium-1-2 uk-container-center uk-margin-top">
			<div class="uk-panel uk-panel-box">
				<div class="uk-panel-title">
					<i class="uk-icon-sign-in uk-icon-medium"></i>
				</div>
				<h3><?php echo $this->sitename ?></h3>
			</div>
			<form class="uk-form uk-margin-top" method="post">
				<input class="uk-form-controls uk-form-large uk-width-1-1 uk-margin-small-bottom" type="text" name="username" placeholder="<?php echo Text::get('login_username'); ?>" required />
				<input class="uk-form-controls uk-width-1-1 uk-margin-bottom" type="password" name="password" placeholder="<?php echo Text::get('login_password'); ?>" required />
				<div class="uk-text-right">
					<button type="submit" class="uk-button uk-button-primary">
						<i class="uk-icon-sign-in"></i>&nbsp;&nbsp;<?php echo Text::get('btn_login'); ?>
					</button>
				</div>
			</form>
		</div>
		<?php if (!empty($error)) { ?>
		<script type="text/javascript">
			Automad.notify.error('<?php echo $error; ?>');
		</script>	
		<?php } ?>
			
<?php


$this->element('footer');


?>		