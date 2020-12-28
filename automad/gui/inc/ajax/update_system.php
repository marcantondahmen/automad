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
 *	Copyright (c) 2017-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;
use Automad\System as System;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	Update Automad.
 */


// To prevent accidental updates within the development repository, exit updater in case the base directoy contains "/automad-dev".
if (strpos(AM_BASE_DIR, '/automad-dev') !== false) {
	
	$output['html'] = 	'<div class="uk-alert uk-alert-danger">' .
						'Can\'t run updates within the development repository!' .
						'</div>';
	
} else {
	
	// Test if server supports all required functions/extensions.
	if (System\Update::supported()) {
		
		if (!empty($_POST['update'])) {
		
			$output = System\Update::run();
		
		} else {
		
			// Start buffering the HTML.
			ob_start();
	
			// Get version and test connection.
			if ($version = System\Update::getVersion()) {
		
				// Check if an the current installation is outdated.
				if (version_compare(AM_VERSION, $version, '<')) {
					
					?>
					<div class="am-update-progress">
						<input type="hidden" name="update" value="run" />
						<?php if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { ?>
						<div class="uk-alert uk-alert-danger uk-margin-top uk-margin-bottom-remove">
							<?php Text::e('sys_update_windows_warning'); ?>
						</div>
						<?php } ?>
						<div class="uk-alert uk-alert-success" data-icon="&#xf021">
							<?php echo Text::get('sys_update_current_version') . ' <b>' . AM_VERSION . '</b>'; ?>.
							<br>
							<?php Text::e('sys_update_available'); ?>
							<div class="uk-margin-top">
								<button 
								type="submit" 
								class="uk-button uk-button-success uk-text-nowrap" 
								data-uk-toggle="{target:'.am-update-progress',cls:'uk-hidden'}"
								>
									<i class="uk-icon-download"></i>&nbsp;
									<?php Text::e('sys_update_to'); ?>
									<b><?php echo $version; ?></b>
								</button>
								<a href="https://automad.org/release-notes" class="uk-button uk-button-link uk-hidden-small" target="_blank">
									<i class="uk-icon-file-text-o"></i>&nbsp;
									Release Notes
								</a>
							</div>
						</div>
						<p>
							<?php Text::e('sys_update_items'); ?>
						</p>
						<ul>
							<?php foreach (Core\Parse::csv(AM_UPDATE_ITEMS) as $item) {	
								echo '<li>' . $item . '</li>';
							} ?>
						</ul>
					</div>
					<div class="am-update-progress am-progress-panel uk-progress uk-progress-striped uk-active uk-hidden">
						<div class="uk-progress-bar" style="width: 100%;">			
							<?php Text::e('sys_update_progress'); ?>
						</div>
					</div>
					<?php
			
				} else {
			
					?>
					<div class="uk-alert uk-alert-success">
						<?php Text::e('sys_update_not_required'); ?>
						<?php echo Text::get('sys_update_current_version') . ' ' . AM_VERSION; ?>.
					</div>
					<?php
			
				}
		
			} else {
		
				?>
				<div class="uk-alert uk-alert-danger">
					<?php Text::e('error_update_connection'); ?>
				</div>
				<?php
		
			}
	
			// Save buffer to JSON array.
			$output['html'] = ob_get_contents();
			ob_end_clean();	
		
		}
		
	} else {
			
		$output['html'] = '<div class="uk-alert uk-alert-danger">' . Text::get('error_update_not_supported') . '</div>';
		
	}
	
}


$this->jsonOutput($output);


?>