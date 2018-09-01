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
 *	Copyright (c) 2014-2018 by Marc Anton Dahmen
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
	
		<ul class="uk-subnav uk-subnav-pill uk-margin-top">
			<li class="uk-disabled uk-hidden-small"><i class="uk-icon-sliders"></i></li>
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
				<?php Text::e('sys_cache_info'); ?>
				<!-- Cache Enable / Settings -->
				<form 
				class="uk-form uk-form-stacked" 
				data-am-handler="update_config" 
				data-am-auto-submit
				>
					<!-- Cache Enable -->
					<input type="hidden" name="type" value="cache" />		
					<label 
					class="am-toggle-switch-large" 
					data-am-toggle="#am-cache-settings, #am-cache-actions"
					>
						<?php Text::e('sys_cache_enable'); ?>
						<input 
						type="checkbox" 
						name="cache[enabled]" 
						value="on"<?php if (AM_CACHE_ENABLED) { echo ' checked'; } ?> 
						/>
					</label>
					<!-- Cache Settings -->
					<div id="am-cache-settings" class="am-toggle-container">
						<!-- Cache Monitor Delay -->
						<p class="uk-margin-large-top"><?php Text::e('sys_cache_monitor_info') ?></p>
						<?php 
							echo $this->Html->select(
								'cache[monitor-delay]',
								array(
									'1 min' => 60,
									'2 min' => 120,
									'5 min' => 300
								),
								AM_CACHE_MONITOR_DELAY,
								Text::get('sys_cache_monitor')
							); 
						?>
						<!-- Cache Lifetime -->
						<p class="uk-margin-large-top"><?php Text::e('sys_cache_lifetime_info') ?></p>
						<?php 
							echo $this->Html->select(
								'cache[lifetime]',
								array(
									'1 h' => 3600,
									'6 h' => 21600,
									'12 h' => 43200
								),
								AM_CACHE_LIFETIME,
								Text::get('sys_cache_lifetime')
							);
						?> 
					</div>	
				</form>
				<div id="am-cache-actions" class="am-toggle-container uk-margin-large-top">
					<!-- Clear Cache -->	
					<?php Text::e('sys_cache_clear_info'); ?>
					<form data-am-handler="clear_cache">
						<button type="submit" class="uk-button uk-button-success uk-button-large uk-margin-bottom">
							<i class="uk-icon-refresh"></i>&nbsp;
							<?php Text::e('sys_cache_clear'); ?>
						</button>
					</form>	
					<?php if ($tmp = FileSystem::getTmpDir()) { ?>
					<!-- Purge Cache -->
					<?php Text::e('sys_cache_purge_info'); ?>
					<form data-am-handler="purge_cache">
						<button type="submit" class="uk-button uk-button-success uk-button-large">
							<?php Text::e('sys_cache_purge'); ?>&nbsp;
							<i class="uk-icon-angle-right"></i>
							&nbsp;<span class="uk-badge"><?php echo $tmp; ?></span>
						</button>
					</form>
					<?php } ?>
				</div>
			</li>
			<!-- User -->
			<li>
				<?php Text::e('sys_user_info'); ?>
				<!-- Registered Users -->
				<a 
				href="#am-users-modal" 
				class="uk-button uk-button-large uk-button-success" 
				data-uk-modal 
				data-am-status="users"
				></a>
				<div id="am-users-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						<div class="uk-modal-header">
							<?php Text::e('sys_user_registered'); ?>
							<a href="#" class="uk-modal-close uk-close"></a>
						</div>
						<form 
						class="uk-form" 
						data-am-handler="users" 
						data-am-init 
						data-am-confirm="<?php Text::e('confirm_delete_users') ;?>"
						></form>
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
						<form 
						class="uk-form" 
						data-am-handler="add_user" 
						data-am-close-on-success="#am-add-user-modal"
						>		
							<input 
							class="uk-form-controls uk-form-large uk-width-1-1" 
							type="text" 
							name="username" 
							placeholder="<?php Text::e('sys_user_add_name'); ?>"
							data-am-enter="#am-add-user-submit" 
							required
							/>	
							<input 
							class="uk-form-controls uk-width-1-1 uk-margin-small-top" 
							type="password" 
							name="password1" 
							placeholder="<?php Text::e('sys_user_add_password'); ?>"  
							data-am-enter="#am-add-user-submit" 
							required
							/>		
							<input 
							class="uk-form-controls uk-width-1-1" 
							type="password" 
							name="password2" 
							placeholder="<?php Text::e('sys_user_add_repeat'); ?>"  
							data-am-enter="#am-add-user-submit" 
							required
							/>
							<div class="uk-modal-footer uk-text-right">
								<button type="button" class="uk-modal-close uk-button">
									<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php Text::e('btn_close'); ?>
								</button>
								<button id="am-add-user-submit" type="submit" class="uk-button uk-button-success">
									<i class="uk-icon-user-plus"></i>&nbsp;&nbsp;<?php Text::e('btn_add'); ?>
								</button>
							</div>
						</form>
					</div>
				</div>
				<!-- Change Password -->
				<br />
				<a 
				href="#am-change-password-modal" 
				class="uk-button uk-margin-small-top" 
				data-uk-modal
				>
					<i class="uk-icon-key"></i>&nbsp;&nbsp;<?php Text::e('sys_user_change_password'); ?>
				</a>
				<div id="am-change-password-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						<div class="uk-modal-header">
							<?php Text::e('sys_user_change_password'); ?>
							<a href="#" class="uk-modal-close uk-close"></a>
						</div>
						<form 
						class="uk-form" 
						data-am-handler="change_password" 
						data-am-close-on-success="#am-change-password-modal"
						>
							<input 
							class="uk-form-controls uk-width-1-1" 
							type="password" 
							name="current-password" 
							placeholder="<?php Text::e('sys_user_change_password_current'); ?>"  
							data-am-enter="#am-change-password-submit" 
							required
							/>
							<input 
							class="uk-form-controls uk-width-1-1" 
							type="password" 
							name="new-password1" 
							placeholder="<?php Text::e('sys_user_change_password_new'); ?>"  
							data-am-enter="#am-change-password-submit" 
							required
							/>
							<input 
							class="uk-form-controls uk-width-1-1" 
							type="password" 
							name="new-password2" 
							placeholder="<?php Text::e('sys_user_change_password_repeat'); ?>"  
							data-am-enter="#am-change-password-submit" 
							required
							/>
							<div class="uk-modal-footer uk-text-right">
								<button type="button" class="uk-modal-close uk-button">
									<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php Text::e('btn_close'); ?>
								</button>
								<button id="am-change-password-submit" type="submit" class="uk-button uk-button-success">
									<i class="uk-icon-check"></i>&nbsp;&nbsp;<?php Text::e('btn_save'); ?>
								</button>
							</div>
						</form>
					</div>
				</div>
			</li>
			<!-- Update -->
			<li>
				<form class="uk-form uk-form-stacked" data-am-init data-am-handler="update_system">
					<?php echo $this->Html->loading(); ?>
				</form>
			</li>
			<!-- Debug -->
			<li>
				<?php Text::e('sys_debug_info'); ?>
				<form class="uk-form" data-am-handler="update_config" data-am-auto-submit>
					<input type="hidden" name="type" value="debug" />
					<label class="am-toggle-switch-large" data-am-toggle>
						<?php Text::e('sys_debug_enable'); ?>
						<input 
						type="checkbox" 
						name="debug" 
						value="on" <?php if (AM_DEBUG_ENABLED) { echo ' checked'; } ?> 
						/>
					</label>
				</form>
			</li>
		</ul>

<?php


$this->element('footer');


?>