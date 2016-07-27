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
	$Page = $this->collection[$url];
	$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
	
} else {
	
	$url = '';
	$path = AM_BASE_DIR . AM_DIR_SHARED . '/';
	
}


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
	
		<div class="uk-text-right uk-margin-bottom">
			<a href="#automad-upload-modal" class="uk-button" data-uk-modal="{bgclose: false, keyboard: false}">
				<i class="uk-icon-upload"></i>&nbsp;&nbsp;<?php echo Text::get('btn_upload'); ?>
			</a>
			<button class="uk-button uk-button-danger" data-automad-submit="files">
				<i class="uk-icon-trash"></i>&nbsp;&nbsp;<?php echo Text::get('btn_remove_selected'); ?>
			</button>
		</div>
	
		<?php 
		
		sort($files);

		foreach ($files as $file) { 
			
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			
			if (in_array(strtolower($extension), $imageTypes)) { 

				$img = new \Automad\Core\Image($file, 90, 90, true);
				$info = '<div class="uk-text-small">' . 
					$img->originalWidth . ' <i class="uk-icon-times"></i> ' . $img->originalHeight . 
					'</div>';
				$icon = '<img src="' . AM_BASE_URL . $img->file . '" width="' . $img->width . '" height="' . $img->height . '" />';
				
			} else {
				
				$info = '';
				$icon = '<div class="uk-vertical-align-middle uk-text-muted"><i class="uk-icon-file-o"></i><br /><span class="automad-files-extension">' . $extension . '</span></div>';
				
			}
			
		?>
	
		<div class="uk-panel uk-panel-box uk-margin-small-top">	
			<div class="automad-files-icon uk-border-rounded uk-overflow-hidden">
				<a class="uk-vertical-align uk-text-center" href="<?php echo str_replace(AM_BASE_DIR, AM_BASE_URL, $file); ?>" target="_blank" title="Download">
					<?php echo $icon; ?>
				</a>
			</div>
			<div class="automad-files-info">
				<div class="uk-panel-title uk-text-truncate">
					<a class="uk-link-muted" href="#automad-rename-file-modal" title="<?php echo Text::get('btn_rename_file'); ?>" data-uk-modal data-automad-file="<?php echo basename($file); ?>">
						<?php echo basename($file) ?>&nbsp;&nbsp;<i class="uk-icon-pencil"></i>
					</a>
				</div>
				<div class="uk-text-small uk-text-truncate">
					<?php echo date('M j, Y H:i', filemtime($file)); ?>
				</div>
				<div class="uk-text-small uk-text-truncate">
					<a class="uk-link-muted" href="<?php echo str_replace(AM_BASE_DIR, AM_BASE_URL, $file); ?>" target="_blank">
						<?php echo str_replace(AM_BASE_DIR, '', $file) ?>
					</a>
				</div>
				<?php echo $info; ?>   
			</div>
			<div class="uk-panel-badge">
				<label data-automad-toggle>
					<input type="checkbox" name="delete[]" value="<?php echo basename($file); ?>" />
				</label>
			</div>	
		</div>
				
		<?php } ?> 
		
		<!-- Rename File Modal -->
		<div id="automad-rename-file-modal" class="uk-modal" data-automad-url="<?php echo $url; ?>">
			<div class="uk-modal-dialog">
				<div class="uk-modal-header">
					<?php echo Text::get('btn_rename_file'); ?>
				</div>
				<!-- Input fields get created by JS -->
				<div class="uk-modal-footer uk-text-right">
					<button type="button" class="uk-modal-close uk-button">
						<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php echo Text::get('btn_close'); ?>
					</button>
					<button id="automad-rename-file-submit" type="button" class="uk-button uk-button-primary">
						<i class="uk-icon-plus"></i>&nbsp;&nbsp;<?php echo Text::get('btn_rename_file'); ?>
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