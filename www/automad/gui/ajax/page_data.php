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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
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
$output['debug'] = $_POST;


// Verify page's URL - The URL must exist in the site's collection.
if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->collection)) {

	
	$url = $_POST['url'];
	
	
	// The currently edited page.
	$P = $this->collection[$url];
	
	
	// If the posted form contains any "data", save the form's data to the page file.
	if (isset($_POST['data'])) {
	
	
		$data = $_POST['data'];
	
	
		// A title is required for building the page's path.
		// If there is no title provided, an error will be returned instead of saving and moving the page.
		if ($data['title']) {
			
		
			// Check if the parent directory is writable.
			if (is_writable(dirname(dirname($this->pageFile($P))))) {
	
			
				// Check if the page's file and the page's directory is writable.
				if (is_writable($this->pageFile($P)) && is_writable(dirname($this->pageFile($P)))) {
			
	
					// Remove empty data.
					// Needs to be done here, to be able to simply test for empty title field.
					$data = array_filter($data);
		
		
					// Set hidden parameter within the $data array. 
					// Since it is a checkbox, it must get parsed separately.
					if (isset($_POST['hidden'])) {
						$data['hidden'] = 1;
					}
	
	
					// The theme and the template get passed as theme/template.php combination separate form $_POST['data']. 
					// That information has to be parsed first and "subdivided".

					// Get correct theme name.
					// If the theme is not set and there is no slash passed within 'theme_template', the resulting dirname is just a dot.
					// In that case, $data['theme'] gets removed (no theme - use site theme). 
					if (dirname($_POST['theme_template']) != '.') {
						$data['theme'] = dirname($_POST['theme_template']);
					} else {
						unset($data['theme']);
					}
	
	
					// Build file content to be written to the txt file.
					$pairs = array();

					foreach ($data as $key => $value) {
						$pairs[] = $key . AM_PARSE_PAIR_SEPARATOR . ' ' . $value;
					}

					$content = implode("\r\n\r\n" . AM_PARSE_BLOCK_SEPARATOR . "\r\n\r\n", $pairs);
	

					// Delete old (current) file, in case, the template has changed.
					unlink($this->pageFile($P));


					// Build the path of the data file by appending the basename of 'theme_template' to $page->path.
					$newPageFile = AM_BASE_DIR . AM_DIR_PAGES . $P->path . str_replace('.php', '', basename($_POST['theme_template'])) . '.' . AM_FILE_EXT_DATA;
	
	
					// Save new file within current directory, even when the prefix/title changed. 
					// Renaming/moving is done in a later step, to keep files and subpages bundled to the current text file.
					$old = umask(0);
					file_put_contents($newPageFile, $content);
					umask($old);
	
	
					// If the page is not the homepage, 
					// rename the page's directory including all children and all files, after saving according to the (new) title and prefix.
					// $this->movePage() will check if renaming is needed, and will skip moving, when old and new path are equal.
					if ($url != '/') {
	
						if (!isset($_POST['prefix'])) {
							$prefix = '';
						} else {
							$prefix = $_POST['prefix'];
						}

						$newPagePath = $this->movePage($P->path, dirname($P->path), $prefix, $data['title']);
	
					} else {
			
						// In case the page is the home page, the path is just '/'.
						$newPagePath = '/';
			
					}
	

					// Clear the cache to make sure, the changes get reflected on the website directly.
					$C = new Cache();
					$C->clear();
	

					// Rebuild Site object, since the file structure might be different now.
					$S = new Site(false);
					$collection = $S->getCollection();

	
					// Find new URL.
					foreach ($collection as $key => $page) {
		
						if ($page->path == $newPagePath) {
				
							// Just return a redirect URL (might be the old URL), to also reflect the possible renaming in all the GUI's navigation.
							$output['redirect'] = '?context=edit_page&url=' . urlencode($key);
							break;
				
						}
		
					}
	
	
				} else {
					
					$output['error'] = $this->tb['error_permission'] . '<p>' . dirname($this->pageFile($P)) . '</p>';
					
				}
	
	
			} else {
				
				$output['error'] = $this->tb['error_permission'] . '<p>' . dirname(dirname($this->pageFile($P))) . '</p>';
				
			}
	
	
		} else {
		
			// If the title is missing, just return an error.
			$output['error'] = $this->tb['error_page_title'];
		
		}
	
	
	} else {
		
		
		// If only the URL got submitted, 
		// get the page's data from its .txt file and return a form's inner HTML containing these information.
		
		// Get page's data.
		$data = Parse::textFile($this->pageFile($P));


		// Set up all standard variables.
	
		// These keys are always part of the form and have to be normalized/created.
		$standardKeys = array(AM_KEY_TITLE, AM_KEY_TAGS, AM_KEY_THEME, AM_KEY_URL, AM_KEY_HIDDEN);

		// Create empty array items for all missing standard variables in $data.
		foreach ($standardKeys as $key) {
			if (!isset($data[$key])) {
				$data[$key] = false;
			}
		}

		// Set title, in case the variable is not set (when editing the text file in an editor and the title wasn't set correctly)
		if (!$data[AM_KEY_TITLE]) {
			$data[AM_KEY_TITLE] = basename($P->url);
		}
		
		// Check if page is hidden.
		if (isset($data[AM_KEY_HIDDEN]) && $data[AM_KEY_HIDDEN] && $data[AM_KEY_HIDDEN] != 'false') {
			$hidden = true;
		} else {
			$hidden = false;
		} 
	
	
		// Get variable keys from selected template file, which are not part of the $standardKeys.
		// If one of these keys is not a key in $data, its textarea gets automatically created in the form, to make it easier for the user to understand, 
		// what variables are available without having to add them manually. (below)
		$templateKeys = array_diff($this->getTemplateVars($data[AM_KEY_THEME], $P->template), $standardKeys);
	
	
		// Start buffering the HTML.
		ob_start();
		
		?>
			
			<div class="list-group">
			
				<div class="list-group-item">
					
					<button type="button" data-toggle="modal" data-target="#select-template-modal" class="btn btn-default btn-lg">
						<?php echo $this->tb['page_theme_template']; ?> <span class="badge"><?php echo ucwords(ltrim($data[AM_KEY_THEME] . ' > ', '> ') . $P->template); ?></span> 
					</button>
					
					<!-- Select Template Modal -->	
					<div id="select-template-modal" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-body">
									<?php echo $this->templateSelectBox('theme_template', 'theme_template', $data[AM_KEY_THEME], $P->template); ?>
								</div>
								<div class="modal-footer">
									<div class="btn-group">
										<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button>
										<button type="submit" class="btn btn-primary" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $this->tb['btn_apply_reload']; ?></button>
									</div>
								</div>
							</div>
						</div>
					</div>
					
				</div>
			
				<div class="list-group-item">
					<div class="form-group">
						<label for="input-data-title" class="text-muted"><span class="glyphicon glyphicon-exclamation-sign"></span> <?php echo ucwords(AM_KEY_TITLE); ?></label>
						<input id="input-data-title" class="form-control input-lg" type="text" name="data[<?php echo AM_KEY_TITLE; ?>]" value="<?php echo $data[AM_KEY_TITLE]; ?>" onkeypress="return event.keyCode != 13;" placeholder="Required" required />
					</div>
					<div class="form-group">
						<label for="input-data-tags" class="text-muted"><span class="glyphicon glyphicon-tags"></span> <?php echo $this->tb['page_tags']; ?></label>
						<input id="input-data-tags" class="form-control" type="text" name="data[<?php echo AM_KEY_TAGS; ?>]" value="<?php echo $data[AM_KEY_TAGS]; ?>" onkeypress="return event.keyCode != 13;" />
					</div>
				</div>	
			
				<?php if ($P->path != '/') { ?> 
				<div class="list-group-item">
					<h4 class="text-muted"><?php echo $this->tb['page_settings']; ?></h4>
					<div class="row">	
						<div class="form-group col-xs-6">
							<label for="input-prefix" class="text-muted"><span class="glyphicon glyphicon-sort-by-attributes"></span> <?php echo $this->tb['page_prefix']; ?></label>
							<input id="input-prefix" class="form-control" type="text" name="prefix" value="<?php echo $this->extractPrefixFromPath($P->path); ?>" onkeypress="return event.keyCode != 13;" />
						</div>
						<div class="form-group col-xs-6">
							<label class="text-muted"><span class="glyphicon glyphicon-eye-close"></span> <?php echo $this->tb['page_visibility']; ?></label>
							<div class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default<?php if ($hidden) { echo ' active'; } ?>"><?php echo $this->tb['btn_hide_page']; ?> 
									<input type="checkbox" name="<?php echo AM_KEY_HIDDEN; ?>"<?php if ($hidden) { echo ' checked'; } ?> />
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="input-redirect" class="text-muted"><span class="glyphicon glyphicon-link"></span> <?php echo $this->tb['page_redirect']; ?></label>
						<input id="input-redirect" class="form-control" type="text" name="data[<?php echo AM_KEY_URL; ?>]" value="<?php echo $data[AM_KEY_URL]; ?>" onkeypress="return event.keyCode != 13;" />
					</div>
				</div>
				<?php } ?> 
				
				<div class="list-group-item">
					<h4 class="text-muted"><?php echo $this->tb['page_vars_used']; ?></h4>
					<?php
					
					// Add textareas for all variables in $data, which are used in the currently selected template and are not part of the $standardKeys array 
					// and create empty textareas for those keys found in the template, but are not defined in $data.
					foreach ($templateKeys as $key) {
						if (isset($data[$key])) {
							echo $this->varTextArea($key, $data[$key]);
						} else {
							echo $this->varTextArea($key, '');
						}	
					}
					
					?>
				</div>
				
				<div class="list-group-item">
					<h4 class="text-muted"><?php echo $this->tb['page_vars_unused']; ?></h4>
					<div id="automad-custom-variables">	
						<?php
					
						// Add textareas for all left over variables $data, which don't show up in the template.
						// The left over vars get also a remove button, since they are optional.
						// Even when these vars are not used in the current template, they might be used in another template and should therefore still be present.
						foreach (array_diff(array_keys($data), $templateKeys, $standardKeys) as $key) {
							echo $this->varTextArea($key, $data[$key], true);
						}
					
						?> 
					</div>
					<br />
					<a class="btn btn-default" href="#" data-toggle="modal" data-target="#automad-add-variable-modal"><span class="glyphicon glyphicon-plus"></span> <?php echo $this->tb['btn_add_var']; ?></a>
				</div>

				<div class="list-group-item">	
					<div class="btn-group">
						<a class="btn btn-default" href=""><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_discard']; ?></a>
						<button type="submit" class="btn btn-success" data-loading-text="<?php echo $this->tb['btn_loading']?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $this->tb['btn_save']; ?></button>
					</div>
				</div>

			</div>
			
			<!-- Add Variable Modal -->	
			<div id="automad-add-variable-modal" class="modal fade">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title"><?php echo $this->tb['btn_add_var']; ?></h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="automad-add-variable-name" class="text-muted"><?php echo $this->tb['page_var_name']; ?></label>
								<input type="text" class="form-control" id="automad-add-variable-name" onkeypress="return event.keyCode != 13;" />
							</div>	
						</div>
						<div class="modal-footer">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button>
								<button type="button" class="btn btn-primary" id="automad-add-variable-button" data-automad-error-exists="<?php echo $this->tb['error_var_exists']; ?>" data-automad-error-name="<?php echo $this->tb['error_var_name']; ?>">
									<span class="glyphicon glyphicon-plus"></span> <?php echo $this->tb['btn_add_var']; ?>
								</button>
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
	
	$output['error'] = $this->tb['error_page_not_found'];
	
}


echo json_encode($output);


?>