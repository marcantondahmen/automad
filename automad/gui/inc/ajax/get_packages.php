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
 *	Copyright (c) 2019-2020 by Marc Anton Dahmen
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

		<form class="uk-display-inline-block" data-am-handler="update_all_packages">
			<button 
			class="uk-button uk-button-success"
			data-uk-modal="{target:'#am-modal-update-all-packages-progress',keyboard:false,bgclose:false}"
			>
				<i class="uk-icon-refresh"></i>&nbsp;
				<?php Text::e('packages_update_all'); ?>
			</button>
		</form>&nbsp;
		
		<a 
		href="https://packages.automad.org" 
		class="uk-button uk-button-link uk-hidden-small" 
		target="_blank"
		>
			<i class="uk-icon-folder-open-o"></i>&nbsp;
			<?php Text::e('packages_browse'); ?>
		</a>

		<ul 
		class="uk-grid uk-grid-width-medium-1-3 uk-margin-top" 
		data-uk-grid-margin 
		data-uk-grid-match="{target:'.uk-panel'}"
		>
			<?php foreach ($packages as $package) { 
				echo '<li>' . Components\Card\Package::render($package) . '</li>';
			} ?> 
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