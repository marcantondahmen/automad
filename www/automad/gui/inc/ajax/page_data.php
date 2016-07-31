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
 * 	All ajax requests regarding a page's data file get processed here.
 *	Basically that means "Saving, Renaming & Redirecting" as the first option and "Loading" as the second option.
 *
 *	When "$_POST['data']" exists, that means, that a form with "edited" page information got submitted and the data gets processed to be written into the data file.
 *	In that case, this handler either returns a redirect URL (for reloading the page, in case it got renamed) or an error message. NO (!) form data is submitted back, since
 *	The form already exists on the "client side".
 *
 *	When only "$_POST['url']" got submitted, that means, the the form on the "client side" is still empty and therefore it must be an initial page loading request, which then will return 
 *	the page's data as the form HTML.
 *
 *	NOTE: Only the inner elements of the form are returned. To keep the outer form information lose form the processing here, there must be an outer form existing on the page to wrap that HTML output.	
 */


// Array for returned JSON data.
$output = array();


// Verify page's URL - The URL must exist in the site's collection.
if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->collection)) {

	// The currently edited page.
	$url = $_POST['url'];
	$Page = $this->collection[$url];
	
	// If the posted form contains any "data", save the form's data to the page file.
	if (isset($_POST['data'])) {
	
		// Save page and replace $output with the returned $output array (error or redirect).
		$output = $this->Content->savePage($url, $_POST['data']);
	
	} else {
		
		// If only the URL got submitted, 
		// get the page's data from its .txt file and return a form's inner HTML containing these information.
		
		// Get page's data.
		$data = Core\Parse::textFile($this->Content->getPageFilePath($Page));

		// Set up all standard variables.
	
		// Create empty array items for all missing standard variables in $data.
		foreach ($this->Keys->reserved as $key) {
			if (!isset($data[$key])) {
				$data[$key] = false;
			}
		}

		// Set title, in case the variable is not set (when editing the text file in an editor and the title wasn't set correctly)
		if (!$data[AM_KEY_TITLE]) {
			$data[AM_KEY_TITLE] = basename($Page->url);
		}
		
		// Check if page is hidden.
		if (isset($data[AM_KEY_HIDDEN]) && $data[AM_KEY_HIDDEN] && $data[AM_KEY_HIDDEN] != 'false') {
			$hidden = true;
		} else {
			$hidden = false;
		} 
		
		// Start buffering the HTML.
		ob_start();
		
		?>
			
			<div class="uk-form-row">
				<label for="automad-input-data-title" class="uk-form-label"><?php echo ucwords(AM_KEY_TITLE); ?></label>
				<input id="automad-input-data-title" class="uk-form-controls uk-form-large uk-width-1-1" type="text" name="data[<?php echo AM_KEY_TITLE; ?>]" value="<?php echo htmlspecialchars($data[AM_KEY_TITLE]); ?>" placeholder="Required" required />
			</div>
			<div class="uk-form-row">
				<label for="automad-input-data-tags" class="uk-form-label"><?php echo Text::get('page_tags'); ?></label>
				<input id="automad-input-data-tags" class="uk-form-controls uk-width-1-1" type="text" name="data[<?php echo AM_KEY_TAGS; ?>]" value="<?php echo htmlspecialchars($data[AM_KEY_TAGS]); ?>" />
			</div>
			
			<hr />
			
			<h3><?php echo Text::get('page_settings'); ?></h3>	
			<!-- Select Template Modal -->	
			<div id="automad-select-template-modal" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						<h3><?php echo Text::get('page_theme_template'); ?></h3>
					</div>	
					<?php echo $this->Html->templateSelectBox('theme_template', $data[AM_KEY_THEME], $Page->template); ?>	
					<div class="uk-modal-footer uk-text-right">
						<button class="uk-modal-close uk-button">
							<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php echo Text::get('btn_close'); ?>
						</button>
						<button class="uk-button uk-button-primary" type="button" data-automad-submit="page_data">
							<i class="uk-icon-check"></i>&nbsp;&nbsp;<?php echo Text::get('btn_apply_reload'); ?>
						</button>
					</div>
				</div>
			</div>
			<!-- Select Template Button -->	
			<div class="uk-form-row">
				<a href="#automad-select-template-modal" type="button" data-uk-modal class="uk-button uk-button-large uk-width-1-1">
					<?php
					
					if ($data[AM_KEY_THEME]) {
						$theme = $data[AM_KEY_THEME];
					} else {
						$theme = $this->Automad->Shared->get(AM_KEY_THEME);
					}
					
					echo Text::get('page_theme_template');	
					
					// Give feedback in template button whether the template exists or not.	
					if (file_exists(AM_BASE_DIR . AM_DIR_THEMES . '/' . $theme . '/' . $Page->template . '.php')) {
						echo ' <span class="uk-badge uk-badge-notification">' . ucwords(str_replace('_', ' ', ltrim($data[AM_KEY_THEME] . ' > ', '> ') . $Page->template)) . '</span>';
					} else {
						echo ' <span class="uk-badge uk-badge-notification uk-badge-danger">' . ucwords(str_replace('_', ' ', ltrim($data[AM_KEY_THEME] . ' > ', '> ') . $Page->template)) . ' - ' . Text::get('error_template_missing') . '</span>';
					}
						
					?> 
				</a>
			</div>
			<?php if ($Page->path != '/') { ?> 
			<div class="uk-form-row">	
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1 uk-width-medium-1-2">
						<label for="automad-input-prefix" class="uk-form-label uk-text-truncate"><?php echo Text::get('page_prefix'); ?></label>
						<input id="automad-input-prefix" class="uk-form-controls uk-width-1-1" type="text" name="prefix" value="<?php echo $this->Content->extractPrefixFromPath($Page->path); ?>" />
					</div>
					<div class="uk-width-1-1 uk-width-medium-1-2">
						<label for="automad-checkbox-hidden" class="uk-form-label"><?php echo Text::get('page_visibility'); ?></label>
						<label class="uk-button" data-automad-toggle>
							<?php echo Text::get('btn_hide_page'); ?>
							<input id="automad-checkbox-hidden" type="checkbox" name="<?php echo AM_KEY_HIDDEN; ?>"<?php if ($hidden) { echo ' checked'; } ?> />
						</label>
					</div>
				</div>
			</div>
			<div class="uk-form-row">
				<label for="automad-input-redirect" class="uk-form-label"><?php echo Text::get('page_redirect'); ?></label>
				<input id="automad-input-redirect" class="uk-form-controls uk-width-1-1" type="text" name="data[<?php echo AM_KEY_URL; ?>]" value="<?php echo htmlspecialchars($data[AM_KEY_URL]); ?>" />
			</div>
			<?php } ?> 
			
			<hr />
			
			<!-- Content -->
			<h3><?php echo Text::get('page_content'); ?></h3>
			
			<?php 
			
			// Vars in selected template.
			echo 	$this->Html->formGroup(
					$this->Keys->inCurrentTemplate(), 
					$data, 
					Text::get('page_vars_in_template'), 
					true
				); 
			
			// Vars in other templates.
			echo 	$this->Html->formGroup(
					$this->Keys->inOtherTemplates(), 
					$data, 
					Text::get('page_vars_in_other_templates')
				); 
					
			// Vars in data but not in any template	
			$unusedDataKeys = array_diff(array_keys($data), $this->Keys->inAllTemplates(), $this->Keys->reserved);
			// Pass the prefix for all IDs related to adding variables according to the IDs defined in 'add_variable.js'.
			echo 	$this->Html->formGroup(
					$unusedDataKeys, 
					$data, 
					Text::get('page_vars_unused'), 
					false, 
					'automad-add-variable'
				); 
					
			?>
				
		<?php	
	
		// Save buffer to JSON array.
		$output['html'] = ob_get_contents();
		ob_end_clean();	
	
	}
		
} 

echo json_encode($output);

?>