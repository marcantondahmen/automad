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
	
		<div class="form-group">
			<label for="input-data-sitename"><?php echo ucwords(AM_KEY_SITENAME); ?></label>
			<input id="input-data-sitename" class="form-control input-lg" type="text" name="data[<?php echo AM_KEY_SITENAME; ?>]" value="<?php echo str_replace('"', '&quot;', $data[AM_KEY_SITENAME]); ?>" onkeypress="return event.keyCode != 13;" />
		</div>
		
		<div class="form-group">
			<label for="input-data-theme">Theme</label>
			<select id="input-data-theme" class="form-control" name="data[<?php echo AM_KEY_THEME; ?>]">
				<?php
			
				// Get available themes.
				$themes = glob(AM_BASE_DIR . AM_DIR_THEMES . '/*', GLOB_ONLYDIR);
			
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

		<!-- Used shared variables -->
		<?php echo $this->Html->formFields(Text::get('shared_vars_used'), $this->Keys->inAllTemplates(), $data, false, false); ?>
	
		<hr>

		<!-- Unused shared variables -->
		<div id="automad-custom-variables">
			<?php
				$unusedDataKeys = array_diff(array_keys($data), $this->Keys->inAllTemplates(), $this->Keys->reserved);
				echo $this->Html->formFields(Text::get('shared_vars_unused'), $unusedDataKeys, $data, true, false);				
			?> 
		</div>
		<br />
		<a class="btn btn-default" href="#" data-toggle="modal" data-target="#automad-add-variable-modal"><span class="glyphicon glyphicon-plus"></span> <?php echo Text::get('btn_add_var'); ?></a>
		
		<hr>
	
		<div class="btn-group btn-group-justified">
			<div class="btn-group"><a class="btn btn-danger" href=""><span class="glyphicon glyphicon-remove"></span> <?php echo Text::get('btn_discard'); ?></a></div>
			<div class="btn-group"><button type="submit" class="btn btn-success" data-loading-text="<?php echo Text::get('btn_loading'); ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo Text::get('btn_save'); ?></button></div>
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
							<label for="automad-add-variable-name"><?php echo Text::get('shared_var_name'); ?></label>
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
									<span class="glyphicon glyphicon-plus"></span> <?php echo Text::get('btn_add'); ?> 
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