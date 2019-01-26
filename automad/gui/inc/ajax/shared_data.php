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
	
	// Get themes.
	$themes = $this->Themelist->getThemes();
	$mainTheme = $this->Themelist->getThemeByKey($this->Automad->Shared->get(AM_KEY_THEME));
	
	// Start buffering the HTML.
	ob_start();
	
	
	?>
	
		<div class="uk-form-row">
			<label for="am-input-data-sitename" class="uk-form-label uk-margin-top-remove">
				<?php echo ucwords(AM_KEY_SITENAME); ?>
			</label>
			<input 
			id="am-input-data-sitename" 
			class="uk-form-controls uk-form-large uk-width-1-1" 
			type="text" 
			name="data[<?php echo AM_KEY_SITENAME; ?>]" 
			value="<?php echo htmlspecialchars($data[AM_KEY_SITENAME]); ?>" 
			/>
		</div>
				
		<div 
		class="uk-accordion" 
		data-uk-accordion="{duration: 200, showfirst: false, collapse: false}"
		>
			<!-- Theme -->
			<div class="uk-accordion-title">
				<?php Text::e('shared_theme'); ?>
			</div>
			<div class="uk-accordion-content">
				<div id="am-apply-theme-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						<?php Text::e('shared_theme_apply'); ?>
						<div class="uk-modal-footer uk-text-right">
							<button 
							class="uk-modal-close uk-button"
							>
								<i class="uk-icon-close"></i>&nbsp;
								<?php Text::e('btn_close'); ?>
							</button>
							<button 
							class="uk-modal-close uk-button uk-button-success" 
							type="button" 
							data-am-submit="shared_data"
							>
								<i class="uk-icon-check"></i>&nbsp;
								<?php Text::e('btn_apply_reload'); ?>
							</button>
						</div>
					</div>
				</div>			
				<ul 
				class="uk-grid uk-grid-match uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4 uk-margin-top" 
				data-uk-grid-match="{target:'.uk-panel'}" 
				data-uk-grid-margin
				>
					<?php 
					
					Core\Debug::ajax($output, 'themes', $themes);
					$i = 0;
					
					// Select the first theme is the main theme is missing to avoid
					// sending the form without a valid theme.
					if (!$mainTheme) {
						$defaultTheme = reset($themes);
						$data[AM_KEY_THEME] = $defaultTheme->path;
					}
					
					foreach ($themes as $Theme) { 
					
						$path = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $Theme->path;
						$files = glob($path . '/*');
						$id = 'am-theme-' . ++$i;
					
						// Set icon.
						if ($images = preg_grep('/\.(jpg|jpeg|png|gif$)/i', $files)) {
							$img = new Core\Image(reset($images), 320, 240, true);
							$icon = '<img src="' . AM_BASE_URL . $img->file . '" width="' . $img->width . '" height="' . $img->height . '" />';
						} else {
							$icon = '<div class="am-panel-icon"><span><i class="uk-icon-code"></i></span></div>';
						}
				
						// Check currently active theme.
						if ($Theme->path === $data[AM_KEY_THEME]) {
							$attrChecked = ' checked';
						} else {
							$attrChecked = '';
						}
				
					?>
					<li>
						<?php if ($Theme->readme) { ?>
						<div id="<?php echo $id . '-modal' ?>" class="uk-modal">
							<div class="am-text-readme uk-modal-dialog uk-modal-dialog-large">
								<div class="uk-modal-header">
									Readme
									<a href="#" class="uk-modal-close uk-close"></a>
								</div>
								<?php echo Core\Str::markdown(file_get_contents($Theme->readme)); ?>
								<div class="uk-modal-footer uk-text-right">
									<button 
									class="uk-modal-close uk-button"
									>
										<i class="uk-icon-close"></i>&nbsp;
										<?php Text::e('btn_close'); ?>
									</button>
								</div>
							</div>
						</div>
						<?php } ?>				
						<div id="<?php echo $id; ?>" class="uk-panel uk-panel-box">
							<div class="uk-panel-teaser">
								<?php echo $icon; ?>
							</div>
							<?php if ($Theme->version) { ?> 
							<div class="uk-panel-badge uk-badge">
								<?php echo $Theme->version; ?>
							</div>	
							<?php } ?>
							<div class="uk-panel-title">
								<?php echo $Theme->name; ?>
							</div>
							<div class="uk-text-small uk-text-muted uk-hidden-small">
								<?php echo $Theme->description; ?>
							</div>
							<?php if ($Theme->author) { ?> 
							<div class="uk-text-small uk-text-muted uk-hidden-small">
								<i class="uk-icon-copyright uk-icon-justify"></i>&nbsp;
								<?php echo $Theme->author; ?>
							</div>
							<?php } ?>
							<?php if ($Theme->license) { ?>
							<div class="uk-text-small uk-text-muted uk-hidden-small">
								<i class="uk-icon-balance-scale uk-icon-justify"></i>&nbsp;
								<?php echo $Theme->license; ?>
							</div>
							<?php } ?>
							<div class="am-panel-bottom">
								<?php if ($Theme->readme) { ?>
								<a 
								href="#<?php echo $id . '-modal' ?>"
								class="uk-icon-button uk-icon-align-left"
								title="<?php Text::e('btn_readme'); ?>"
								data-uk-tooltip
								data-uk-modal
								>
								</a>
								<?php } ?>
								<label 
								class="am-toggle-checkbox am-panel-bottom-right" 
								data-am-toggle="#<?php echo $id; ?>"
								>
									<input 
									type="radio" 
									name="data[<?php echo AM_KEY_THEME; ?>]" 
									value="<?php echo $Theme->path; ?>" 
									data-am-modal-on-change="#am-apply-theme-modal"
									<?php echo $attrChecked; ?> 
									/>
								</label>
							</div>
						</div>
					</li>		
					<?php } ?> 	
				</ul>	
			</div>
			
			<!-- Variables -->
			<?php 
			
				if ($mainTheme) {
					$keysInMainTheme = $this->Keys->inTheme($mainTheme);
				} else {
					$keysInMainTheme = array();
				}
				
				$keysInOtherThemes = array_diff(
					$this->Keys->inAllTemplates(), 
					$keysInMainTheme
				); 
				
			?>
			<!-- Shared variables in main theme -->
			<?php if ($keysInMainTheme) { ?>
			<div class="uk-accordion-title">
				<?php Text::e('shared_vars_main_theme'); ?>&nbsp;
				<span class="uk-badge"><?php echo count($keysInMainTheme); ?></span>
			</div>
			<div class="uk-accordion-content">
				<?php 
					echo $this->Html->formGroup(
						$keysInMainTheme, 
						$data,
						false,
						$mainTheme
					); 
				?>
			</div>
			<?php } ?>
			<!-- Shared variables in other themes -->
			<div class="uk-accordion-title">
				<?php Text::e('shared_vars_other_themes'); ?>&nbsp;
				<span class="uk-badge"><?php echo count($keysInOtherThemes); ?></span>
			</div>
			<div class="uk-accordion-content">
				<?php 
					echo $this->Html->formGroup(
						$keysInOtherThemes, 
						$data
					); 
				?>
			</div>
			<!-- Unused shared variables -->
			<div class="uk-accordion-title">
				<?php Text::e('shared_vars_unused'); ?>&nbsp;
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


echo json_encode($output);


?>