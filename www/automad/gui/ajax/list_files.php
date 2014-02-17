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


$output = array();


// Verify if posted path is actually an existing directory below Automad's base directory.
if ($G->isBelowBaseDir($_POST['path'])) {
	
	
	// Get the allowed file types from const.php.
	$allowedFileTypes = unserialize(AM_ALLOWED_FILE_TYPES);

	// Define image file extensions. 
	$imageTypes = array('jpg', 'png', 'gif');
		
	// Get files for each allowed file type.
	$files = array();
	
	foreach ($allowedFileTypes as $type) {	
		// Since glob is case sensitive, both, lowercase and uppercase types get searched. 	
		$files = array_merge($files, glob($_POST['path'] . '*.' . strtolower($type)));
		$files = array_merge($files, glob($_POST['path'] . '*.' . strtoupper($type)));
	}
	
	if ($files) {
	
		sort($files);
	
		$output['html'] = '<form class="item bg"><table>';
	
		// Create table row for each file.
		foreach ($files as $file) {
		
			$output['html'] .= '<tr><td><a href="' . str_replace(AM_BASE_DIR, AM_BASE_URL, $file) . '" target="_blank" title="Download" tabindex=-1>';
	
			$extension = pathinfo($file, PATHINFO_EXTENSION);
	
			if (in_array(strtolower($extension), $imageTypes)) {
				
				// Images
				$img = new Core\Image($file, 120, 120, true);
				$output['html'] .= '<img src="' . AM_BASE_URL . $img->file . '" width="' . $img->width . '" height="' . $img->height . '" />';
				$info = $img->originalWidth . 'px / ' . $img->originalHeight . 'px';	
			
			} else {
			
				// All other files
				$output['html'] .= '<div class=filetype>' . strtoupper($extension) . '</div>';
				$info = strtoupper($extension) . '-File';
			
			}
		
			$output['html'] .= '</a></td>';
			$output['html'] .= '<td>' . basename($file) . '<br />' . $info . '</td>';	
			$output['html'] .= '<td><input type="checkbox" name="delete[]" value="' . $file . '" tabindex=-1 /></td>';
			$output['html'] .= '</tr>';
		
		}
	
		$output['html'] .= '</table></form>';
		
	} else {
		
		$output['html'] = '<div class="item text bg"><b>No files found for this page!</b></div>';
		
	}
	
	
} else {
	
	$output['html'] = '<div class="item bg text">Error: "<b>' . realpath($_POST['path']) . '</b>" is not a valid path!</div>';
	
}


echo json_encode($output);


?>