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
 *	The GUI Sytem Settings' Page. As part of the GUI, this file is only to be included via the GUI class.
 */


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('sys_title');
$this->element('header');
$this->element('title');


?>

		<div class="automad-navbar" data-uk-sticky="{showup:true,animation:'uk-animation-slide-top'}">
			<?php $this->element('searchbar'); ?>
			<div class="automad-navbar-context uk-width-1-1">
				<ul class="uk-subnav">
					<li>
						<a href="?context=system_settings" class="uk-text-truncate">
							<i class="uk-icon-cog uk-icon-justify"></i>&nbsp;&nbsp;<?php echo Text::get('sys_title'); ?>
						</a>
					</li>
				</ul>
			</div>
			<!-- Menu -->
			<ul class="uk-grid uk-grid-small uk-grid-width-1-4" data-uk-switcher="{connect:'#automad-sys-content', toggle:'> li > button', animation: 'uk-animation-fade'}">
				<!-- Cache Button -->
				<li>
					<button class="uk-button uk-width-1-1 uk-text-truncate" type="button">
						<i class="uk-icon-hdd-o"></i><span class="uk-hidden-small">&nbsp;&nbsp;<?php echo Text::get('sys_cache'); ?></span>
					</button>
				</li>
				<!-- User Button -->
				<li>
					<button class="uk-button uk-width-1-1 uk-text-truncate" type="button">
						<i class="uk-icon-user"></i><span class="uk-hidden-small">&nbsp;&nbsp;<?php echo Text::get('sys_user'); ?></span>
					</button>
				</li>
				<!-- File Types Button -->
				<li>
					<button class="uk-button uk-width-1-1 uk-text-truncate" type="button">
						<i class="uk-icon-file-o"></i><span class="uk-hidden-small">&nbsp;&nbsp;<?php echo Text::get('sys_file_types'); ?></span>
					</button>
				</li>
				<!-- Debug Button -->
				<li>
					<button class="uk-button uk-width-1-1 uk-text-truncate" type="button">
						<i class="uk-icon-bug"></i><span class="uk-hidden-small">&nbsp;&nbsp;<?php echo Text::get('sys_debug'); ?></span>
					</button>
				</li>
			</ul>
		</div>

		<ul id="automad-sys-content" class="uk-switcher">
			
			<!-- Cache -->
			<li>
				<div class="uk-block">
					<div class="uk-panel uk-panel-box uk-panel-box-primary">
						<?php echo Text::get('sys_cache_info'); ?>
					</div>
				</div>
				<!-- Cache Enable / Settings -->
				<form class="uk-form uk-form-stacked" data-automad-handler="update_config" data-automad-auto-submit>
					<!-- Cache Enable -->
					<input type="hidden" name="type" value="cache" />		
					<label class="uk-button uk-button-large" data-automad-toggle="#automad-cache-settings, [data-automad-handler='clear_cache']">
						<span data-automad-status="cache"></span>
						<input type="checkbox" name="cache[enabled]" value="on"<?php if (AM_CACHE_ENABLED) { echo ' checked'; } ?> />
					</label>
					<!-- Cache Settings -->
					<div id="automad-cache-settings" class="uk-margin-top">
						<ul class="uk-grid uk-grid-width-medium-1-2">
							<li>
								<!-- Cache Monitor Delay -->			
								<label class="uk-form-label"><?php echo Text::get('sys_cache_monitor'); ?></label>		
								<?php
						
								$delays = array(60, 120, 300);
							
								// Set default delay, if the current setting is not in $delays, to prevent submitting an empty value for cache[monitor-delay].
								if (in_array(AM_CACHE_MONITOR_DELAY, $delays)) {
									$current = AM_CACHE_MONITOR_DELAY;
								} else {
									$current = end($delays);
								}
							
								foreach ($delays as $seconds) { ?>
							
									<label class="uk-button uk-margin-small-top uk-text-left" data-automad-toggle>		
										<?php echo intval($seconds / 60); ?> min
										<input type="radio" name="cache[monitor-delay]" value="<?php echo $seconds; ?>"<?php if ($seconds == $current) { echo ' checked'; } ?> />
									</label>
									
								<?php } ?> 
							</li>
							<li>
								<hr class="uk-visible-small" />
								<!-- Cache Lifetime -->			
								<label class="uk-form-label"><?php echo Text::get('sys_cache_lifetime'); ?></label>		
								<?php
						
								$lifetimes = array(3600, 21600, 43200);
							
								// Set default delay, if the current setting is not in $delays, to prevent submitting an empty value for cache[monitor-delay].
								if (in_array(AM_CACHE_LIFETIME, $lifetimes)) {
									$current = AM_CACHE_LIFETIME;
								} else {
									$current = end($lifetimes);
								}
							
								foreach ($lifetimes as $seconds) { ?>
							
									<label class="uk-button uk-margin-small-top uk-text-left" data-automad-toggle>		
										<?php echo intval($seconds / 3600); ?> h
										<input type="radio" name="cache[lifetime]" value="<?php echo $seconds; ?>"<?php if ($seconds == $current) { echo ' checked'; } ?> />
									</label>
									
								<?php } ?> 
							</li>		
						</ul>
					</div>	
				</form>
				<!-- Clear Cache -->	
				<div class="uk-block">
					<form data-automad-handler="clear_cache">
						
						<button type="submit" class="uk-button uk-button-large uk-width-1-1">
							<i class="uk-icon-refresh"></i>&nbsp;&nbsp;<?php echo Text::get('sys_cache_clear'); ?>
						</button>
					</form>
				</div>
			</li>
			
			<!-- User -->
			<li>
				<div class="uk-block">
					<div class="uk-panel uk-panel-box uk-panel-box-primary">
						<?php echo Text::get('sys_user_info'); ?>
					</div>
				</div>
				<!-- Registered Users -->
				<a href="#automad-users-modal" class="uk-button uk-button-large uk-width-1-1" data-uk-modal data-automad-status="users"></a>
				<div id="automad-users-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						<div class="uk-modal-header">
							<?php echo Text::get('sys_user_registered'); ?>
						</div>
						<form class="uk-form" data-automad-handler="users" data-automad-init data-automad-confirm="<?php echo Text::get('confirm_delete_users') ;?>"></form>
						<div class="uk-modal-footer uk-text-right">
							<button type="button" class="uk-modal-close uk-button">
								<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php echo Text::get('btn_close'); ?>
							</button>
							<button type="button" class="uk-button uk-button-danger" data-automad-submit="users">
								<i class="uk-icon-user-times"></i>&nbsp;&nbsp;<?php echo Text::get('btn_remove_selected'); ?>
							</button>
						</div>
					</div>
				</div>
				<!-- Add User -->
				<a href="#automad-add-user-modal" class="uk-button uk-width-1-1 uk-margin-small-top" data-uk-modal>
					<i class="uk-icon-user-plus"></i>&nbsp;&nbsp;<?php echo Text::get('sys_user_add'); ?>
				</a>
				<div id="automad-add-user-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						<form class="uk-form" data-automad-handler="add_user" data-automad-close-on-success="#automad-add-user-modal">
							<div class="uk-modal-header">
								<?php echo Text::get('sys_user_add'); ?>
							</div>		
							<input class="uk-form-controls uk-form-large uk-width-1-1" type="text" name="username" placeholder="<?php echo Text::get('sys_user_add_name'); ?>" required data-automad-enter="#automad-add-user-submit" />	
							<input class="uk-form-controls uk-width-1-1 uk-margin-large-top" type="password" name="password1" placeholder="<?php echo Text::get('sys_user_add_password'); ?>" required data-automad-enter="#automad-add-user-submit" />		
							<input class="uk-form-controls uk-width-1-1 uk-margin-small-top" type="password" name="password2" placeholder="<?php echo Text::get('sys_user_add_repeat'); ?>" required data-automad-enter="#automad-add-user-submit" />
							<div class="uk-modal-footer uk-text-right">
								<button type="button" class="uk-modal-close uk-button">
									<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php echo Text::get('btn_close'); ?>
								</button>
								<button id="automad-add-user-submit" type="submit" class="uk-button uk-button-primary">
									<i class="uk-icon-user-plus"></i>&nbsp;&nbsp;<?php echo Text::get('btn_add'); ?>
								</button>
							</div>
						</form>
					</div>
				</div>
				<!-- Change Password -->
				<a href="#automad-change-password-modal" class="uk-button uk-width-1-1 uk-margin-small-top" data-uk-modal>
					<i class="uk-icon-key"></i>&nbsp;&nbsp;<?php echo Text::get('sys_user_change_password'); ?>
				</a>
				<div id="automad-change-password-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						<form class="uk-form" data-automad-handler="change_password" data-automad-close-on-success="#automad-change-password-modal">
							<div class="uk-modal-header">
								<?php echo Text::get('sys_user_change_password'); ?>
							</div>
							<input class="uk-form-controls uk-width-1-1" type="password" name="current-password" placeholder="<?php echo Text::get('sys_user_change_password_current'); ?>" required data-automad-enter="#automad-change-password-submit" />
							<input class="uk-form-controls uk-width-1-1 uk-margin-small-top" type="password" name="new-password1" placeholder="<?php echo Text::get('sys_user_change_password_new'); ?>" required data-automad-enter="#automad-change-password-submit" />
							<input class="uk-form-controls uk-width-1-1 uk-margin-small-top" type="password" name="new-password2" placeholder="<?php echo Text::get('sys_user_change_password_repeat'); ?>" required data-automad-enter="#automad-change-password-submit" />
							<div class="uk-modal-footer uk-text-right">
								<button type="button" class="uk-modal-close uk-button">
									<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php echo Text::get('btn_close'); ?>
								</button>
								<button id="automad-change-password-submit" type="submit" class="uk-button uk-button-primary">
									<i class="uk-icon-check"></i>&nbsp;&nbsp;<?php echo Text::get('btn_save'); ?>
								</button>
							</div>
						</form>
					</div>
				</div>
			</li>
			
			<!-- File Types -->
			<li>
				<div class="uk-block">
					<div class="uk-panel uk-panel-box uk-panel-box-primary">
						<?php echo Text::get('sys_file_types_info') . Text::get('sys_file_types_help'); ?>
					</div>
				</div>
				<form class="uk-form" data-automad-handler="update_config" data-automad-auto-submit>
					<input type="hidden" name="type" value="file-types" />
					<input type="text" class="uk-form-controls uk-width-1-1" name="file-types" value="<?php echo AM_ALLOWED_FILE_TYPES; ?>" data-automad-default="<?php echo AM_ALLOWED_FILE_TYPES_DEFAULT_GUI; ?>" />
				</form>
			</li>
			
			<!-- Debug -->
			<li>
				<div class="uk-block">
					<div class="uk-panel uk-panel-box uk-panel-box-primary">
						<?php echo Text::get('sys_debug_info'); ?>
					</div>
				</div>
				<form class="uk-form" data-automad-handler="update_config" data-automad-auto-submit>
					<input type="hidden" name="type" value="debug" />
					<label class="uk-button uk-button-large" data-automad-toggle>
						<span data-automad-status="debug"></span>
						<input type="checkbox" name="debug" value="on" <?php if (AM_DEBUG_ENABLED) { echo ' checked'; } ?> />
					</label>
				</form>
			</li>
			
		</ul>

<?php


$this->element('footer');


?>