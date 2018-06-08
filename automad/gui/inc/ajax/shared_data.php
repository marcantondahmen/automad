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
	
	// Start buffering the HTML.
	ob_start();
	
	
	?>
	
		<div class="uk-form-row uk-margin-bottom">
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

		<!-- Content -->		
		<div 
		class="uk-accordion" 
		data-uk-accordion="{duration: 200, showfirst: false, collapse: false}"
		>
			
			<!-- Theme -->
			<div type="button" class="uk-accordion-title">
				<?php Text::e('shared_theme'); ?>
			</div>
			<div class="uk-accordion-content">
				<ul 
				class="uk-grid uk-grid-match uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4 uk-margin-top" 
				data-uk-grid-match="{target:'.uk-panel'}" 
				data-uk-grid-margin
				>
					<?php 
					
					$themes = $this->Themelist->getThemes();
					Core\Debug::ajax($output, 'themes', $themes);
					$i = 0;
					
					foreach ($themes as $theme) { 
					
						$path = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $theme->path;
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
						if ($theme->path === $data[AM_KEY_THEME]) {
							$attrChecked = ' checked';
						} else {
							$attrChecked = '';
						}
				
					?>
					<li>
						<div id="<?php echo $id; ?>" class="uk-panel uk-panel-box">
							<div class="uk-panel-teaser">
								<?php echo $icon; ?>
							</div>
							<?php if ($theme->version) { ?> 
							<div class="uk-panel-badge uk-badge">
								<?php echo $theme->version; ?>
							</div>	
							<?php } ?>
							<div class="uk-panel-title">
								<?php echo $theme->name; ?>
							</div>
							<div class="uk-text-small uk-text-muted uk-hidden-small">
								<?php echo $theme->description; ?>
							</div>
							<?php if ($theme->author) { ?> 
							<div class="uk-text-small uk-text-muted uk-hidden-small">
								<i class="uk-icon-copyright uk-icon-justify"></i>&nbsp;
								<?php echo $theme->author; ?>
							</div>
							<?php } ?>
							<?php if ($theme->license) { ?>
							<div class="uk-text-small uk-text-muted uk-hidden-small">
								<i class="uk-icon-balance-scale uk-icon-justify"></i>&nbsp;
								<?php echo $theme->license; ?>
							</div>
							<?php } ?>
							<div class="am-panel-bottom am-panel-bottom-small">
								<label 
								class="am-toggle-checkbox am-panel-bottom-right" 
								data-am-toggle="#<?php echo $id; ?>"
								>
									<input 
									type="radio" 
									name="data[<?php echo AM_KEY_THEME; ?>]" 
									value="<?php echo $theme->path; ?>" 
									<?php echo $attrChecked; ?> 
									/>
								</label>
							</div>
						</div>
					</li>		
					<?php } ?> 	
				</ul>
			</div>
			<!-- Used shared variables -->
			<?php $keysInAllTemplates = $this->Keys->inAllTemplates(); ?>
			<div type="button" class="uk-accordion-title">
				<?php Text::e('shared_vars_used'); ?>&nbsp;
				<span class="uk-badge"><?php echo count($keysInAllTemplates); ?></span>
			</div>
			<div class="uk-accordion-content">
				<?php 
					echo $this->Html->formGroup(
						$keysInAllTemplates, 
						$data,
						false,
						$this->Themelist->getThemeByKey($this->Automad->Shared->get(AM_KEY_THEME))
					); 
				?>
			</div>
			<!-- Unused shared variables -->
			<div type="button" class="uk-accordion-title">
				<?php Text::e('shared_vars_unused'); ?>
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