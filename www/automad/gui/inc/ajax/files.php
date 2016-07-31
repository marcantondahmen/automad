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
 *	Copyright (c) 2014-2016 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


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
} else {
	$url = '';
}


$path = $this->Content->getPathByPostUrl();


// Delete files in $_POST['delete'].
if (isset($_POST['delete'])) {
	
	$output = $this->Content->deleteFiles($_POST['delete'], $path);

}


// Define image file extensions. 
$imageTypes = array('jpg', 'png', 'gif');


// Get files for each allowed file type.
$files = array();

foreach (Core\Parse::allowedFileTypes() as $type) {
	
	if ($f = glob($path . '*.{' . strtolower($type) . ',' . strtoupper($type) . '}', GLOB_BRACE)) {
		$files = array_merge($files, $f);
	}
	
}

ob_start();


if ($files) { ?>
	
		<div class="uk-text-right">
			<a href="#automad-upload-modal" class="uk-button uk-margin-bottom" data-uk-modal="{bgclose: false, keyboard: false}">
				<span class="uk-hidden-small"><i class="uk-icon-upload"></i>&nbsp;&nbsp;</span>
				<?php echo Text::get('btn_upload'); ?>
			</a>
			<button class="uk-button uk-button-danger uk-margin-bottom" data-automad-submit="files">
				<span class="uk-hidden-small"><i class="uk-icon-trash"></i>&nbsp;&nbsp;</span>
				<?php echo Text::get('btn_remove_selected'); ?>
			</button>
		</div>
	
		<?php 
		
		sort($files);

		foreach ($files as $file) { 
			
			$ext = FileSystem::getExtension($file);
			
			if (in_array(strtolower($ext), $imageTypes)) { 

				$img = new \Automad\Core\Image($file, 95, 95, true);
				$size = '<div class="uk-text-muted uk-text-small">' . 
					'<i class="uk-icon-expand uk-icon-justify"></i>&nbsp;&nbsp;' .
					$img->originalWidth . ' <i class="uk-icon-times"></i> ' . $img->originalHeight . 
					'</div>';
				$icon = '<img src="' . AM_BASE_URL . $img->file . '" width="' . $img->width . '" height="' . $img->height . '" />';
				
			} else {
				
				$size = '';
				$icon = '<div class="uk-vertical-align-middle uk-text-muted"><i class="uk-icon-eye-slash"></i><br /><span class="automad-files-extension">' . $ext . '</span></div>';
				
			}
			
			$caption = Core\Parse::caption($file);
			$editLink = 'href="#automad-edit-file-info-modal" data-uk-modal data-automad-caption="' . htmlspecialchars($caption) . '" data-automad-file="' . basename($file) . '"';
			
		?>
		
		<div class="uk-panel uk-panel-box uk-margin-small-top">	
			<div class="automad-files-icon uk-border-rounded uk-overflow-hidden">
				<div class="uk-vertical-align uk-text-center">
					<?php echo $icon; ?>
				</div>
			</div>
			<div class="automad-files-info">
				<div class="uk-panel-title uk-text-truncate">
					<?php echo basename($file); ?>
				</div>
				
				<?php if ($caption) { ?>
				<div class="uk-text-muted uk-text-small uk-text-truncate">
					<i class="uk-icon-comment-o uk-icon-justify"></i>&nbsp;&nbsp;"<?php echo Core\String::shorten($caption, 100); ?>"
				</div>
				<?php } ?>
				
				<div class="uk-text-muted uk-text-small uk-text-truncate">
					<i class="uk-icon-calendar-o uk-icon-justify"></i>&nbsp;&nbsp;<?php echo date('M j, Y H:i', filemtime($file)); ?>
				</div>
				<div class="uk-text-small uk-text-truncate">
					<a class="uk-text-muted" href="<?php echo str_replace(AM_BASE_DIR, AM_BASE_URL, $file); ?>" download>
						<i class="uk-icon-download uk-icon-justify"></i>&nbsp;&nbsp;<?php echo str_replace(AM_BASE_DIR, '', $file) ?>
					</a>
				</div>
				<?php echo $size; ?>   
				<div class="automad-files-info-edit">
					<a href="#automad-edit-file-info-modal" class="uk-icon-button uk-icon-pencil" <?php echo $editLink; ?>></a>
				</div>
			</div>
			<div class="uk-panel-badge">
				<label data-automad-toggle>
					<input type="checkbox" name="delete[]" value="<?php echo basename($file); ?>" />
				</label>
			</div>	
		</div>
				
		<?php } ?> 
		
		<!-- Edit Modal -->
		<div id="automad-edit-file-info-modal" class="uk-modal" data-automad-url="<?php echo $url; ?>">
			<div class="uk-modal-dialog">
				<div class="uk-modal-header">
					<?php echo Text::get('btn_edit_file_info'); ?>
				</div>
				<div id="automad-edit-file-info-container" class="uk-position-relative">
					<!-- Input fields get created by JS -->
				</div>
				<div class="uk-modal-footer uk-text-right">
					<button type="button" class="uk-modal-close uk-button">
						<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php echo Text::get('btn_close'); ?>
					</button>
					<button id="automad-edit-file-info-submit" type="button" class="uk-button uk-button-primary">
						<i class="uk-icon-check"></i>&nbsp;&nbsp;<?php echo Text::get('btn_ok'); ?>
					</button>
				</div>
			</div>
		</div>
	
<?php } else { ?>

		<a href="#automad-upload-modal" class="uk-button uk-button-large uk-width-1-1" data-uk-modal="{bgclose: false, keyboard: false}">
			<i class="uk-icon-upload"></i>&nbsp;&nbsp;<?php echo Text::get('btn_upload'); ?>
		</a>
	
<?php } ?> 

		<!-- Upload Modal -->
		<div id="automad-upload-modal" class="uk-modal" data-automad-url="<?php echo $url; ?>" data-automad-dropzone-text="<?php echo Text::get('dropzone'); ?>" data-automad-browse-text="<?php echo Text::get('btn_browse'); ?>">
			<div class="uk-modal-dialog">
				<div class="uk-modal-header">
					<?php echo Text::get('btn_upload'); ?>
				</div>
				<div id="automad-upload-container"></div>
				<div class="uk-modal-footer uk-text-right">
					<button type="button" class="uk-modal-close uk-button">
						<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php echo Text::get('btn_close'); ?>
					</button>
				</div>
				
			</div>
		</div>

<?php 


$output['html'] = ob_get_contents();
ob_end_clean();


echo json_encode($output);


?>