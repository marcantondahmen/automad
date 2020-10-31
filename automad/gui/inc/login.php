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
 *	The GUI Login Page. As part of the GUI, this file is only to be included via the GUI class.
 */

$error = User::login();

$this->guiTitle = $this->guiTitle . ' / ' . Text::get('login_title');
$this->element('header');


?>
		
		<div class="uk-width-medium-1-2 uk-container-center">
			<h1><?php echo $this->getShared()->get(AM_KEY_SITENAME); ?></h1>
			<form class="uk-form uk-margin-top" method="post">
				<input 
				class="uk-form-controls uk-width-1-1" 
				type="text" 
				name="username" 
				placeholder="<?php Text::e('login_username'); ?>" 
				required 
				/>
				<input 
				class="uk-form-controls uk-width-1-1 uk-margin-small-bottom" 
				type="password" 
				name="password" 
				placeholder="<?php Text::e('login_password'); ?>" 
				required 
				/>
				<div class="uk-text-right">
					<a href="<?php echo AM_BASE_INDEX . '/'; ?>" class="uk-button uk-button-link">
						<?php Text::e('btn_home'); ?>
					</a>
					<button type="submit" class="uk-button uk-button-success">
						<?php Text::e('btn_login'); ?>
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