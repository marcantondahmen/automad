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
 *	AJAX Upload handler.
 */


$output = array();
$output['debug'] = $_POST + $_FILES;


// Set path
if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->collection)) {
	
	$P = $this->collection[$_POST['url']];
	$path = AM_BASE_DIR . AM_DIR_PAGES . $P->path;
	
} else {
	
	$path = AM_BASE_DIR . AM_DIR_SHARED . '/';
	
}


// Move uploaded files
if (isset($_FILES['files']['name'])) {

	$errors = array();

	// Get the allowed file types from const.php.
	$allowedFileTypes = unserialize(AM_ALLOWED_FILE_TYPES);

	// Get number of files.
	$fileCount = count($_FILES['files']['name']);

	// In case the $_FILES array consists of multiple files (IE uploads!).
	for ($i = 0; $i < $fileCount; $i++) {
	
		$extension = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);
	
		if (in_array(strtolower($extension), $allowedFileTypes)) {
		
			$newFile = $path . Parse::sanitize($_FILES['files']['name'][$i]);
			move_uploaded_file($_FILES['files']['tmp_name'][$i], $newFile);
		
		} else {
		
			$errors[] = $this->tb['error_file_format'] . ' <strong>' . $extension . '</strong>';
		
		}
	
	}

	if ($errors) {
	
		$output['error'] = implode('<br />', $errors);
		
	} 

}


echo json_encode($output);


?>