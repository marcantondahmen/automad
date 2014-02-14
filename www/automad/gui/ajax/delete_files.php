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


if (isset($_POST['delete'])) {

	$html = '<div class="item text bg">Successfully deleted the following files:<br /><b>';

	foreach ($_POST['delete'] as $file) {

		// Verify, that the current file is located below Automad's root directory for security.
		if (strpos(realpath($file), AM_BASE_DIR) !== false) {
		
			if (is_writable(dirname($file))) {
			
				if (unlink($file)) {
					$html .= basename($file) . '<br />';
				}
				
			}
		
		} else {
			
			die('<div class="item bg text"><b>' . realpath($file) . '</b> is not a valid file!</div>');
			
		}
		
	}

	$html .= '</b></div>';

	echo $html;

}


?>