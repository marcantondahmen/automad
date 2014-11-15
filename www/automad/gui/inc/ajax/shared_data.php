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


namespace Automad\Core;


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


	// If the posted form contains any "data", save the form's data to the page file.
	$data = array_filter($_POST['data']);

		
	// Build file content to be written to the txt file.
	$pairs = array();

	foreach ($data as $key => $value) {
		$pairs[] = $key . AM_PARSE_PAIR_SEPARATOR . ' ' . $value;
	}

	$content = implode("\r\n\r\n" . AM_PARSE_BLOCK_SEPARATOR . "\r\n\r\n", $pairs);
	

	// Write file.
	$old = umask(0);
	
	if (!@file_put_contents(AM_FILE_SITE_SETTINGS, $content)) {
		$output['error'] = $this->tb['error_permission'] . '<p>' . AM_FILE_SITE_SETTINGS . '</p>';
	}
	
	umask($old);
	
	
	// Clear the cache.
	$Cache = new Cache();
	$Cache->clear();
	
		
} else {
	
	
	// Else get the data from the .txt file and return a form's inner HTML containing its information.
	$data = Parse::textFile(AM_FILE_SITE_SETTINGS);
	
	// Set main properties
	$data[AM_KEY_SITENAME] = $this->siteName();
	
	if (isset($this->siteData[AM_KEY_THEME])) {
		$data[AM_KEY_THEME] = $this->siteData[AM_KEY_THEME];
	} else {
		$data[AM_KEY_THEME] = false;
	}
	
	// Get available themes.
	$themes = glob(AM_BASE_DIR . AM_DIR_THEMES . '/*', GLOB_ONLYDIR);
	
	// Array of the standard variable keys, which are always needed.
	$standardKeys = array(AM_KEY_SITENAME, AM_KEY_THEME);
	
	// Collect all keys of all shared site variables, which are found in the template files.
	$themesKeys = array_diff($this->getSiteVarsInThemes(), $standardKeys);
	
	// Start buffering the HTML.
	ob_start();
	
	
	?>
	
		<div class="form-group">
			<label for="input-data-sitename"><?php echo ucwords(AM_KEY_SITENAME); ?></label>
			<input id="input-data-sitename" class="form-control input-lg" type="text" name="data[<?php echo AM_KEY_SITENAME; ?>]" value="<?php echo str_replace('"', '&quot;', $data[AM_KEY_SITENAME]); ?>" onkeypress="return event.keyCode != 13;" />
		</div>
		
		<div class="form-group">
			<label for="input-data-theme">Theme</label>
			<select id="input-data-theme" class="form-control" name="data[<?php echo AM_KEY_THEME; ?>]">
				<?php
			
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

		<hr>

		<h3><?php echo $this->tb['shared_vars_used']; ?></h3>
		<?php
		// Add textareas for all variables in $data, which are used in the currently installed themes and are not part of the $standardKeys array 
		// and create empty textareas for those keys found in the themes, but are not defined in $data.
		foreach ($themesKeys as $key) {
			if (isset($data[$key])) {
				echo $this->varTextArea($key, $data[$key]);
			} else {
				echo $this->varTextArea($key, '');
			}
		}
		?>
	
		<hr>

		<h3><?php echo $this->tb['shared_vars_unused']; ?></h3>
		<div id="automad-custom-variables">
			<?php
			// All unused site-wide variables.
			foreach (array_diff(array_keys($data), $standardKeys, $themesKeys) as $key) {
				echo $this->varTextArea($key, $data[$key], true);
			}				
			?> 
		</div>
		<br />
		<a class="btn btn-default" href="#" data-toggle="modal" data-target="#automad-add-variable-modal"><span class="glyphicon glyphicon-plus"></span> <?php echo $this->tb['btn_add_var']; ?></a>
		
		<hr>
	
		<div class="btn-group btn-group-justified">
			<div class="btn-group"><a class="btn btn-danger" href=""><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_discard']; ?></a></div>
			<div class="btn-group"><button type="submit" class="btn btn-success" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $this->tb['btn_save']; ?></button></div>
		</div>
	
		<!-- Add Variable Modal -->	
		<div id="automad-add-variable-modal" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3 class="modal-title"><?php echo $this->tb['btn_add_var']; ?></h3>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="automad-add-variable-name"><?php echo $this->tb['shared_var_name']; ?></label>
							<input type="text" class="form-control" id="automad-add-variable-name" onkeypress="return event.keyCode != 13;" />
						</div>	
					</div>
					<div class="modal-footer">
						<div class="btn-group btn-group-justified">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal">
									<span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?>
								</button>
							</div>
							<div class="btn-group">
								<button type="button" class="btn btn-primary" id="automad-add-variable-button" data-automad-error-exists="<?php echo $this->tb['error_var_exists']; ?>" data-automad-error-name="<?php echo $this->tb['error_var_name']; ?>">
									<span class="glyphicon glyphicon-plus"></span> <?php echo $this->tb['btn_add']; ?> 
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


echo json_encode($output);


?>