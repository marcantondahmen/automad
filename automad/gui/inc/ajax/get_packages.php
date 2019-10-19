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
 *	Copyright (c) 2019 by Marc Anton Dahmen
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
 *	Get packages from Packagist.
 */

$output = array();

if ($packages = PackageManager::getPackages()) {

	// Start buffering the HTML.
	ob_start();

	?>

		<form class="uk-display-inline-block" data-am-handler="update_packages">
			<button 
			class="uk-button uk-button-success"
			data-uk-modal="{target:'#am-modal-update-packages-progress',keyboard:false,bgclose:false}"
			>
				<i class="uk-icon-refresh"></i>&nbsp;
				<?php Text::e('packages_update_all'); ?>
			</button>
		</form>&nbsp;
		
		<a 
		href="https://packages.automad.org" 
		class="uk-button uk-hidden-small" 
		target="_blank"
		>
			<i class="uk-icon-share"></i>&nbsp;
			<?php Text::e('packages_browse'); ?>
		</a>

		<ul 
		class="uk-grid uk-grid-width-medium-1-3 uk-margin-top" 
		data-uk-grid-margin 
		data-uk-grid-match="{target:'.uk-panel'}"
		>

		<?php foreach ($packages as $package) { ?>
		
			<li>
				<div class="uk-panel uk-panel-box<?php if ($package->installed) { ?> uk-panel-box-primary<?php } ?>">
					<a 
					href="<?php echo $package->info; ?>" 
					class="uk-display-block"
					target="_blank"
					>
						<i class="uk-icon-file-archive-o uk-icon-medium"></i>
						<div class="uk-panel-title uk-margin-small-top uk-padding-top-remove">
								<?php echo $package->name; ?>
						</div>
						<div class="uk-text-small">
							<?php echo $package->description; ?>
						</div>
						<?php  if ($package->installed) { ?>
							<span class="uk-panel-badge uk-badge">
								<i class="uk-icon-check"></i>&nbsp;
								<?php Text::e('packages_installed'); ?>
							</span>
						<?php } ?>
					</a>
					<div class="am-panel-bottom">
						<div class="am-panel-bottom-right">
							<a 
							href="<?php echo $package->info; ?>" 
							class="uk-button uk-button-small"
							target="_blank"
							>
								<i class="uk-icon-file-text-o"></i>&nbsp;	
								<?php Text::e('btn_readme'); ?>
							</a>&nbsp;
							<?php if ($package->installed) { ?>
								<form class="uk-display-inline-block" data-am-handler="remove_package">
									<input type="hidden" name="package" value="<?php echo $package->name; ?>">
									<button 
									class="uk-button uk-button-small uk-button-primary"
									data-uk-modal="{target:'#am-modal-remove-package-progress',keyboard:false,bgclose:false}"
									>
										<i class="uk-icon-close"></i>&nbsp;	
										<?php Text::e('btn_remove'); ?>
									</button>
								</form>
							<?php } else { ?>
								<form class="uk-display-inline-block" data-am-handler="install_package">
									<input type="hidden" name="package" value="<?php echo $package->name; ?>">
									<button 
									class="uk-button uk-button-small uk-button-success"
									data-uk-modal="{target:'#am-modal-install-package-progress',keyboard:false,bgclose:false}"
									>
										<i class="uk-icon-download"></i>&nbsp;	
										<?php Text::e('btn_install'); ?>
									</button>
								</form>
							<?php } ?>
						</div>
					</div>
				</div>
			</li>

		<?php } ?>

		</ul>

	<?php	
	
	// Save buffer to JSON array.
	$output['html'] = ob_get_contents();
	ob_end_clean();	

} else {

	$output['error'] = Text::get('error_packages');

}

$this->jsonOutput($output);

?>