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
 *	Processes an AJAX request to delete the posted files.
 */


define('AUTOMAD', true);
require '../elements/base.php';


$output = array();


if (isset($_POST['delete'])) {

	$output['html'] = '<div class="item text bg">';

	foreach ($_POST['delete'] as $file) {

		// Verify, that the current file is located below Automad's root directory for security.
		if (strpos(realpath($file), AM_BASE_DIR) !== false) {
		
			if (is_writable(dirname($file))) {
			
				if (unlink($file)) {
					$output['html'] .= 'Deleted "<b>' . basename($file) . '</b>"<br />';
				}
				
			}
		
		} else {
			
			$output['html'] .= '"<b>' . realpath($file) . '</b>" is not a valid file!<br />';
			
		}
		
	}

	$output['html'] .= '</div>';

	echo json_encode($output);

}


?>