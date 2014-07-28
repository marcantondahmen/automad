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


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	Rename a file.
 */


$output = array();


// Get correct path of file by the posted URL. For security reasons the file path gets build here and not on the client side.
if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->collection)) {
	
	$url = $_POST['url'];
	$Page = $this->collection[$url];
	$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
	
} else {
	
	$url = '';
	$path = AM_BASE_DIR . AM_DIR_SHARED . '/';
	
}


if (isset($_POST['old-name']) && isset($_POST['new-name'])) {
	
	if ($_POST['new-name']) {
		
		if ($_POST['new-name'] != $_POST['old-name']) {
			
			$oldFile = $path . basename($_POST['old-name']);
			$newFile = $path . Parse::sanitize(basename($_POST['new-name']));
			
			if (is_writable($path) && is_writable($oldFile)) {
				
				if (!file_exists($newFile)) {
					rename($oldFile, $newFile);
				} else {
					$output['error'] = '"' . $newFile . '" ' . $this->tb['error_existing'];
				}
				
			} else {
				$output['error'] = $this->tb['error_permission'];
			}
			
		}
		
	} else {
		$output['error'] = $this->tb['error_filename'];
	}
	
} else {
	$output['error'] = $this->tb['error_form'];
}


// Echo JSON
echo json_encode($output);


?>