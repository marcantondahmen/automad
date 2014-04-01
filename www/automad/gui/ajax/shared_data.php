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
	$C = new Cache();
	$C->clear();
	
		
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
	
	// Start buffering the HTML.
	ob_start();
	

	?>
	
		<div class="list-group">
	
			<div class="list-group-item">
			
				<div class="form-group">
					<label for="input-data-sitename" class="text-muted"><?php echo ucwords(AM_KEY_SITENAME); ?></label>
					<input id="input-data-sitename" class="form-control input-lg" type="text" name="data[<?php echo AM_KEY_SITENAME; ?>]" value="<?php echo $data[AM_KEY_SITENAME]; ?>" onkeypress="return event.keyCode != 13;" />
				</div>

				<div class="form-group">
					<label for="input-data-theme" class="text-muted">Theme</label>
					<select id="input-data-theme" class="form-control" name="data[<?php echo AM_KEY_THEME; ?>]">
						<?php
					
						foreach ($themes as $theme) {
						
							echo '<option'; 
						
							if (basename($theme) == $data[AM_KEY_THEME]) {
								echo ' selected';
							}
						
							echo ' value="' . basename($theme) . '">' . ucwords(basename($theme)) . '</option>';
						
						}
				
						?> 	
					</select>
				</div>

			</div>
		
			<div class="list-group-item">
				<h4 class="text-muted"><?php echo $this->tb['shared_vars']; ?></h4>
				<br />
				<div id="automad-custom-variables">
					<?php
					// All site-wide variable except the site's name and the theme.
					foreach (array_diff(array_keys($data), array(AM_KEY_SITENAME, AM_KEY_THEME)) as $key) {
						echo $this->varTextArea($key, $data[$key], true);
					}				
					?> 
				</div>
				<a class="btn btn-default" href="#" data-toggle="modal" data-target="#automad-add-variable-modal"><span class="glyphicon glyphicon-plus"></span> <?php echo $this->tb['btn_add_var']; ?></a>
			</div>

			<div class="list-group-item clearfix">	
				<div class="btn-group">
					<a class="btn btn-default" href=""><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_discard']; ?></a>
					<button type="submit" class="btn btn-success" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $this->tb['btn_save']; ?></button>
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
							<label for="automad-add-variable-name" class="text-muted"><?php echo $this->tb['shared_var_name']; ?></label>
							<input type="text" class="form-control" id="automad-add-variable-name" onkeypress="return event.keyCode != 13;" />
						</div>	
					</div>
					<div class="modal-footer">
						<div class="btn-group">
							<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button>
							<button type="button" class="btn btn-primary" id="automad-add-variable-button" data-automad-error-exists="<?php echo $this->tb['error_var_exists']; ?>" data-automad-error-name="<?php echo $this->tb['error_var_name']; ?>">
								<span class="glyphicon glyphicon-plus"></span> <?php echo $this->tb['btn_add']; ?> 
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


echo json_encode($output);


?>