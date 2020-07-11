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
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
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
$url = Core\Request::post('url');

if (!array_key_exists($url, $this->getAutomad()->getCollection())) {
	$url = '';
	$modalTitle = Text::get('shared_title');
} else {
	$modalTitle = $this->getAutomad()->getPage($url)->get(AM_KEY_TITLE);
}

$path = $this->getContent()->getPathByPostUrl();

// Delete files in $_POST['delete'].
if ($delete = Core\Request::post('delete')) {	
	$output = $this->getContent()->deleteFiles($delete, $path);
}

// Get files for each allowed file type.
$files = FileSystem::globGrep($path . '*.*', '/\.(' . implode('|', Core\Parse::allowedFileTypes()) . ')$/i');

ob_start();

if ($files) { ?>
	
	<ul class="uk-grid">
		<li class="uk-width-2-3 uk-width-medium-1-1">
			<div class="uk-button-group">
				<a 
				href="#am-upload-modal" 
				class="uk-button uk-button-success" 
				data-uk-modal="{bgclose: false, keyboard: false}"
				>
					<i class="uk-icon-upload"></i>&nbsp;
					<?php Text::e('btn_upload'); ?>
				</a>	
				<a 
				href="#am-import-modal" 
				class="uk-button uk-button-success" 
				data-uk-modal
				>
					<span class="uk-hidden-small"><i class="uk-icon-cloud-download"></i>&nbsp;</span>
					<?php Text::e('btn_import'); ?>
				</a>
			</div>&nbsp;
			<button 
			class="uk-button uk-button-link uk-hidden-small" 
			data-am-submit="files"
			>
				<i class="uk-icon-remove"></i>&nbsp;
				<?php Text::e('btn_remove_selected'); ?>
			</button>
		</li>
		<li class="uk-width-1-3 uk-visible-small">
			<div class="am-icon-buttons uk-text-right">
				<button 
				class="uk-button" 
				title="<?php Text::e('btn_remove_selected'); ?>"
				data-am-submit="files"
				data-uk-tooltip
				>
					<i class="uk-icon-remove"></i>
				</button>
			</div>
		</li>
	</ul>
	
	<?php 
		echo Components\Grid\Files::render($files); 
		echo Components\Modal\CopyResized::render($url);
		echo Components\Modal\EditFileInfo::render($modalTitle, $url);
	?>
	
<?php } else { ?>

	<div class="uk-button-group">
		<a 
		href="#am-upload-modal" 
		class="uk-button uk-button-success uk-button-large" 
		data-uk-modal="{bgclose: false, keyboard: false}"
		>
			<i class="uk-icon-upload"></i>&nbsp;&nbsp;<?php Text::e('btn_upload'); ?>
		</a>
		<a 
		href="#am-import-modal" 
		class="uk-button uk-button-success uk-button-large" 
		data-uk-modal
		>
			<i class="uk-icon-cloud-download"></i>&nbsp;
			<?php Text::e('btn_import'); ?>
		</a>
	</div>
	
<?php } 

echo Components\Modal\Upload::render($url);
echo Components\Modal\Import::render($url);

$output['html'] = ob_get_contents();
ob_end_clean();

$this->jsonOutput($output);

?>