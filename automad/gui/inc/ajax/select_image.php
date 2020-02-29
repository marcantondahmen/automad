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
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	Select image dialog.
 */


$output = array();


// Check if file from a specified page or the shared files will be listed and managed.
// To display a file list of a certain page, its URL has to be submitted along with the form data.
if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->getAutomad()->getCollection())) {
	$url = $_POST['url'];
} else {
	$url = '';
}

if ($url) {
	$pageFiles = FileSystem::globGrep($this->getContent()->getPathByPostUrl() . '*.*', '/\.(jpg|jpeg|gif|png)$/i');
	sort($pageFiles);
} else {
	$pageFiles = array();
}

$sharedFiles = FileSystem::globGrep(AM_BASE_DIR . AM_DIR_SHARED . '/*.*', '/\.(jpg|jpeg|gif|png)$/i');
sort($sharedFiles);

$Html = $this->getHtml();

ob_start();

?>

	<div class="uk-flex">
		<input 
		class="uk-form-controls uk-width-1-1" 
		type="text" 
		name="imageUrl"
		placeholder="URL"
		>
		<button class="uk-button uk-button-large uk-text-nowrap">
			<i class="uk-icon-link"></i>&nbsp;
			<?php Text::e('btn_link'); ?>
		</button>
	</div>

	<hr>

	<div class="am-select-image-resize">
		<p><?php Text::e('image_options'); ?></p>

		<ul class="uk-grid uk-grid-width-1-2">
			<li>
				<input 
				class="uk-form-controls uk-width-1-1" 
				type="number" 
				name="width"
				step="10"
				placeholder="<?php Text::e('image_width_px'); ?>"
				>
			</li>
			<li>
				<input 
				class="uk-form-controls uk-width-1-1" 
				type="number" 
				name="height"
				step="10"
				placeholder="<?php Text::e('image_height_px'); ?>"
				>
			</li>
		</ul>
	</div>

<?php

echo Components\Form\SelectImage::render($pageFiles, Text::get('images_page'), true);
echo Components\Form\SelectImage::render($sharedFiles, Text::get('images_shared'));

$output['html'] = ob_get_contents();
ob_end_clean();

$this->jsonOutput($output);

?>