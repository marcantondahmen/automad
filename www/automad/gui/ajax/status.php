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
			$output['status'] = '<span class="text-success"><span class="glyphicon glyphicon-ok"></span> ' . $this->tb['sys_cache_enabled'] . '</span>';
		} else {
			$output['status'] = '<span class="text-muted"><span class="glyphicon glyphicon-ban-circle"></span> ' . $this->tb['sys_cache_disabled'] . '</span>';
		}
		
	}
	
	if ($item == 'debug') {
		
		if (AM_DEBUG_ENABLED) {
			$output['status'] = '<span class="text-success"><span class="glyphicon glyphicon-ok"></span> ' . $this->tb['sys_debug_enabled'] . '</span>';
		} else {
			$output['status'] = '<span class="text-muted"><span class="glyphicon glyphicon-ban-circle"></span> ' . $this->tb['sys_debug_disabled'] . '</span>';
		}
		
	}
	
	if ($item == 'users') {
		
		$accounts = $this->accountsGetArray();		
		$output['status'] = '<span class="badge">' . count($accounts) . '</span> ' . $this->tb['sys_user_registered'];

	}
	
}


echo json_encode($output);


?>