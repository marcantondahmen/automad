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
 *	Copyright (c) 2014 by Marc Anton Dahmen
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
			
			<div class="form-group">
				<label for="input-data-title"><?php echo ucwords(AM_KEY_TITLE); ?></label>
				<input id="input-data-title" class="form-control input-lg" type="text" name="data[<?php echo AM_KEY_TITLE; ?>]" value="<?php echo str_replace('"', '&quot;', $data[AM_KEY_TITLE]); ?>" onkeypress="return event.keyCode != 13;" placeholder="Required" required />
			</div>
			<div class="form-group">
				<label for="input-data-tags"><?php echo Text::get('page_tags'); ?></label>
				<input id="input-data-tags" class="form-control" type="text" name="data[<?php echo AM_KEY_TAGS; ?>]" value="<?php echo str_replace('"', '&quot;', $data[AM_KEY_TAGS]); ?>" onkeypress="return event.keyCode != 13;" />
			</div>
			
			<hr>
			
			<h3><?php echo Text::get('page_settings'); ?></h3>
			<div class="form-group">	
				<button type="button" data-toggle="modal" data-target="#select-template-modal" class="btn btn-default">
					<?php
					
					if ($data[AM_KEY_THEME]) {
						$theme = $data[AM_KEY_THEME];
					} else {
						$theme = $this->Automad->Shared->get(AM_KEY_THEME);
					}
					
					echo Text::get('page_theme_template');	
					
					// Give feedback in template button whether the template exists or not.	
					if (file_exists(AM_BASE_DIR . AM_DIR_THEMES . '/' . $theme . '/' . $Page->template . '.php')) {
						echo ' <span class="badge">' . ucwords(str_replace('_', ' ', ltrim($data[AM_KEY_THEME] . ' > ', '> ') . $Page->template)) . '</span>';
					} else {
						echo ' <span class="badge off">' . ucwords(str_replace('_', ' ', ltrim($data[AM_KEY_THEME] . ' > ', '> ') . $Page->template)) . ' - ' . Text::get('error_template_missing') . '</span>';
					}
						
					?> 
				</button>
			</div>
			<!-- Select Template Modal -->	
			<div id="select-template-modal" class="modal fade">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-body">
							<?php echo $this->Html->templateSelectBox('theme_template', 'theme_template', $data[AM_KEY_THEME], $Page->template); ?>
						</div>
						<div class="modal-footer">
							<div class="btn-group btn-group-justified">
								<div class="btn-group">
									<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo Text::get('btn_close'); ?></button>
								</div>
								<div class="btn-group">
									<button type="submit" class="btn btn-primary" data-loading-text="<?php echo Text::get('btn_loading'); ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo Text::get('btn_apply_reload'); ?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php if ($Page->path != '/') { ?> 	
			<div class="row">	
				<div class="form-group col-xs-6">
					<label for="input-prefix"><?php echo Text::get('page_prefix'); ?></label>
					<input id="input-prefix" class="form-control" type="text" name="prefix" value="<?php echo $this->Content->extractPrefixFromPath($Page->path); ?>" onkeypress="return event.keyCode != 13;" />
				</div>
				<div class="form-group col-xs-6">
					<label><?php echo Text::get('page_visibility'); ?></label>
					<div class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default<?php if ($hidden) { echo ' active'; } ?>"><?php echo Text::get('btn_hide_page'); ?> 
							<input type="checkbox" name="<?php echo AM_KEY_HIDDEN; ?>"<?php if ($hidden) { echo ' checked'; } ?> />
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="input-redirect"><?php echo Text::get('page_redirect'); ?></label>
				<input id="input-redirect" class="form-control" type="text" name="data[<?php echo AM_KEY_URL; ?>]" value="<?php echo $data[AM_KEY_URL]; ?>" onkeypress="return event.keyCode != 13;" />
			</div>
			<?php } ?> 
			<hr>
			<!-- Vars in template -->
			<?php echo $this->Html->formFields(Text::get('page_vars_in_template'), $this->Keys->inCurrentTemplate(), $data); ?>
			<hr>
			<!-- Vars in other templates -->
			<?php echo $this->Html->formFields(Text::get('page_vars_in_other_templates'), $this->Keys->inOtherTemplates(), $data); ?>
			<hr>
			<!-- Vars in in data but not in any template -->
			<div id="automad-custom-variables">
				<?php 
					$unusedDataKeys = array_diff(array_keys($data), $this->Keys->inAllTemplates(), $this->Keys->reserved);
					echo $this->Html->formFields(Text::get('page_vars_unused'), $unusedDataKeys, $data, true); 
				?>
			</div>
			<br />
			<a class="btn btn-default" href="#" data-toggle="modal" data-target="#automad-add-variable-modal"><span class="glyphicon glyphicon-plus"></span> <?php echo Text::get('btn_add_var'); ?></a>
		
			<hr>
			
			<div class="btn-group btn-group-justified">
				<div class="btn-group"><a class="btn btn-danger" href=""><span class="glyphicon glyphicon-remove"></span> <?php echo Text::get('btn_discard'); ?></a></div>
				<div class="btn-group"><button type="submit" class="btn btn-success" data-loading-text="<?php echo Text::get('btn_loading')?>"><span class="glyphicon glyphicon-ok"></span> <?php echo Text::get('btn_save'); ?></button></div>
			</div>
			
			<!-- Add Variable Modal -->	
			<div id="automad-add-variable-modal" class="modal fade">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h3 class="modal-title"><?php echo Text::get('btn_add_var'); ?></h3>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="automad-add-variable-name"><?php echo Text::get('page_var_name'); ?></label>
								<input type="text" class="form-control" id="automad-add-variable-name" onkeypress="return event.keyCode != 13;" />
							</div>	
						</div>
						<div class="modal-footer">
							<div class="btn-group btn-group-justified">
								<div class="btn-group">
									<button type="button" class="btn btn-default" data-dismiss="modal">
										<span class="glyphicon glyphicon-remove"></span> <?php echo Text::get('btn_close'); ?>
									</button>
								</div>
								<div class="btn-group">
									<button type="button" class="btn btn-primary" id="automad-add-variable-button" data-automad-error-exists="<?php echo Text::get('error_var_exists'); ?>" data-automad-error-name="<?php echo Text::get('error_var_name'); ?>">
										<span class="glyphicon glyphicon-plus"></span> <?php echo Text::get('btn_add_var'); ?>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		<?php	
	
		// Save buffer to JSON array.
		$output['html'] = ob_get_contents();
		ob_end_clean();	
	
	}
		
} else {
	
	$output['error'] = Text::get('error_page_not_found');
	
}

echo json_encode($output);

?>