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
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
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
$url = Core\Request::post('url');

// Verify page's URL - The page must exist in the site's collection.
if ($url && ($Page = $this->getAutomad()->getPage($url))) {

	// If the posted form contains any "data", save the form's data to the page file.
	if ($data = Core\Request::post('data')) {
	
		// Save page and replace $output with the returned $output array (error or redirect).
		$output = $this->getContent()->savePage($url, $data);
	
	} else {
		
		// If only the URL got submitted, 
		// get the page's data from its .txt file and return a form's inner HTML containing these information.
		
		// Get page's data.
		$data = Core\Parse::textFile($this->getContent()->getPageFilePath($Page));

		// Set up all standard variables.
	
		// Create empty array items for all missing standard variables in $data.
		foreach (Keys::$reserved as $key) {
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

		// Check if page is private.
		if (isset($data[AM_KEY_PRIVATE]) && $data[AM_KEY_PRIVATE] && $data[AM_KEY_PRIVATE] != 'false') {
			$private = true;
		} else {
			$private = false;
		} 
		
		// Start buffering the HTML.
		ob_start();
		
		?>
			
			<div class="uk-form-row">
				<label for="am-input-data-title" class="uk-form-label uk-margin-top-remove">
					<?php echo ucwords(AM_KEY_TITLE); ?>
				</label>
				<input 
				id="am-input-data-title" 
				class="am-form-title uk-form-controls uk-form-large uk-width-1-1" 
				type="text" 
				name="data[<?php echo AM_KEY_TITLE; ?>]" 
				value="<?php echo htmlspecialchars($Page->get(AM_KEY_TITLE)); ?>" 
				placeholder="Required" 
				required 
				/>
				<?php if (!AM_HEADLESS_ENABLED) { ?>
					<a 
					href="<?php echo AM_BASE_INDEX . $url; ?>" 
					class="uk-button uk-button-mini uk-margin-small-top uk-text-truncate uk-display-inline-block" 
					title="<?php Text::e('btn_inpage_edit'); ?>" 
					data-uk-tooltip="pos:'bottom'"
					>
						<?php 
						
						if ($url == '/') {
							echo getenv('SERVER_NAME');
						} else {
							echo $url; 	
						}

						?> 
					</a>
				<?php } ?>
			</div>
			<?php 
			
			echo Components\Form\CheckboxPrivate::render(
				'data[' . AM_KEY_PRIVATE . ']', 
				$private); 
			
			?>
			<?php 

			echo Components\Alert\ThemeReadme::render(
				$this->getThemelist()->getThemeByKey($Page->get(AM_KEY_THEME))
			); 

			?>

			<div 
			class="uk-accordion" 
			data-uk-accordion="{duration: 200, showfirst: false, collapse: false}"
			>
				
				<!-- Settings -->
				<div class="uk-accordion-title">
					<?php Text::e('page_settings'); ?>
				</div>
				<div class="uk-accordion-content">
					<?php if (!AM_HEADLESS_ENABLED) { ?>
						<!-- Select Template Modal -->	
						<div id="am-select-template-modal" class="uk-modal">
							<div class="uk-modal-dialog">
								<div class="uk-modal-header">
									<?php Text::e('page_theme_template'); ?>
									<a href="#" class="uk-modal-close uk-close"></a>
								</div>	
								<?php 

								echo Components\Form\SelectTemplate::render(
									$this->getAutomad(),
									$this->getThemelist(),
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
							
							$themeName = '';

							if ($data[AM_KEY_THEME]) {

								$themePath = $data[AM_KEY_THEME];

								if ($Theme = $this->getThemelist()->getThemeByKey($data[AM_KEY_THEME])) {
									$themeName = $Theme->name . ' / ';
								}
								
							} else {
								$themePath = $this->getAutomad()->Shared->get(AM_KEY_THEME);
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
					<?php } ?>
					<!-- Visibility -->
					<?php echo Components\Form\CheckboxHidden::render(
						'data[' . AM_KEY_HIDDEN . ']',
						$hidden
					); ?>
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
						value="<?php echo $this->getContent()->extractPrefixFromPath($Page->path); ?>" 
						/>
					</div>	
					<!-- Redirect -->
					<?php 

					echo Components\Form\Field::render(
						$this->getAutomad(), 
						AM_KEY_URL, 
						$data[AM_KEY_URL],
						false,
						false,
						Text::get('page_redirect')
					); 

					?>
					<?php } ?> 
					<!-- Date -->
					<?php 

					echo Components\Form\Field::render(
						$this->getAutomad(), 
						AM_KEY_DATE, 
						$Page->get(AM_KEY_DATE)
					); 

					?>
					<!-- Tags -->
					<div class="uk-form-row">	
						<?php 	
						
						$tags = Core\Parse::csv(htmlspecialchars($data[AM_KEY_TAGS]));
						sort($tags);
						
						$Pagelist = $this->getAutomad()->getPagelist();
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
				
				<?php 
			
				if (!AM_HEADLESS_ENABLED) {
					$keys = Keys::inCurrentTemplate($Page, $this->getThemelist()->getThemeByKey($Page->get(AM_KEY_THEME)));
				} else {
					$keys = Keys::inTemplate(Headless::getTemplate());
				}
				
				$textKeys = Keys::filterTextKeys($keys);
				$settingKeys = Keys::filterSettingKeys($keys);
				$unusedDataKeys = array_diff(array_keys($data), $keys, Keys::$reserved);
			
				?>

				<?php if (!empty($textKeys)) { ?>
					<!-- Text vars -->
					<div class="uk-accordion-title">
						<?php Text::e('page_vars_content'); ?> &mdash;
						<?php echo count($textKeys); ?>
					</div>
					<div class="uk-accordion-content">
						<?php 

						echo Components\Form\Group::render(
							$this->getAutomad(),
							$textKeys, 
							$data, 
							false, 
							$this->getThemelist()->getThemeByKey($Page->get(AM_KEY_THEME))
						); 

						?>
					</div>
				<?php } ?>

				<?php if (!empty($settingKeys)) { ?>
					<!-- Setting vars -->
					<div class="uk-accordion-title">
						<?php Text::e('page_vars_settings'); ?> &mdash;
						<?php echo count($settingKeys); ?>
					</div>
					<div class="uk-accordion-content">
						<?php

						echo Components\Form\Group::render(
							$this->getAutomad(),
							$settingKeys, 
							$data, 
							false, 
							$this->getThemelist()->getThemeByKey($Page->get(AM_KEY_THEME))
						); 

						?>
					</div>
				<?php } ?>
				
				<!-- Vars in data but not in any template -->
				<div class="uk-accordion-title">
					<?php Text::e('page_vars_unused'); ?> &mdash;
					<span data-am-count="#am-add-variable-container .uk-form-row"></span>	
				</div>
				<div class="uk-accordion-content">
					<?php 

					// Pass the prefix for all IDs related to adding variables according to the IDs defined in 'add_variable.js'.
					echo Components\Form\Group::render(
						$this->getAutomad(), 
						$unusedDataKeys, 
						$data, 
						'am-add-variable'
					); 
					
					?>
				</div>

			</div>	
				
		<?php	
	
		// Save buffer to JSON array.
		$output['html'] = ob_get_contents();
		ob_end_clean();	
	
	}
		
} 

$this->jsonOutput($output);

?>