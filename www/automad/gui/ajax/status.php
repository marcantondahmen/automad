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
 *	Return the current status of a config item.
 */


$output = array();


if (isset($_POST['item'])) {
	
	$item = $_POST['item'];
	
	if ($item == 'cache') {
		
		if (AM_CACHE_ENABLED) {
			$output['status'] = '<span class="text-success"><span class="glyphicon glyphicon-ok"></span> Page Caching is Enabled</span>';
		} else {
			$output['status'] = '<span class="text-muted"><span class="glyphicon glyphicon-ban-circle"></span> Page Caching is Disabled</span>';
		}
		
	}
	
	if ($item == 'debug') {
		
		if (AM_DEBUG_ENABLED) {
			$output['status'] = '<span class="text-success"><span class="glyphicon glyphicon-ok"></span> Debug Mode is Enabled</span>';
		} else {
			$output['status'] = '<span class="text-muted"><span class="glyphicon glyphicon-ban-circle"></span> Debug Mode is Disabled</span>';
		}
		
	}
	
	if ($item == 'users') {
		
		$accounts = unserialize(file_get_contents(AM_FILE_ACCOUNTS));
		
		if (count($accounts) == 1) {
			$output['status'] = '<span class="badge">1</span> Registered User';
		} else {
			$output['status'] = '<span class="badge">' . count($accounts) . '</span> Registered Users';
		}
		
	}
	
}


echo json_encode($output);


?>