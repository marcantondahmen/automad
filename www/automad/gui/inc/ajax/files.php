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
 *	File Manager. In case $_POST['url'] is defined, the files of that page will be managed.
 *	Else, the files under "/shared" will be managed instead.
 *
 *	Basically the inner (!) HTML form the calling form will be replaced with the updated file list. 
 *	So that means, that the outer form tags are NOT part of the HTML output!
 */


$output = array();


// Check if file from a specified page or the shared files will be listed and managed.
// To display a file list of a certain page, its URL has to be submitted along with the form data.
if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->collection)) {
	
	$url = $_POST['url'];
	$Page = $this->collection[$url];
	$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
	
} else {
	
	$url = '';
	$path = AM_BASE_DIR . AM_DIR_SHARED . '/';
	
}


// Delete files in $_POST['delete'].
if (isset($_POST['delete'])) {
	
	// Check if directory is writable.
	if (is_writable($path)) {
	
		$success = array();
		$errors = array();
	
		foreach ($_POST['delete'] as $f) {
		
			// Make sure submitted filename has no '../' (basename).
			$file = $path . basename($f);
		
			if (is_writable($file)) {
				if (unlink($file)) {
					$success[] = $this->tb['success_remove'] . ' <strong>' . basename($file) . '</strong>';
				}
			} else {
				$errors[] = $this->tb['error_remove'] . ' <strong>' . basename($file) . '</strong>';
			} 
	
		}
	
		// Clear cache to update galleries and sliders.
		$Cache = new Cache();
		$Cache->clear();
	
		$output['success'] = implode('<br />', $success);
		$output['error'] = implode('<br />', $errors);

	} else {
		
		$output['error'] = $this->tb['error_permission'] . '<p>' . $path . '</p>';
		
	}

}


// Get the allowed file types from const.php.
$allowedFileTypes = Parse::allowedFileTypes();


// Define image file extensions. 
$imageTypes = array('jpg', 'png', 'gif');


// Get files for each allowed file type.
$files = array();

foreach ($allowedFileTypes as $type) {
	
	if ($f = glob($path . '*.{' . strtolower($type) . ',' . strtoupper($type) . '}', GLOB_BRACE)) {
		$files = array_merge($files, $f);
	}
	
}


ob_start();


if ($files) {
	
	sort($files);

	foreach ($files as $file) { 
		
		?>
		
		<div class="box">
			
			<div class="row">
				
				<?php 
	
				$extension = pathinfo($file, PATHINFO_EXTENSION);
	
				if (in_array(strtolower($extension), $imageTypes)) { 
	
					$img = new Image($file, 125, 125, true);
	
					echo 	'<div class="col-xs-3">' .
						'<a href="' . str_replace(AM_BASE_DIR, AM_BASE_URL, $file) . '" target="_blank" title="Download" tabindex=-1>' .
						'<img class="img-responsive" src="' . AM_BASE_URL . $img->file . '" width="' . $img->width . '" height="' . $img->height . '" />' .
						'</a>' . 
						'</div>' .		
						'<div class="col-xs-8">' . 
						'<h4><a title="' . $this->tb['btn_rename_file'] . '" href="#" data-target="#automad-rename-file-modal" data-toggle="modal" data-file="' . basename($file) . '">' . basename($file) . ' <span class="glyphicon glyphicon-pencil"></span></a></h4>';
					
					if (strtolower($extension) == 'jpg' && $img->description) {
						echo '<h6 title="Exif description"><span class="glyphicon glyphicon-comment"></span> ' . $img->description . '</h6>';	
					}
						
					echo	'<h6 title="Modification time"><span class="glyphicon glyphicon-time"></span> ' . date('F j, Y / H:i', filemtime($file)) . '</h6>' . 
						'<h6 title="Path relative to the Automad base directory"><span class="glyphicon glyphicon-hdd"></span> ' . str_replace(AM_BASE_DIR, '', $file) . '</h6>' .
						'<div class="badge">' . $img->originalWidth . 'x' . $img->originalHeight . '</div>' .
						'</div>';
			
				} else { 
	
					echo 	'<div class="col-xs-3">' .
						'<a class="filetype img-responsive" href="' . str_replace(AM_BASE_DIR, AM_BASE_URL, $file) . '" target="_blank" title="Download" tabindex=-1>' .
						'<span class="glyphicon glyphicon-file"></span> ' . $extension . 
						'</a>' .
						'</div>' .
						'<div class="col-xs-8">' . 
						'<h4><a title="' . $this->tb['btn_rename_file'] . '" href="#" data-target="#automad-rename-file-modal" data-toggle="modal" data-file="' . basename($file) . '">' . basename($file) . ' <span class="glyphicon glyphicon-pencil"></span></a></h4>' .
						'<h6 title="Modification time"><span class="glyphicon glyphicon-time"></span> ' . date('F j, Y / H:i', filemtime($file)) . '</h6>' .
						'<h6 title="Path relative to the Automad base directory"><span class="glyphicon glyphicon-hdd"></span> ' . str_replace(AM_BASE_DIR, '', $file) . '</h6>' .
						'</div>';
	 
				} 

				?> 
				
				<div class="col-xs-1">
					<div class="pull-right btn-group" data-toggle="buttons">
						<label class="btn btn-default btn-xs">
							<input type="checkbox" name="delete[]" value="<?php echo basename($file); ?>"><span class="glyphicon glyphicon-ok"></span>
						</label>
					</div>
				</div>
	
			</div>
			
		</div>
				
		<?php 

	}

} else {

	?><div class="alert alert-warning"><?php echo $this->tb['error_no_files']; ?></div><?php
	
}

?> 

<hr>

<div class="btn-group btn-group-justified">
	<div class="btn-group">
		<button type="submit" class="btn btn-danger" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-trash"></span> <?php echo $this->tb['btn_remove_selected']; ?></button>
	</div>
	<div class="btn-group">
		<a class="btn btn-primary" href="#" data-target="#automad-upload-modal"><span class="glyphicon glyphicon-open"></span> <?php echo $this->tb['btn_upload']; ?></a>
	</div>
</div>


<!-- Rename File Modal -->
<div class="modal fade" id="automad-rename-file-modal" tabindex="-1" data-automad-url="<?php echo $url; ?>">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header"> 
				<h3 class="modal-title"><?php echo $this->tb['btn_rename_file']; ?></h3> 
			</div>
			<div class="modal-body"><!-- Input fields get created by JS --></div>	
			<div class="modal-footer">
				<div class="btn-group btn-group-justified">
					<div class="btn-group"><button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button></div>
					<div class="btn-group"><button id="rename-file" type="button" class="btn btn-primary" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $this->tb['btn_rename_file']; ?></button></div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Upload Modal -->
<div class="modal fade" id="automad-upload-modal" tabindex="-1" data-automad-url="<?php echo $url; ?>" data-automad-dropzone-text="<?php echo $this->tb['dropzone']; ?>" data-automad-browse-text="<?php echo $this->tb['btn_browse']; ?>">
	<div class="modal-dialog">
		<div class="modal-content"> 
			<div class="modal-header"> 
				<h3 class="modal-title"><?php echo $this->tb['btn_upload']; ?></h3> 
			</div>
			<div id="automad-upload" class="modal-body"></div>	
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button>
			</div>
		</div>
	</div>
</div>

<?php


$output['html'] = ob_get_contents();
ob_end_clean();


echo json_encode($output);


?>