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
 *	File Manager. In case $_POST['url'] is defined, the files of that page will be managed.
 *	Else, the files under "/shared" will be managed instead.
 *
 *	Basically the inner (!) HTML form the calling form will be replaced with the updated file list. 
 *	So that means, that the outer form tags are NOT part of the HTML output!
 */


$output = array();
$output['debug'] = $_POST;


// Check if file from a specified page or the shared files will be listed and managed.
// To display a file list of a certain page, the submitting form needs a hidden URL field.
if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->collection)) {
	
	$url = $_POST['url'];
	$P = $this->collection[$url];
	$path = AM_BASE_DIR . AM_DIR_PAGES . $P->path;
	
} else {
	
	$url = '';
	$path = AM_BASE_DIR . AM_DIR_SHARED . '/';
	
}


// Delete file in $_POST['delete'].
if (isset($_POST['delete'])) {
	
	$success = array();
	$errors = array();
	
	foreach ($_POST['delete'] as $f) {
		
		// Make sure submitted filename has no '../' (basename).
		$file = $path . basename($f);
		
		if (is_writable($file)) {
			if (unlink($file)) {
				$success[] = 'Successfully deleted <strong>' . basename($file) . '</strong>';
			}
		} else {
			$errors[] = 'Can not delete <strong>' . basename($file) . '</strong>';
		} 
	
	}
	
	$output['success'] = implode('<br />', $success);
	$output['error'] = implode('<br />', $errors);

}


// Get the allowed file types from const.php.
$allowedFileTypes = unserialize(AM_ALLOWED_FILE_TYPES);


// Define image file extensions. 
$imageTypes = array('jpg', 'png', 'gif');


// Get files for each allowed file type.
$files = array();

foreach ($allowedFileTypes as $type) {	 	
	$files = array_merge($files, glob($path . '*.{' . strtolower($type) . ',' . strtoupper($type) . '}', GLOB_BRACE));
}


ob_start();


?>

<div class="list-group">
	
	<div class="list-group-item">
		<h5 class="text-muted"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;&nbsp;<?php echo $path; ?></h5>
	</div>
	
	<?php

	if ($files) {
		
		sort($files);
	
		foreach ($files as $file) { 
			
			?>
			
			<div class="list-group-item">
				
				<div class="row">
					
					<?php 
		
					$extension = pathinfo($file, PATHINFO_EXTENSION);
		
					if (in_array(strtolower($extension), $imageTypes)) { 
		
						$img = new Image($file, 95, 95, true);
		
						echo 	'<div class="col-md-2 col-xs-4"><a href="' . str_replace(AM_BASE_DIR, AM_BASE_URL, $file) . '" target="_blank" title="Download" tabindex=-1>' .
							'<img class="img-thumbnail" src="' . AM_BASE_URL . $img->file . '" width="' . $img->width . '" height="' . $img->height . '" />' .
							'</a></div>' .		
							'<div class="col-md-9 col-xs-6"><h5>' . basename($file) . '</h5>' .
							'<h6>' . $img->description . '</h6>' .
							'<h6 class="label label-default">' . $img->originalWidth . 'x' . $img->originalHeight . '</h6></div>';
				
					} else { 
		
						echo 	'<div class="col-md-2 col-xs-4"><a class="btn btn-default btn-block" href="' . str_replace(AM_BASE_DIR, AM_BASE_URL, $file) . '" target="_blank" title="Download" tabindex=-1>' .
							'<h1><span class="glyphicon glyphicon-file"></span></h1>' .
							'</a></div>' .
							'<div class="col-md-9 col-xs-6"><h5>' . basename($file) . '</h5></div>';
		 
					} 
	
					?> 
					
					<div class="col-md-1 col-xs-2">
						<div class="pull-right btn-group" data-toggle="buttons">
							<label class="btn btn-default" title="Mark file for deletion">
								<input type="checkbox" name="delete[]" value="<?php echo basename($file); ?>"><span class="glyphicon glyphicon-trash"></span>
							</label>
						</div>
					</div>
		
				</div>
				
			</div>
					
			<?php 

		}
	
	} else {
	
		?><div class="list-group-item"><h4>No files!</h4></div><?php
		
	}

	?> 
	
	<div class="list-group-item">
		<ul class="nav nav-pills nav-justified">	
			<li><a href="#" data-target="#automad-upload-modal"><span class="glyphicon glyphicon-open"></span> Upload Files</a></li>
		</ul>	
	</div>
	
	<div class="list-group-item">		
		<button type="submit" class="btn btn-danger btn-block" data-loading-text="Processing ..."><span class="glyphicon glyphicon-trash"></span> Delete Selected</button>	
	</div>
	
</div>

<!-- Modal -->
<div class="modal fade" id="automad-upload-modal" tabindex="-1" data-automad-url="<?php echo $url; ?>">
	<div class="modal-dialog modal-sm">
		<div class="modal-content"> 
			<div class="modal-header"> 
				<h4 class="modal-title" id="myModalLabel">Upload Files</h4> 
			</div>
			<div id="automad-upload" class="modal-body"></div>	
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" data-loading-text="Uploading ...">Close</button>
			</div>
		</div>
	</div>
</div>

<?php


$output['html'] = ob_get_contents();
ob_end_clean();


echo json_encode($output);


?>