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
 *	The GUI Log Out Page. As part of the GUI, this file is only to be included via the GUI class.
 */


$loggedOut = User::logout();


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('log_out_title');
$this->element('header');


?>

		<div class="uk-width-medium-1-2 uk-container-center uk-margin-top">
			<div class="uk-panel uk-panel-box uk-margin-bottom">
				<div class="uk-panel-title">
					<i class="uk-icon-sign-out uk-icon-medium"></i>
				</div>
				<h3><?php echo $this->sitename ?></h3>
			</div>
			<?php if ($loggedOut) { ?>
			<div class="uk-alert uk-alert-success uk-margin-bottom">
				<?php echo Text::get('success_log_out'); ?>
			</div>
			<div class="uk-text-right">
				<a href="<?php echo AM_BASE_URL . AM_INDEX . AM_PAGE_GUI; ?>" class="uk-button">
					<i class="uk-icon-sign-in"></i>&nbsp;&nbsp;<?php echo Text::get('btn_login'); ?>
				</a>
			</div>
			<?php } else { ?>
			<div class="uk-alert uk-alert-danger">
				<?php echo Text::get('error_log_out'); ?>
			</div>
			<?php } ?>
		</div>
		
<?php


$this->element('footer');


?>