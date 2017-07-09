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
 *	Copyright (c) 2014-2017 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	The GUI Sytem Settings' Page. As part of the GUI, this file is only to be included via the GUI class.
 */


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('sys_title');
$this->element('header');


?>
	
		<ul class="uk-subnav uk-subnav-pill uk-margin-large-top uk-margin-bottom">
			<li class="uk-disabled"><i class="uk-icon-cog"></i></li>
			<li><a href=""><?php Text::e('sys_title'); ?></a></li>
		</ul>
		<?php
		 
			echo $this->Html->stickySwitcher('#am-sys-content', array(
				array(
					'icon' => '<i class="uk-icon-rocket"></i>',
					'text' => Text::get('sys_cache')
				),
				array(
					'icon' => '<i class="uk-icon-user"></i>',
					'text' => Text::get('sys_user')
				),
				array(
					'icon' => '<i class="uk-icon-refresh"></i>',
					'text' => Text::get('sys_update')
				),
				array(
					'icon' => '<i class="uk-icon-bug"></i>',
					'text' => Text::get('sys_debug')
				)
				
			)); 
			
		?> 

		<ul id="am-sys-content" class="uk-switcher">
			<!-- Cache -->
			<li>
				<div class="uk-block">
					<?php Text::e('sys_cache_info'); ?>
				</div>
				<!-- Cache Enable / Settings -->
				<form class="uk-form uk-form-stacked" data-am-handler="update_config" data-am-auto-submit>
					<!-- Cache Enable -->
					<input type="hidden" name="type" value="cache" />		
					<label class="uk-button uk-button-large" data-am-toggle="#am-cache-settings, #am-cache-actions">
						<?php Text::e('sys_cache_enable'); ?>
						<input type="checkbox" name="cache[enabled]" value="on"<?php if (AM_CACHE_ENABLED) { echo ' checked'; } ?> />
					</label>
					<!-- Cache Settings -->
					<ul id="am-cache-settings" class="am-toggle-container uk-grid uk-grid-width-1-1 uk-grid-width-small-1-2">
						<!-- Cache Monitor Delay -->
						<li>
							<label class="uk-form-label uk-margin-large-top"><?php Text::e('sys_cache_monitor'); ?></label>		
							<?php echo 	$this->Html->radios(
										'cache[monitor-delay]',
										array(
											'1 min' => 60,
											'2 min' => 120,
											'5 min' => 300
										),
										AM_CACHE_MONITOR_DELAY
									); 
							?> 
						</li>
						<!-- Cache Lifetime -->
						<li>
							<label class="uk-form-label uk-margin-large-top"><?php Text::e('sys_cache_lifetime'); ?></label>		
							<?php echo 	$this->Html->radios(
										'cache[lifetime]',
										array(
											'1 h' => 3600,
											'6 h' => 21600,
											'12 h' => 43200
										),
										AM_CACHE_LIFETIME
									);
							?> 
						</li>
					</ul>	
				</form>
				<div id="am-cache-actions" class="am-toggle-container">
					<hr />
					<!-- Clear Cache -->	
					<?php Text::e('sys_cache_clear_info'); ?>
					<form data-am-handler="clear_cache">
						<button type="submit" class="uk-button uk-button-large">
							<i class="uk-icon-refresh"></i>&nbsp;&nbsp;<?php Text::e('sys_cache_clear'); ?>
						</button>
					</form>	
					<?php if ($tmp = FileSystem::getTmpDir()) { ?>
					<hr />
					<!-- Purge Cache -->
					<?php Text::e('sys_cache_purge_info'); ?>
					<form data-am-handler="purge_cache">
						<button type="submit" class="uk-button uk-button-danger uk-button-large">
							<span class="uk-badge"><?php echo $tmp; ?></span>
							&nbsp;<?php Text::e('sys_cache_purge'); ?>
						</button>
					</form>
					<?php } ?>
				</div>
			</li>
			<!-- User -->
			<li>
				<div class="uk-block">
					<?php Text::e('sys_user_info'); ?>
				</div>
				<!-- Registered Users -->
				<a href="#am-users-modal" class="uk-button uk-button-large uk-button-primary" data-uk-modal data-am-status="users"></a>
				<div id="am-users-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						<div class="uk-modal-header">
							<?php Text::e('sys_user_registered'); ?>
							<a href="#" class="uk-modal-close uk-close"></a>
						</div>
						<form class="uk-form" data-am-handler="users" data-am-init data-am-confirm="<?php Text::e('confirm_delete_users') ;?>"></form>
						<div class="uk-modal-footer uk-text-right">
							<button type="button" class="uk-modal-close uk-button">
								<span class="uk-hidden-small"><i class="uk-icon-close"></i>&nbsp;</span>
								<?php Text::e('btn_close'); ?>
							</button>
							<button type="button" class="uk-button uk-button-danger" data-am-submit="users">
								<span class="uk-hidden-small"><i class="uk-icon-user-times"></i>&nbsp;</span>
								<?php Text::e('btn_remove_selected'); ?>
							</button>
						</div>
					</div>
				</div>
				<!-- Add User -->
				<br />
				<a href="#am-add-user-modal" class="uk-button uk-margin-top" data-uk-modal>
					<i class="uk-icon-user-plus"></i>&nbsp;&nbsp;<?php Text::e('sys_user_add'); ?>
				</a>
				<div id="am-add-user-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						<div class="uk-modal-header">
							<?php Text::e('sys_user_add'); ?>
							<a href="#" class="uk-modal-close uk-close"></a>
						</div>
						<form class="uk-form" data-am-handler="add_user" data-am-close-on-success="#am-add-user-modal">		
							<input class="uk-form-controls uk-form-large uk-width-1-1" type="text" name="username" placeholder="<?php Text::e('sys_user_add_name'); ?>" required data-am-enter="#am-add-user-submit" />	
							<input class="uk-form-controls uk-width-1-1 uk-margin-small-top" type="password" name="password1" placeholder="<?php Text::e('sys_user_add_password'); ?>" required data-am-enter="#am-add-user-submit" />		
							<input class="uk-form-controls uk-width-1-1 uk-margin-small-top" type="password" name="password2" placeholder="<?php Text::e('sys_user_add_repeat'); ?>" required data-am-enter="#am-add-user-submit" />
							<div class="uk-modal-footer uk-text-right">
								<button type="button" class="uk-modal-close uk-button">
									<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php Text::e('btn_close'); ?>
								</button>
								<button id="am-add-user-submit" type="submit" class="uk-button uk-button-primary">
									<i class="uk-icon-user-plus"></i>&nbsp;&nbsp;<?php Text::e('btn_add'); ?>
								</button>
							</div>
						</form>
					</div>
				</div>
				<!-- Change Password -->
				<hr />
				<a href="#am-change-password-modal" class="uk-button uk-button-danger" data-uk-modal>
					<i class="uk-icon-key"></i>&nbsp;&nbsp;<?php Text::e('sys_user_change_password'); ?>
				</a>
				<div id="am-change-password-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						<div class="uk-modal-header">
							<?php Text::e('sys_user_change_password'); ?>
							<a href="#" class="uk-modal-close uk-close"></a>
						</div>
						<form class="uk-form" data-am-handler="change_password" data-am-close-on-success="#am-change-password-modal">
							<input class="uk-form-controls uk-width-1-1" type="password" name="current-password" placeholder="<?php Text::e('sys_user_change_password_current'); ?>" required data-am-enter="#am-change-password-submit" />
							<input class="uk-form-controls uk-width-1-1 uk-margin-small-top" type="password" name="new-password1" placeholder="<?php Text::e('sys_user_change_password_new'); ?>" required data-am-enter="#am-change-password-submit" />
							<input class="uk-form-controls uk-width-1-1 uk-margin-small-top" type="password" name="new-password2" placeholder="<?php Text::e('sys_user_change_password_repeat'); ?>" required data-am-enter="#am-change-password-submit" />
							<div class="uk-modal-footer uk-text-right">
								<button type="button" class="uk-modal-close uk-button">
									<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php Text::e('btn_close'); ?>
								</button>
								<button id="am-change-password-submit" type="submit" class="uk-button uk-button-primary">
									<i class="uk-icon-check"></i>&nbsp;&nbsp;<?php Text::e('btn_save'); ?>
								</button>
							</div>
						</form>
					</div>
				</div>
			</li>
			<!-- Update -->
			<li>
				<div class="uk-block">
					<form class="uk-form uk-form-stacked" data-am-init data-am-handler="update_system">
						<i class="uk-icon-circle-o-notch uk-icon-spin uk-text-muted"></i>
					</form>
				</div>	
			</li>
			<!-- Debug -->
			<li>
				<div class="uk-block">
					<?php Text::e('sys_debug_info'); ?>
				</div>
				<form class="uk-form" data-am-handler="update_config" data-am-auto-submit>
					<input type="hidden" name="type" value="debug" />
					<label class="uk-button uk-button-large" data-am-toggle>
						<?php Text::e('sys_debug_enable'); ?>
						<input type="checkbox" name="debug" value="on" <?php if (AM_DEBUG_ENABLED) { echo ' checked'; } ?> />
					</label>
				</form>
			</li>
		</ul>

<?php


$this->element('footer');


?>