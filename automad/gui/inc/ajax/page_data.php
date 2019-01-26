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


// Verify page's URL - The page must exist in the site's collection.
if (isset($_POST['url']) && ($Page = $this->Automad->getPage($_POST['url']))) {

	// The URL of the currently edited page.
	$url = $_POST['url'];
	
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
	
		// Check if page is hidden.
		if (isset($data[AM_KEY_HIDDEN]) && $data[AM_KEY_HIDDEN] && $data[AM_KEY_HIDDEN] != 'false') {
			$hidden = true;
		} else {
			$hidden = false;
		} 
		
		// Start buffering the HTML.
		ob_start();
		
		?>
			
			<div class="uk-form-row uk-margin-large-bottom">
				<label for="am-input-data-title" class="uk-form-label uk-margin-top-remove">
					<?php echo ucwords(AM_KEY_TITLE); ?>
				</label>
				<input 
				id="am-input-data-title" 
				class="uk-form-controls uk-form-large uk-width-1-1" 
				type="text" 
				name="data[<?php echo AM_KEY_TITLE; ?>]" 
				value="<?php echo htmlspecialchars($Page->get(AM_KEY_TITLE)); ?>" 
				placeholder="Required" 
				required 
				/>
				<a 
				href="<?php echo AM_BASE_INDEX . $url; ?>" 
				class="uk-button uk-button-small uk-margin-small-top uk-text-truncate uk-display-inline-block" 
				title="<?php Text::e('btn_inpage_edit'); ?>" 
				data-uk-tooltip="pos:'bottom-left'"
				>
					<i class="uk-icon-share"></i>&nbsp;
					<?php 
					
					if ($url == '/') {
						echo getenv('SERVER_NAME');
					} else {
						echo $url; 	
					}

					?> 
				</a>
			</div>
			
			<div 
			class="uk-accordion" 
			data-uk-accordion="{duration: 200, showfirst: false, collapse: false}"
			>
				
				<!-- Settings -->
				<div class="uk-accordion-title">
					<?php Text::e('page_settings'); ?>
				</div>
				<div class="uk-accordion-content">
					<!-- Select Template Modal -->	
					<div id="am-select-template-modal" class="uk-modal">
						<div class="uk-modal-dialog">
							<div class="uk-modal-header">
								<?php Text::e('page_theme_template'); ?>
								<a href="#" class="uk-modal-close uk-close"></a>
							</div>	
							<?php 
								echo 	$this->Html->selectTemplate(
											$this->Themelist,
											'theme_template', 
											$data[AM_KEY_THEME], 
											$Page->template
										); 
							?>	
							<div class="uk-modal-footer uk-text-right">
								<button class="uk-modal-close uk-button">
									<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php Text::e('btn_close'); ?>
								</button>
								<button class="uk-modal-close uk-button uk-button-success" type="button" data-am-submit="page_data">
									<i class="uk-icon-check"></i>&nbsp;&nbsp;<?php Text::e('btn_apply_reload'); ?>
								</button>
							</div>
						</div>
					</div>
					<!-- Select Template Button -->	
					<div class="uk-form-row">
						<label class="uk-form-label uk-text-truncate">
							<?php Text::e('page_theme_template'); ?>
						</label>
						<?php 
						
						if ($data[AM_KEY_THEME]) {
							$themePath = $data[AM_KEY_THEME];
							$Theme = $this->Themelist->getThemeByKey($data[AM_KEY_THEME]);
							$themeName = $Theme->name . ' / ';
						} else {
							$themePath = $this->Automad->Shared->get(AM_KEY_THEME);
							$themeName = '';
						}
						
						$template = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $themePath . '/' . $Page->template . '.php';
						$templateName = $themeName . ucwords(str_replace('_', ' ', $Page->template));
						
						if (file_exists($template)) {
							$templateButtonClass = 'uk-button-success';
						} else {
							$templateButtonClass = 'uk-button-danger';
							$templateName .= ' - ' . Text::get('error_template_missing');
						}
						
						?>
						<button 
						type="button" 
						class="uk-button <?php echo $templateButtonClass; ?> uk-button-large uk-width-1-1" 
						data-uk-modal="{target:'#am-select-template-modal'}"
						>
							<div class="uk-flex uk-flex-space-between">
								<div class="uk-text-truncate uk-text-left">
									<?php echo $templateName;?> 
								</div>
								<div class="uk-hidden-small">
									<i class="uk-icon-pencil"></i>
								</div>
							</div>
						</button>	
					</div>
					<!-- Visibility -->
					<div class="uk-form-row">
						<label class="uk-form-label uk-text-truncate">
							<?php Text::e('page_visibility'); ?>
						</label>
						<label 
						class="am-toggle-switch uk-button" 
						data-am-toggle
						>
							<?php Text::e('btn_hide_page'); ?>
							<input 
							id="am-checkbox-hidden" 
							type="checkbox" 
							name="<?php echo AM_KEY_HIDDEN; ?>"<?php if ($hidden) { echo ' checked'; } ?> 
							/>
						</label>
					</div>	
					<?php if ($Page->path != '/') { ?>
					<!-- Prefix -->
					<div class="uk-form-row">
						<label for="am-input-prefix" class="uk-form-label uk-text-truncate">
							<?php Text::e('page_prefix'); ?>
						</label>
						<input 
						id="am-input-prefix" 
						class="uk-form-controls uk-width-1-1" 
						type="text" 
						name="prefix" 
						value="<?php echo $this->Content->extractPrefixFromPath($Page->path); ?>" 
						/>
					</div>	
					<!-- Redirect -->
					<div class="uk-form-row">
						<label for="am-input-redirect" class="uk-form-label">
							<?php Text::e('page_redirect'); ?>
						</label>
						<input 
						id="am-input-redirect" 
						class="uk-form-controls uk-width-1-1" 
						type="text" 
						name="data[<?php echo AM_KEY_URL; ?>]" 
						value="<?php echo htmlspecialchars($data[AM_KEY_URL]); ?>" 
						/>
					</div>
					<?php } ?> 
					<!-- Tags -->
					<div class="uk-form-row">	
						<?php 	
						
							$tags = Core\Parse::csv(htmlspecialchars($data[AM_KEY_TAGS]));
							sort($tags);
							
							$Pagelist = $this->Automad->getPagelist();
							$Pagelist->config(
								array_merge(
									$Pagelist->getDefaults(),
									array('excludeHidden' => false)
								)
							);
							
							$allTags = $Pagelist->getTags();
							sort($allTags);
							
							$allTagsAutocomplete = array();
							
							foreach ($allTags as $tag) {
								$allTagsAutocomplete[]['value'] = $tag;
							}
							
						?>
						<label class="uk-form-label">
							<?php Text::e('page_tags'); ?>
						</label>
						<div 
						id="am-taggle" 
						data-am-tags='{
							"tags": <?php echo json_encode($tags); ?>,
							"autocomplete": <?php echo json_encode($allTagsAutocomplete); ?>
						}'
						></div>
						<input  
						id="am-input-data-tags"
						type="hidden" 
						name="data[<?php echo AM_KEY_TAGS; ?>]" 
						value="" 
						/>
					</div>	
				</div>
				
				<?php if ($keysInCurrentTemplate = $this->Keys->inCurrentTemplate()) { ?>
				<!-- Vars in selected template -->
				<div class="uk-accordion-title">
					<?php Text::e('page_vars_in_template'); ?>&nbsp;
					<span class="uk-badge"><?php echo count($keysInCurrentTemplate); ?></span>
				</div>
				<div class="uk-accordion-content">
					<?php 
						echo 	$this->Html->formGroup(
									$keysInCurrentTemplate, 
									$data, 
									false, 
									$this->Themelist->getThemeByKey($Page->get(AM_KEY_THEME))
								); 
					?>
				</div>
				<?php } ?>
				
				<!-- Vars in other templates -->
				<?php $keysInOtherTemplates = $this->Keys->inOtherTemplates(); ?>
				<div class="uk-accordion-title">
					<?php Text::e('page_vars_in_other_templates'); ?>&nbsp;
					<span class="uk-badge"><?php echo count($keysInOtherTemplates); ?></span>
				</div>
				<div class="uk-accordion-content">
					<?php echo $this->Html->formGroup($keysInOtherTemplates, $data); ?>
				</div>
				
				<!-- Vars in data but not in any template -->
				<div class="uk-accordion-title">
					<?php Text::e('page_vars_unused'); ?>&nbsp;
					<span class="uk-badge" data-am-count="#am-add-variable-container .uk-form-row"></span>	
				</div>
				<div class="uk-accordion-content">
					<?php 
					
					$unusedDataKeys = array_diff(array_keys($data), $this->Keys->inAllTemplates(), $this->Keys->reserved);
					// Pass the prefix for all IDs related to adding variables according to the IDs defined in 'add_variable.js'.
					echo $this->Html->formGroup($unusedDataKeys, $data, 'am-add-variable'); 
					
					?>
				</div>
				
			</div>	
				
		<?php	
	
		// Save buffer to JSON array.
		$output['html'] = ob_get_contents();
		ob_end_clean();	
	
	}
		
} 

echo json_encode($output);

?>