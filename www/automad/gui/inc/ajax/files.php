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
 *	Copyright (c) 2014-2017 by Marc Anton Dahmen
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
	
		<ul class="uk-grid">
			<li class="uk-width-2-3 uk-width-medium-1-2">
				<a href="#am-upload-modal" class="uk-button uk-button-primary uk-width-1-1" data-uk-modal="{bgclose: false, keyboard: false}">
					<span class="uk-hidden-small"><i class="uk-icon-upload"></i>&nbsp;</span>
					<?php Text::e('btn_upload'); ?>
				</a>
			</li>
			<li class="uk-width-1-3 uk-width-medium-1-2">
				<button class="uk-button uk-button-danger uk-width-1-1" data-am-submit="files">
					<i class="uk-icon-trash"></i>
					<span class="uk-hidden-small">&nbsp;<?php Text::e('btn_remove_selected'); ?></span>
				</button>
			</li>
		</ul>
			
		<ul class="uk-grid uk-grid-match uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4" data-uk-grid-match="{target:'.uk-panel'}" data-uk-grid-margin>
			<?php 
			
			sort($files);

			foreach ($files as $file) { 
				
				$ext = FileSystem::getExtension($file);
				$caption = Core\Parse::caption($file);
				
				$fileInfo = array(
					'img' => false, 
					'filename' => basename($file), 
					'caption' => htmlspecialchars($caption), 
					'extension' => htmlspecialchars($ext),
					'download' => str_replace(AM_BASE_DIR, AM_BASE_URL, $file)
				);
				
				if (in_array(strtolower($ext), $imageTypes)) { 

					$imgPanel = new Core\Image($file, 220, 165, true);
					$size = '<div class="uk-panel-badge uk-badge">' . 
						$imgPanel->originalWidth . ' <i class="uk-icon-times"></i> ' . $imgPanel->originalHeight . 
						'</div>';
					$icon = '<img src="' . AM_BASE_URL . $imgPanel->file . '" width="' . $imgPanel->width . '" height="' . $imgPanel->height . '" />';
					$imgModal = new Core\Image($file, 1000, 800, false);
			
					// Update file info with image.
					$fileInfo['img'] = array(
						'src' => AM_BASE_URL . $imgModal->file,
						'width' => $imgModal->width,
						'height' => $imgModal->height
					);
					
				} else {
					
					$size = '';
					$icon = '<div class="am-panel-icon"><span>' . $ext . '</span></div>';
					
				}
						
			?>  
			<li>
				<div class="uk-panel uk-panel-box" data-am-file-info='<?php echo json_encode($fileInfo); ?>'>
					<a href="#am-edit-file-info-modal" class="uk-panel-teaser uk-display-block" data-uk-modal>
						<?php echo $icon; ?>
					</a>
					<div class="uk-margin-bottom">
						<?php echo basename($file); ?>
					</div>
					<?php if ($caption) { ?>
					<div class="uk-text-small uk-text-truncate uk-margin-small-top">
						<i class="uk-icon-comment-o uk-icon-justify"></i>&nbsp;&nbsp;"<?php echo Core\String::shorten($caption, 100); ?>"
					</div>
					<?php } ?>
					<div class="uk-text-small uk-text-truncate uk-margin-small-top">
						<i class="uk-icon-calendar-o uk-icon-justify"></i>&nbsp;&nbsp;<?php echo date('M j, Y H:i', filemtime($file)); ?>
					</div>
					<?php echo $size; ?> 
					<div class="am-panel-bottom">
						<a href="#am-edit-file-info-modal" class="uk-icon-button uk-icon-pencil" title="<?php Text::e('btn_edit_file_info'); ?>" data-uk-modal></a>
						<label class="am-panel-bottom-right" data-am-toggle><input type="checkbox" name="delete[]" value="<?php echo basename($file); ?>" /></label>
					</div>
				</div>	
			</li>
			<?php } ?> 		
		</ul>
		
		<!-- Edit Modal -->
		<div id="am-edit-file-info-modal" class="uk-modal" data-am-url="<?php echo $url; ?>">
			<div class="uk-modal-dialog uk-modal-dialog-blank uk-text-center">
				<a href="#" class="uk-modal-close uk-close"></a>
				<div class="am-files-modal-container">
					<div class="am-files-modal-preview">
						<img id="am-edit-file-info-img" src="" />
						<div id="am-edit-file-info-icon" data-am-extension=""></div>
					</div>
					<div class="am-files-modal-info">
						<div class="uk-modal-header uk-hidden-small">
							<?php Text::e('btn_edit_file_info'); ?>
						</div>
						<div class="uk-form uk-form-stacked">
							<input id="am-edit-file-info-old-name" type="hidden" name="old-name" />	
							<div class="uk-form-row">
								<label for="am-edit-file-info-new-name" class="uk-form-label"><?php Text::e('file_name'); ?></label>
								<input id="am-edit-file-info-new-name" name="new-name" class="uk-form-controls uk-form-large uk-width-1-1" data-am-watch-exclude />
							</div>
							<div class="uk-form-row">
								<label for="am-edit-file-info-caption" class="uk-form-label"><?php Text::e('file_caption'); ?></label>
								<textarea id="am-edit-file-info-caption" name="caption" class="uk-form-controls uk-width-1-1" data-am-watch-exclude></textarea>
							</div>
						</div>
						<div class="uk-block uk-text-right">
							<button type="button" class="uk-modal-close uk-button">
								<span class="uk-hidden-small"><i class="uk-icon-close"></i>&nbsp;</span>
								<?php Text::e('btn_close'); ?>
							</button>
							<a id="am-edit-file-info-download" class="uk-button uk-button-primary" download>
								<span class="uk-hidden-small"><i class="uk-icon-download"></i>&nbsp;</span>
								<?php Text::e('btn_download_file'); ?>
							</a>
							<button id="am-edit-file-info-submit" type="button" class="uk-button uk-button-success">
								<span class="uk-hidden-small"><i class="uk-icon-check"></i>&nbsp;</span>
								<?php Text::e('btn_save'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
<?php } else { ?>

		<a href="#am-upload-modal" class="uk-button uk-button-primary uk-button-large uk-width-1-1" data-uk-modal="{bgclose: false, keyboard: false}">
			<i class="uk-icon-upload"></i>&nbsp;&nbsp;<?php Text::e('btn_upload'); ?>
		</a>
	
<?php } ?> 

		<!-- Upload Modal -->
		<div id="am-upload-modal" class="uk-modal" data-am-url="<?php echo $url; ?>" data-am-dropzone-text="<?php Text::e('dropzone'); ?>" data-am-browse-text="<?php Text::e('btn_browse'); ?>">
			<div class="uk-modal-dialog">
				<div class="uk-modal-header">
					<?php Text::e('btn_upload'); ?>
				</div>
				<div id="am-upload-container"></div>
				<div class="uk-modal-footer uk-text-right">
					<button type="button" class="uk-modal-close uk-button uk-button-primary uk-margin-top">
						<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php Text::e('btn_close'); ?>
					</button>
				</div>
			</div>
		</div>

<?php 


$output['html'] = ob_get_contents();
ob_end_clean();


echo json_encode($output);


?>