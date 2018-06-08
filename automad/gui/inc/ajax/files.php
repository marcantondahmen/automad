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
 *	Copyright (c) 2014-2018 by Marc Anton Dahmen
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
			<li class="uk-width-2-3 uk-width-medium-1-1">
				<a 
				href="#am-upload-modal" 
				class="uk-button uk-button-success" 
				data-uk-modal="{bgclose: false, keyboard: false}"
				>
					<i class="uk-icon-upload"></i>&nbsp;
					<?php Text::e('btn_upload'); ?>
				</a>	
				<button 
				class="uk-button uk-button-danger uk-hidden-small" 
				data-am-submit="files"
				>
					<i class="uk-icon-remove"></i>&nbsp;
					<?php Text::e('btn_remove_selected'); ?>
				</button>
			</li>
			<li class="uk-width-1-3 uk-visible-small">
				<div class="am-icon-buttons uk-text-right">
					<button 
					class="uk-button uk-button-danger" 
					title="<?php Text::e('btn_remove_selected'); ?>"
					data-am-submit="files"
					data-uk-tooltip
					>
						<i class="uk-icon-remove"></i>
					</button>
				</div>
			</li>
		</ul>
		
		<ul 
		class="uk-grid uk-grid-match uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4 uk-margin-top" 
		data-uk-grid-match="{target:'.uk-panel'}" 
		data-uk-grid-margin
		>
			<?php 
			
			sort($files);
			$i = 0;

			foreach ($files as $file) { 
				
				$id = 'am-file-' . ++$i;
				$ext = FileSystem::getExtension($file);
				$caption = Core\Parse::caption($file);
				
				$fileInfo = 	array(
									'img' => false, 
									'filename' => basename($file), 
									'caption' => htmlspecialchars($caption), 
									'extension' => htmlspecialchars($ext),
									'download' => AM_BASE_URL . Core\Str::stripStart($file, AM_BASE_DIR)
								);
				
				if (Core\Parse::fileIsImage($file)) { 

					$imgPanel = new Core\Image($file, 320, 240, true);
					$size = '<div class="uk-panel-badge uk-badge">' . 
							$imgPanel->originalWidth . ' <i class="uk-icon-times"></i> ' . $imgPanel->originalHeight . 
							'</div>';
					$icon = '<img src="' . AM_BASE_URL . $imgPanel->file . '" width="' . $imgPanel->width . '" height="' . $imgPanel->height . '" />';
					$imgModal = new Core\Image($file, 1600, 1200, false);
			
					// Update file info with image.
					$fileInfo['img'] = 	array(
											'src' => AM_BASE_URL . $imgModal->file,
											'width' => $imgModal->width,
											'height' => $imgModal->height,
											'originalWidth' => $imgPanel->originalWidth,
											'originalHeight' => $imgPanel->originalHeight
										);
					
				} else {
					
					$size = '';
					$icon = '<div class="am-panel-icon">' .
							'<i class="uk-icon-file-o am-files-icon-' . $ext . '"></i>' .
							'</div>';
					
				}
						
			?>  
			<li>
				<div 
				id="<?php echo $id; ?>" 
				class="uk-panel uk-panel-box" 
				data-am-file-info='<?php echo json_encode($fileInfo); ?>'
				>
					<a 
					href="#am-edit-file-info-modal" 
					class="uk-panel-teaser uk-display-block" 
					data-uk-modal
					>
						<?php echo $icon; ?>
					</a>
					<div 
					class="uk-panel-title" 
					title="<?php echo basename($file); ?>" 
					>
						<?php echo basename($file); ?>
					</div>
					<?php if ($caption) { ?>
					<div class="uk-text-small uk-text-truncate uk-text-muted uk-hidden-small">
						<i class="uk-icon-comment-o uk-icon-justify"></i>&nbsp;
						"<?php echo Core\Str::shorten($caption, 100); ?>"
					</div>
					<?php } ?>
					<div class="uk-text-small uk-text-truncate uk-text-muted uk-hidden-small">
						<i class="uk-icon-calendar-o uk-icon-justify"></i>&nbsp;
						<?php echo date('M j, Y H:i', filemtime($file)); ?>
					</div>
					<?php echo $size; ?> 
					<div class="am-panel-bottom">
						<div data-uk-dropdown="{mode:'click'}">
							<div class="uk-icon-button uk-icon-ellipsis-v"></div>
							<div class="uk-dropdown uk-dropdown-small">
								<ul class="uk-nav uk-nav-dropdown">
									<li>
										<a 
										href="#am-edit-file-info-modal" 
										data-uk-modal 
										>
											<i class="uk-icon-pencil"></i>&nbsp;
											<?php Text::e('btn_edit_file_info'); ?>
										</a>
									</li>
									<?php if (Core\Parse::fileIsImage($file)) { ?>
									<li>
										<a href="#am-copy-resized-modal"
										data-uk-modal
										>
											<i class="uk-icon-crop"></i>&nbsp;
											<?php Text::e('btn_copy_resized'); ?>
										</a>
									</li>
									<?php } ?>
									<li>
										<a href="#" data-am-clipboard="<?php echo Core\Str::stripStart($file, AM_BASE_DIR); ?>">
											<i class="uk-icon-link"></i>&nbsp;
											<?php Text::e('btn_copy_url_clipboard'); ?>
										</a>
									</li>
								</ul>
							</div>
						</div>
						<label 
						class="am-toggle-checkbox am-panel-bottom-right" 
						data-am-toggle="#<?php echo $id; ?>"
						>
							<input type="checkbox" name="delete[]" value="<?php echo basename($file); ?>" />
						</label>
					</div>
				</div>	
			</li>
			<?php } ?> 		
		</ul>
		
		<!-- Copy Resized Modal -->
		<div id="am-copy-resized-modal" class="uk-modal" data-am-url="<?php echo $url; ?>">
			<div class="uk-modal-dialog">
				<div class="uk-modal-header">
					<?php Text::e('btn_copy_resized'); ?>
					<a href="#" class="uk-modal-close uk-close"></a>
				</div>
				<div class="uk-form uk-form-stacked">
					<input 
					id="am-copy-resized-filename"
					class="uk-form-controls uk-form-large uk-width-1-1" 
					type="text" 
					value="" 
					disabled 
					readonly 
					data-am-watch-exclude
					/>
					<ul class="uk-grid uk-grid-width-1-2">
						<li>
							<label 
							for="am-copy-resized-width" 
							class="uk-form-label uk-margin-small-top"
							>
								<?php Text::e('image_width_px'); ?>
							</label>
							<input 
							id="am-copy-resized-width" 
							class="uk-form-controls uk-width-1-1"
							type="number" 
							step="10"
							name="width" 
							value=""
							data-am-watch-exclude
							>
						</li>
						<li>
							<label 
							for="am-copy-resized-height" 
							class="uk-form-label uk-margin-small-top"
							>
								<?php Text::e('image_height_px'); ?>
							</label>
							<input 
							id="am-copy-resized-height" 
							class="uk-form-controls uk-width-1-1"
							type="number" 
							step="10" 
							name="height" 
							value=""
							data-am-watch-exclude
							>
						</li>
					</ul>
					<div class="uk-form-row uk-margin-small-top">
						<label class="am-toggle-switch uk-button" data-am-toggle>
							<?php Text::e('image_crop'); ?>
							<input 
							id="am-copy-resized-crop"
							type="checkbox" 
							name="crop" 
							value="" 
							data-am-watch-exclude
							>
						</label>
					</div>
				</div>
				<div class="uk-modal-footer uk-text-right">
					<button type="button" class="uk-modal-close uk-button">
						<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php Text::e('btn_close'); ?>
					</button>
					<button id="am-copy-resized-submit" type="button" class="uk-button uk-button-success">
						<i class="uk-icon-check"></i>&nbsp;&nbsp;<?php Text::e('btn_ok'); ?>
					</button>
				</div>
			</div>
		</div>
		
		<!-- Edit Modal -->
		<div id="am-edit-file-info-modal" class="uk-modal" data-am-url="<?php echo $url; ?>">
			<div class="uk-modal-dialog uk-modal-dialog-blank uk-text-center">
				<a href="#" class="uk-modal-close uk-close"></a>
				<div class="am-files-modal-container">
					<a href="#" class="am-files-modal-preview uk-modal-close">
						<img id="am-edit-file-info-img" src="" />
						<div id="am-edit-file-info-icon" data-am-extension=""></div>
					</a>
					<div class="am-files-modal-info">
						<div class="uk-form uk-form-stacked">
							<input id="am-edit-file-info-old-name" type="hidden" name="old-name" />	
							<div class="uk-form-row">
								<label for="am-edit-file-info-new-name" class="uk-form-label uk-margin-top-remove">
									<?php Text::e('file_name'); ?>
								</label>
								<input 
								id="am-edit-file-info-new-name" 
								name="new-name" 
								class="uk-form-controls uk-form-large uk-width-1-1" 
								data-am-watch-exclude 
								/>
							</div>
							<div class="uk-form-row">
								<label for="am-edit-file-info-caption" class="uk-form-label">
									<?php Text::e('file_caption'); ?>
								</label>
								<textarea 
								id="am-edit-file-info-caption" 
								name="caption" 
								class="uk-form-controls uk-width-1-1" 
								data-am-watch-exclude
								></textarea>
							</div>
						</div>
						<div class="uk-margin-top uk-text-right">
							<button type="button" class="uk-modal-close uk-button">
								<span class="uk-hidden-small"><i class="uk-icon-close"></i>&nbsp;</span>
								<?php Text::e('btn_close'); ?>
							</button>
							<a id="am-edit-file-info-download" class="uk-button" download>
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

		<a 
		href="#am-upload-modal" 
		class="uk-button uk-button-success uk-button-large" 
		data-uk-modal="{bgclose: false, keyboard: false}"
		>
			<i class="uk-icon-upload"></i>&nbsp;&nbsp;<?php Text::e('btn_upload'); ?>
		</a>
	
<?php } ?> 

		<!-- Upload Modal -->
		<div 
		id="am-upload-modal" 
		class="uk-modal" 
		data-am-url="<?php echo $url; ?>" 
		data-am-dropzone-text="<?php Text::e('dropzone'); ?>" 
		data-am-browse-text="<?php Text::e('btn_browse'); ?>"
		>
			<div class="uk-modal-dialog">
				<div class="uk-modal-header">
					<?php Text::e('btn_upload'); ?>
					<button type="button" class="uk-modal-close uk-close"></button>
				</div>
				<div id="am-upload-container"></div>
				<div class="uk-modal-footer uk-text-right">
					<button type="button" class="uk-modal-close uk-button">
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