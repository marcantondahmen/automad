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
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	AJAX Upload handler.
 */


$output = array();
$output['debug'] = $_POST + $_FILES;


// Set path.
// If an URL is also posted, use that URL's page path. Without any URL, the /shared path is used.
if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->collection)) {
	
	$Page = $this->collection[$_POST['url']];
	$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
	
} else {
	
	$path = AM_BASE_DIR . AM_DIR_SHARED . '/';
	
}


// Move uploaded files
if (isset($_FILES['files']['name'])) {
	
	// Check if upload destination is writable.
	if (is_writable($path)) {
	
		$errors = array();

		// In case the $_FILES array consists of multiple files (IE uploads!).
		for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
	
			// Check if file has a valid filename (allowed file type).
			if (\Automad\Core\Parse::isFileName($_FILES['files']['name'][$i])) {
		
				$newFile = $path . \Automad\Core\String::sanitize($_FILES['files']['name'][$i]);
				move_uploaded_file($_FILES['files']['tmp_name'][$i], $newFile);
		
			} else {
		
				$errors[] = Text::get('error_file_format') . ' <strong>' . pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION) . '</strong>';
		
			}
	
		}

		// Clear cache to update galleries and sliders.
		$Cache = new \Automad\Core\Cache();
		$Cache->clear();
		
		if ($errors) {
			$output['error'] = implode('<br />', $errors);
		} 

	} else {
		
		$output['error'] = Text::get('error_permission') . '<p>' . $path . '</p>';
				
	}

}


echo json_encode($output);


?>