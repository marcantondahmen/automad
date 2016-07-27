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
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 * 	All ajax requests regarding the site's shared data file get processed here.
 *	Basically that means "Saving" as the first option and "Loading" as the second option.
 *
 *	When "$_POST['data']" exists, that means, that a form with "edited" page information got submitted and the data gets processed to be written into the data file.
 *
 *	NOTE: Only the inner elements of the form are returned. To keep the outer form information lose form the processing here, there must be an outer form existing on the page to wrap that HTML output.	
 */


// Array for returned JSON data.
$output = array();


if (isset($_POST['data'])) {

	// Save changes.
	$output = $this->Content->saveSharedData($_POST['data']);
			
} else {
	
	// Get shared data from Shared object.
	$data = $this->Automad->Shared->data;
	
	// Start buffering the HTML.
	ob_start();
	
	
	?>
	
		<div class="uk-form-row">
			<label for="automad-input-data-sitename" class="uk-form-label"><?php echo ucwords(AM_KEY_SITENAME); ?></label>
			<input id="automad-input-data-sitename" class="uk-form-controls uk-form-large uk-width-1-1" type="text" name="data[<?php echo AM_KEY_SITENAME; ?>]" value="<?php echo str_replace('"', '&quot;', $data[AM_KEY_SITENAME]); ?>" />
		</div>
		<div class="uk-form-row">
			<label for="automad-input-data-theme" class="uk-form-label">Theme</label>
			<select id="automad-input-data-theme" class="uk-form-controls uk-width-1-1" name="data[<?php echo AM_KEY_THEME; ?>]">
				<?php
			
				// Get available themes.
				$themes = glob(AM_BASE_DIR . AM_DIR_THEMES . '/*', GLOB_ONLYDIR);
			
				foreach ($themes as $theme) {
				
					echo '<option'; 
				
					if (basename($theme) == $data[AM_KEY_THEME]) {
						echo ' selected';
					}
				
					echo ' value="' . basename($theme) . '">' . ucwords(str_replace('_', ' ', basename($theme))) . '</option>';
				
				}
		
				?> 	
			</select>
		</div>

		<hr />

		<!-- Content -->
		<h3><?php echo Text::get('shared_content'); ?></h3>

		<?php 
		
		// Used shared variables.
		echo $this->Html->formFields($this->Keys->inAllTemplates(), $data, Text::get('shared_vars_used'), false, false);
		
		// Unused shared variables.
		$unusedDataKeys = array_diff(array_keys($data), $this->Keys->inAllTemplates(), $this->Keys->reserved);
		// Pass the prefix for all IDs related to adding variables according to the IDs defined in 'add_variable.js'.
		echo $this->Html->formFields($unusedDataKeys, $data, Text::get('shared_vars_unused'), true, false, 'automad-add-variable');
		
		?>
	
	<?php	


	// Save buffer to JSON array.
	$output['html'] = ob_get_contents();
	ob_end_clean();
	
	
}


echo json_encode($output);


?>