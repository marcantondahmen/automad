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

/**
 *	Create a table of all files for a direcory for am AJAX request. 
 *	The table consists of a form including a table row for each file with a download link, a preview and a checkbox (delete).
 */


define('AUTOMAD', true);
require '../elements/base.php';


// Get the allowed file types from const.php.
$fileTypes = unserialize(AM_ALLOWED_FILE_TYPES);


// Define image file extensions. 
$imageTypes = array('jpg', 'png', 'gif');


// Verify if posted path is actually an existing directory below Automad's root directory for security.
if (strpos(realpath($_POST['path']), AM_BASE_DIR) !== false) {

	$files = array();
	
	// Get files for each allowed file type.
	foreach ($fileTypes as $type) {		
		$files = array_merge($files, glob($_POST['path'] . '*.' . $type));
	}
	
	if ($files) {
	
		sort($files);
	
		$html = '<form class="item bg"><table>';
	
		// Create table row for each file.
		foreach ($files as $file) {
		
			$html .= '<tr><td><a href="' . str_replace(AM_BASE_DIR, AM_BASE_URL, $file) . '" target="_blank" title="Download" tabindex=-1>';
	
			$extension = pathinfo($file, PATHINFO_EXTENSION);
	
			if (in_array($extension, $imageTypes)) {
				
				// Images
				$img = new Core\Image($file, 120, 120, true);
				$html .= '<img src="' . AM_BASE_URL . $img->file . '" width="' . $img->width . '" height="' . $img->height . '" />';
				$info = $img->originalWidth . 'px / ' . $img->originalHeight . 'px';	
			
			} else {
			
				// All other files
				$html .= '<div class=filetype>' . strtoupper($extension) . '</div>';
				$info = strtoupper($extension) . '-File';
			
			}
		
			$html .= '</a></td>';
			$html .= '<td>' . basename($file) . '<br />' . $info . '</td>';	
			$html .= '<td><input type="checkbox" name="delete[]" value="' . $file . '" tabindex=-1 /></td>';
			$html .= '</tr>';
		
		}
	
		$html .= '</table></form>';
	
		echo $html;
		
	} else {
		
		echo '<div class="item text bg"><b>No files found for this page!</b></div>';
		
	}
	
	
} else {
	
	die('<div class="item bg text"><b>' . realpath($_POST['path']) . '</b> is not a valid path!</div>');
	
}


?>