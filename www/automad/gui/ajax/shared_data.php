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
$output['debug'] = $_POST;



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
	file_put_contents(AM_FILE_SITE_SETTINGS, $content);
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
			
				<h4>Main Properties</h4>

				<div class="form-group">
					<label for="input-data-sitename" class="text-muted"><?php echo ucwords(AM_KEY_SITENAME); ?></label>
					<input id="input-data-sitename" class="form-control input-lg" type="text" name="data[<?php echo AM_KEY_SITENAME; ?>]" value="<?php echo $data[AM_KEY_SITENAME]; ?>" onkeypress="return event.keyCode != 13;" />
				</div>

				<div class="form-group">
					<label for="input-data-theme" class="text-muted">Theme</label>
					<select id="input-data-theme" class="form-control input-sm" name="data[<?php echo AM_KEY_THEME; ?>]" size="1">
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
		
			<div class="list-group-item" id="automad-custom-variables">
			
				<h4>Custom Content</h4>
			
				<?php

				foreach ($data as $key => $value) {
			
					// Only user defined custom variables.
					// Standard vars are processed separately above.
					if (!in_array($key, array(AM_KEY_SITENAME, AM_KEY_THEME))) {
						echo 	'<div class="form-group">' . 
							'<label for="input-data-' . $key . '" class="text-muted">' . ucwords($key) . '</label>' .
							'<button type="button" class="close automad-remove-parent">&times;</button>' .
							'<textarea id="input-data-' . $key . '" class="form-control input-sm" name="data[' . $key . ']" rows="10">' . $value . '</textarea>' .
							'</div>';	
					}
			
				}

				?>
			
			</div>
		
			<div class="list-group-item">
				<ul class="nav nav-pills nav-justified">
					<li><a href="#" data-toggle="modal" data-target="#automad-add-variable-modal"><span class="glyphicon glyphicon-plus"></span> Add Variable</a></li>
					<li><a href=""><span class="glyphicon glyphicon-remove"></span> Discard Changes</a></li>
				</ul>
			</div>

			<div class="list-group-item clearfix">	
				<button type="submit" class="btn btn-success btn-block" data-loading-text="Saving Changes ..."><span class="glyphicon glyphicon-ok"></span> Save Changes</button>
			</div>
		
		
		</div>	

		<!-- Add Variable Modal -->	
		<div id="automad-add-variable-modal" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Add Another Custom Variable</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="automad-add-variable-name" class="text-muted">Variable Name</label>
							<input type="text" class="form-control" id="automad-add-variable-name" onkeypress="return event.keyCode != 13;" />
						</div>	
					</div>
					<div class="modal-footer">
						<div class="btn-group">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary" id="automad-add-variable-button">Add</button>
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