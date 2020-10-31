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
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
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

		<div class="uk-width-medium-1-2 uk-container-center">
			<div class="uk-animation-fade">
				<div class="uk-panel uk-panel-box">
					<div class="uk-panel-title">
						<i class="uk-icon-user-plus uk-icon-medium"></i>
					</div>
					<div class="am-text">
						<?php Text::e('install_help'); ?>
					</div>
				</div>
				<form class="uk-form uk-margin-small-top" method="post">
					<input 
					class="uk-form-controls uk-form-large uk-width-1-1" 
					type="text" 
					name="username" 
					placeholder="<?php Text::e('sys_user_add_name'); ?>" 
					/>
					<input 
					class="uk-form-controls uk-width-1-1 uk-margin-small-top" 
					type="password" 
					name="password1" 
					placeholder="<?php Text::e('sys_user_add_password'); ?>" 
					/>
					<input 
					class="uk-form-controls uk-width-1-1 uk-margin-small-bottom" 
					type="password" 
					name="password2" 
					placeholder="<?php Text::e('sys_user_add_repeat'); ?>" 
					/>
					<div class="uk-text-right">
						<button 
						type="submit" 
						class="uk-button uk-button-success" 
						data-uk-toggle="{target:'.uk-animation-fade'}"
						>
							<i class="uk-icon-download"></i>&nbsp;
							<?php Text::e('btn_accounts_file'); ?>
						</button>
					</div>
				</form>
			</div>
			<div class="uk-animation-fade uk-hidden">
				<div class="uk-panel uk-panel-box uk-margin-small-bottom">
					<div class="uk-panel-title">
						<i class="uk-icon-cloud-upload uk-icon-medium"></i>
					</div>
					<?php Text::e('install_login'); ?>
				</div>
				<div class="uk-text-right">
					<a href="" class="uk-button uk-button-success">
						<?php Text::e('btn_login'); ?>
					</a>
				</div>
			</div>
		</div>		
		<?php if (!empty($error)) { ?>
		<script type="text/javascript">
			Automad.notify.error("<?php echo $error; ?>");
			$('form input').first().focus();
		</script>	
		<?php } ?>

<?php


$this->element('footer');


?>