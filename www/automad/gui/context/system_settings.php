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
 *	The GUI Sytem Settings' Page. As part of the GUI, this file is only to be included via the GUI class.
 */


$this->guiTitle = $this->guiTitle . ' / System Settings';
$this->element('header');


// Get config from json file.
$config = json_decode(file_get_contents(AM_CONFIG), true);


// Normalize $config and get missing items from defaults in const.php
foreach (array('AM_DEBUG_ENABLED', 'AM_CACHE_ENABLED', 'AM_CACHE_MONITOR_DELAY', 'AM_ALLOWED_FILE_TYPES') as $const) {	
	if (!isset($config[$const])) {
		$config[$const] = constant($const);
	}
}


?>

	<div class="row">

		<div class="col-md-4">
			<?php $this->element('navigation');?>
		</div>
		
		<div class="col-md-4">
		
			<div class="list-group">
				<div class="list-group-item">
					<h4><span class="glyphicon glyphicon-hdd"></span> Cache</h4>
				</div>
				<div class="list-group-item">
					<ul class="nav nav-pills nav-justified">
						<li><a href="#" data-toggle="modal" data-target="#cache-settings-modal"><span class="glyphicon glyphicon-hdd"></span> Cache Settings</a></li>
					</ul>
				</div>
				<div class="list-group-item">
					<ul class="nav nav-pills nav-justified">
						<li><a href="#" data-toggle="modal" data-target="#cache-clear-modal"><span class="glyphicon glyphicon-refresh"></span> Clear Cache</a></li>
					</ul>
				</div>
			</div>
			
			<div class="list-group">
				<div class="list-group-item">
					<h4><span class="glyphicon glyphicon-ok-sign"></span> Allowed File Types</h4>
				</div>
				<div class="list-group-item">
					<ul class="nav nav-pills nav-justified">
						<li><a href="#" data-toggle="modal" data-target="#file-types-modal"><span class="glyphicon glyphicon-th-list"></span> Edit The List of Allowed File Types</a></li>
					</ul>
				</div>
			</div>
			
			<div class="list-group">
				<div class="list-group-item">
					<h4><span class="glyphicon glyphicon-info-sign"></span> Debugging</h4>
				</div>
				<div class="list-group-item">
					<ul class="nav nav-pills nav-justified">
						<li><a href="#" data-toggle="modal" data-target="#debug-modal"><span class="glyphicon glyphicon-record"></span> Enable Debug Mode</a></li>
					</ul>
				</div>
			</div>
		
		</div>
		
		<div class="col-md-4">
			<div class="list-group">
				<div class="list-group-item">
					<h4><span class="glyphicon glyphicon-user"></span> Users</h4>
				</div>
				<div class="list-group-item">
					<ul class="nav nav-pills nav-justified">
						<li><a href="#" data-toggle="modal" data-target="#change-password-modal"><span class="glyphicon glyphicon-lock"></span> Change Your Password</a></li>
					</ul>
				</div>
				<div class="list-group-item">
					<ul class="nav nav-pills nav-justified">
						<li><a href="#" data-toggle="modal" data-target="#add-user-modal"><span class="glyphicon glyphicon-plus"></span> Add User</a></li>
					</ul>
				</div>
				<div class="list-group-item">
					<ul class="nav nav-pills nav-justified">
						<li><a href="#" data-toggle="modal" data-target="#remove-users-modal"><span class="glyphicon glyphicon-trash"></span> Remove Users</a></li>
					</ul>
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
					<h4 class="modal-title">Page Cache</h4>
				</div>
				<form class="automad-form" data-automad-handler="update_config">
					<div class="modal-body">
						<div class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-default btn-lg<?php if ($config['AM_CACHE_ENABLED']) { echo ' active'; } ?>">
								<input type="radio" name="cache[enabled]" value="on"<?php if ($config['AM_CACHE_ENABLED']) { echo ' checked'; } ?> />On
							</label>
							<label class="btn btn-default btn-lg<?php if (!$config['AM_CACHE_ENABLED']) { echo ' active'; } ?>">
								<input type="radio" name="cache[enabled]" value="off"<?php if (!$config['AM_CACHE_ENABLED']) { echo ' checked'; } ?> />Off
							</label>
						</div>
						<br />
						<p class="text-muted">Scan for Changes Every</p>
						<div class="btn-group btn-group-justified" data-toggle="buttons">
							<?php
						
							$delays = array(120, 600, 3600, 7200);
							
							// Set default, in case $config['AM_CACHE_MONITOR_DELAY'] is not in $delays.
							if (!in_array($config['AM_CACHE_MONITOR_DELAY'], $delays)) {
								$config['AM_CACHE_MONITOR_DELAY'] = end($delays);
							}
							
							foreach ($delays as $seconds) {
							
								echo '<label class="btn btn-default btn-sm';
							
								if ($seconds == $config['AM_CACHE_MONITOR_DELAY']) {
									echo ' active';
								}
							
								echo '"><input type="radio" name="cache[monitor-delay]" value="' . $seconds . '"';
							
								if ($seconds == $config['AM_CACHE_MONITOR_DELAY']) {
									echo ' checked';
								}
							
								echo ' />' . intval($seconds / 60) . ' min</label>';
							
							}
						
							?> 
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-default btn-block" data-loading-text="Saving ..."><span class="glyphicon glyphicon-ok"></span> Ok</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<!-- Cache Clear -->
	<div class="modal fade" id="cache-clear-modal" tabindex="-1">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<br />
				<form class="automad-form" data-automad-handler="clear_cache">
					<div class="modal-body">
						<button type="submit" class="btn btn-default btn-block btn-lg"><span class="glyphicon glyphicon-repeat"></span> Clear Cache Now</button>
					</div>
				</form>
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-block" data-dismiss="modal">Cancel</button>
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
					<h4 class="modal-title">List of Allowed File Types</h4>
				</div>
				<form class="automad-form" data-automad-handler="update_config">
					<div class="modal-body">
						<p class="text-muted">Add preferred extensions, separated by commas.<br />Leaving the field empty will reset the list to the default values!</p>
						<input type="text" class="form-control" name="file-types" value="<?php echo implode(AM_PARSE_STR_SEPARATOR . ' ', unserialize($config['AM_ALLOWED_FILE_TYPES'])); ?>" />
						
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-default btn-block" data-loading-text="Saving ..."><span class="glyphicon glyphicon-ok"></span> Ok</button>
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
					<h4 class="modal-title">Debug Mode</h4>
				</div>
				<form class="automad-form" data-automad-handler="update_config">
					<div class="modal-body">
						<div class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-default btn-lg<?php if ($config['AM_DEBUG_ENABLED']) { echo ' active'; } ?>">
								<input type="radio" name="debug" value="on"<?php if ($config['AM_DEBUG_ENABLED']) { echo ' checked'; } ?> />On
							</label>
							<label class="btn btn-default btn-lg<?php if (!$config['AM_DEBUG_ENABLED']) { echo ' active'; } ?>">
								<input type="radio" name="debug" value="off"<?php if (!$config['AM_DEBUG_ENABLED']) { echo ' checked'; } ?> />Off
							</label>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-default btn-block" data-loading-text="Saving ..."><span class="glyphicon glyphicon-ok"></span> Ok</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<!-- Change Password -->
	<div class="modal fade" id="change-password-modal" tabindex="-1">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Change Your Password</h4>
				</div>
				<div class="modal-body">
					<p>One fine body&hellip;</p>
				</div>
				<div class="modal-footer">
					<div class="btn-group">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Save</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Add User -->
	<div class="modal fade" id="add-user-modal" tabindex="-1">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Add User</h4>
				</div>
				<div class="modal-body">
					<p>One fine body&hellip;</p>
				</div>
				<div class="modal-footer">
					<div class="btn-group">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Add</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Remove Users -->
	<div class="modal fade" id="remove-users-modal" tabindex="-1">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Remove Users</h4>
				</div>
				<div class="modal-body">
					<p>One fine body&hellip;</p>
				</div>
				<div class="modal-footer">
					<div class="btn-group">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-danger">Remove Selected</button>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php


$this->element('footer');


?>