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


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The GUI Sytem Settings' Page. As part of the GUI, this file is only to be included via the GUI class.
 */


$this->guiTitle = $this->guiTitle . ' / ' . $this->tb['sys_title'];
$this->element('header');


?>
		
		<div class="column subnav">
			<div class="list-group">
				<div class="list-group-item">
					<ul class="nav nav-pills nav-stacked">
						<li class="active"><a href="#cache" data-toggle="tab"><span class="glyphicon glyphicon-hdd"></span> <?php echo $this->tb['sys_cache']; ?></a></li>
						<li><a href="#users" data-toggle="tab"><span class="glyphicon glyphicon-user"></span> <?php echo $this->tb['sys_user']; ?></a></li>
						<li><a href="#file_types" data-toggle="tab"><span class="glyphicon glyphicon-file"></span> <?php echo $this->tb['sys_file_types']; ?></a></li>
						<li><a href="#debug" data-toggle="tab"><span class="glyphicon glyphicon-info-sign"></span> <?php echo $this->tb['sys_debug']; ?></a></li>
					</ul>
				</div>
			</div>			
		</div>
		
		<div class="column content">
			<!-- Tab panes -->
			<div class="inner tab-content">
				<div id="cache" class="tab-pane fade in active">
					<div class="list-group">
						<div class="list-group-item">
							<h4><span class="glyphicon glyphicon-hdd"></span> <?php echo $this->tb['sys_cache']; ?></h4>
						</div>
						<div class="list-group-item">
							<a href="#" class="automad-status btn btn-lg btn-default" data-automad-status="cache" data-toggle="modal" data-target="#cache-settings-modal"></a>
						</div>
						<div class="list-group-item">	
							<a href="#" class="btn btn-default" data-toggle="modal" data-target="#cache-clear-modal"><span class="glyphicon glyphicon-refresh"></span> <?php echo $this->tb['sys_cache_clear']; ?></a>
							
						</div>
					</div>
				</div>	
				<div id="users" class="tab-pane fade">
					<div class="list-group">
						<div class="list-group-item">
							<h4><span class="glyphicon glyphicon-user"></span> <?php echo $this->tb['sys_user']; ?></h4>
						</div>
						<div class="list-group-item">
							<a href="#" class="btn btn-default" data-toggle="modal" data-target="#change-password-modal"><span class="glyphicon glyphicon-lock"></span> <?php echo $this->tb['sys_user_change_password']; ?></a>
						</div>
						<div class="list-group-item">
							<a href="#" class="btn btn-default" data-toggle="modal" data-target="#add-user-modal"><span class="glyphicon glyphicon-plus"></span> <?php echo $this->tb['sys_user_add']; ?></a>
						</div>
						<div class="list-group-item">
							<a href="#" class="automad-status btn btn-default" data-automad-status="users" data-toggle="modal" data-target="#users-modal"></a>
						</div>
					</div>
				</div>
				<div id="file_types" class="tab-pane fade">
					<div class="list-group">
						<div class="list-group-item">
							<h4><span class="glyphicon glyphicon-file"></span> <?php echo $this->tb['sys_file_types']; ?></h4>
						</div>
						<div class="list-group-item">
							<a href="#" class="btn btn-default" data-toggle="modal" data-target="#file-types-modal"><span class="glyphicon glyphicon-pencil"></span> <?php echo $this->tb['sys_file_types_edit']; ?></a>
						</div>
					</div>
				</div>
				<div id="debug" class="tab-pane fade">
					<div class="list-group">
						<div class="list-group-item">
							<h4><span class="glyphicon glyphicon-info-sign"></span> <?php echo $this->tb['sys_debug']; ?></h4>
						</div>
						<div class="list-group-item">
							<?php echo $this->tb['sys_debug_help']; ?>
							<br />
							<a href="#" class="automad-status btn btn-lg btn-default" data-automad-status="debug" data-toggle="modal" data-target="#debug-modal"></a>
						</div>
					</div>
				</div>
			</div>				
		</div>

		<!-- Modals -->
	
		<!-- Cache Settings -->
		<div class="modal fade automad-close-on-success" id="cache-settings-modal" tabindex="-1">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title"><?php echo $this->tb['sys_cache']; ?></h4>
					</div>
					<form class="automad-form" data-automad-handler="update_config">
						<div class="modal-body">
							<div class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default btn-lg<?php if (AM_CACHE_ENABLED) { echo ' active'; } ?>">
									<input type="radio" name="cache[enabled]" value="on"<?php if (AM_CACHE_ENABLED) { echo ' checked'; } ?> />On
								</label>
								<label class="btn btn-default btn-lg<?php if (!AM_CACHE_ENABLED) { echo ' active'; } ?>">
									<input type="radio" name="cache[enabled]" value="off"<?php if (!AM_CACHE_ENABLED) { echo ' checked'; } ?> />Off
								</label>
							</div>
							<br />
							<p class="text-muted"><?php echo $this->tb['sys_cache_lifetime']; ?></p>
							<div class="btn-group btn-group-justified" data-toggle="buttons">
								<?php
						
								$delays = array(60, 120, 300);
							
								// Set default delay, if the current setting is not in $delays, to prevent submitting an empty value for cache[monitor-delay].
								if (in_array(AM_CACHE_MONITOR_DELAY, $delays)) {
									$current = AM_CACHE_MONITOR_DELAY;
								} else {
									$current = end($delays);
								}
							
								foreach ($delays as $seconds) {
							
									echo '<label class="btn btn-default btn-sm';
							
									if ($seconds == $current) {
										echo ' active';
									}
							
									echo '"><input type="radio" name="cache[monitor-delay]" value="' . $seconds . '"';
							
									if ($seconds == $current) {
										echo ' checked';
									}
							
									echo ' />' . intval($seconds / 60) . ' min</label>';
							
								}
						
								?> 
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $this->tb['btn_ok']; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	
		<!-- Cache Clear -->
		<div class="modal fade" id="cache-clear-modal" tabindex="-1">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<form class="automad-form" data-automad-handler="clear_cache">
						<div class="modal-body">
							<button type="submit" class="btn btn-default btn-block btn-lg" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-repeat"></span> <?php echo $this->tb['sys_cache_clear']; ?></button>
						</div>
					</form>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button>
					</div>
				</div>
			</div>
		</div>
	
		<!-- File Types -->
		<div class="modal fade automad-close-on-success" id="file-types-modal" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title"><?php echo $this->tb['sys_file_types']; ?></h4>
					</div>
					<form class="automad-form" data-automad-handler="update_config">
						<div class="modal-body">
							<?php echo $this->tb['sys_file_types_help']; ?> 
							<input type="text" class="form-control" name="file-types" value="<?php echo AM_ALLOWED_FILE_TYPES; ?>" />
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $this->tb['btn_ok']; ?></button>
						</div>
					</form>
		
				</div>
			</div>
		</div>
	
		<!-- Debugging -->
		<div class="modal fade automad-close-on-success" id="debug-modal" tabindex="-1">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title"><?php echo $this->tb['sys_debug']; ?></h4>
					</div>
					<form class="automad-form" data-automad-handler="update_config">
						<div class="modal-body">
							<div class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default btn-lg<?php if (AM_DEBUG_ENABLED) { echo ' active'; } ?>">
									<input type="radio" name="debug" value="on"<?php if (AM_DEBUG_ENABLED) { echo ' checked'; } ?> />On
								</label>
								<label class="btn btn-default btn-lg<?php if (!AM_DEBUG_ENABLED) { echo ' active'; } ?>">
									<input type="radio" name="debug" value="off"<?php if (!AM_DEBUG_ENABLED) { echo ' checked'; } ?> />Off
								</label>
							</div>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $this->tb['btn_ok']; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	
		<!-- Change Password -->
		<div class="modal fade" id="change-password-modal" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title"><?php echo $this->tb['sys_user_change_password']; ?></h4>
					</div>
					<form class="automad-form automad-reset" data-automad-handler="change_password">
						<div class="modal-body">
							<div class="form-group">
								<label for="change-current-password" class="text-muted"><?php echo $this->tb['sys_user_change_password_current']; ?></label>
								<input id="change-current-password" class="form-control" type="password" name="current-password" required />
							</div>
							<div class="form-group">
								<label for="change-new-password1" class="text-muted"><?php echo $this->tb['sys_user_change_password_new']; ?></label>
								<input id="change-new-password1" class="form-control" type="password" name="new-password1" required />
							</div>
							<div class="form-group">
								<label for="change-new-password2" class="text-muted"><?php echo $this->tb['sys_user_change_password_repeat']; ?></label>
								<input id="change-new-password2" class="form-control" type="password" name="new-password2" required />
							</div>
						</div>
						<div class="modal-footer">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button>
								<button type="submit" class="btn btn-success" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $this->tb['btn_save']; ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	
		<!-- Add User -->
		<div class="modal fade" id="add-user-modal" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title"><?php echo $this->tb['sys_user_add']; ?></h4>
					</div>
					<form class="automad-form automad-reset" data-automad-handler="add_user">
						<div class="modal-body">
							<div class="form-group">
								<label for="add-username" class="text-muted"><?php echo $this->tb['sys_user_add_name']; ?></label>
								<input id="add-username" class="form-control" type="text" name="username" required />
							</div>
							<div class="form-group">
								<label for="add-password1" class="text-muted"><?php echo $this->tb['sys_user_add_password']; ?></label>
								<input id="add-password1" class="form-control" type="password" name="password1" required />
							</div>
							<div class="form-group">
								<label for="add-password2" class="text-muted"><?php echo $this->tb['sys_user_add_repeat']; ?></label>
								<input id="add-password2" class="form-control" type="password" name="password2" required />
							</div>
						</div>
						<div class="modal-footer">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button>
								<button type="submit" class="btn btn-primary" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-plus"></span> <?php echo $this->tb['btn_add']; ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	
		<!-- Users -->
		<div class="modal fade" id="users-modal" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title"><?php echo $this->tb['sys_user_registered']; ?></h4>
					</div>
					<form id="users" class="automad-form automad-init" data-automad-handler="users">
						<div class="modal-body"></div>
					</form>
				</div>
			</div>
		</div>

<?php


$this->element('footer');


?>