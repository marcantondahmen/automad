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
 *	Ajax upload handler.
 */


define('AUTOMAD', true);
require '../elements/base.php';


if (isset($_FILES['files']['name']) && isset($_POST['path']) && strpos(realpath($_POST['path']), AM_BASE_DIR) !== false) {

	$errors = array();

	// Get the allowed file types from const.php.
	$allowedFileTypes = unserialize(AM_ALLOWED_FILE_TYPES);

	// Get number of files.
	$fileCount = count($_FILES['files']['name']);

	// In case the $_FILES array consists of multiple files (IE uploads!).
	for ($i = 0; $i < $fileCount; $i++) {
		
		$extension = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);
		
		if (in_array(strtolower($extension), $allowedFileTypes)) {
			
			$newFile = $_POST['path'] . Core\Parse::sanitize($_FILES['files']['name'][$i]);
			move_uploaded_file($_FILES['files']['tmp_name'][$i] ,$newFile);
			
		} else {
			
			$errors[] = 'Unsupported file format (' . $_FILES['files']['name'][$i] . ')';
			
		}
		
		
	}

	if ($errors) {
		
		echo '{"status": "' . implode('<br />', $errors) . '"}';
			
	} else {
		
		echo '{"status": "Finished"}';
		
	}
	

}


?>