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
 *	Copyright (c) 2017-2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
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
		
			System\Update::init();
		
		} else {
		
			// Start buffering the HTML.
			ob_start();
	
			// Get version and test connection.
			if ($version = System\Update::getVersion()) {
		
				// Check if an the current installation is outdated.
				if (version_compare(AM_VERSION, $version, '<')) {
					
					?>
					<div class="am-update-progress">
						<p>
							<?php echo Text::get('sys_update_current_version') . ' ' . AM_VERSION; ?>.
							<br />
							<?php Text::e('sys_update_available'); ?>
						</p>
						<input type="hidden" name="update" value="run" />
						<button 
						type="submit" 
						class="uk-button uk-button-large uk-button-success" 
						data-uk-toggle="{target:'.am-update-progress',cls:'uk-hidden'}"
						>
							<i class="uk-icon-refresh"></i>&nbsp;
							<?php Text::e('sys_update_to'); ?>&nbsp;
							<span class="uk-badge"><?php echo $version; ?></span>
						</button>
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


echo json_encode($output);


?>