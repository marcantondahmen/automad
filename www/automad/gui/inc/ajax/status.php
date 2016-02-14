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
 *	Return the current status of a config item.
 */


$output = array();


if (isset($_POST['item'])) {
	
	$item = $_POST['item'];
	
	if ($item == 'cache') {
		
		if (AM_CACHE_ENABLED) {
			$output['status'] = Text::get('sys_cache') . ' <span class="badge on"><span class="glyphicon glyphicon-ok"></span> ' . Text::get('sys_status_enabled') . '</span>';
		} else {
			$output['status'] = Text::get('sys_cache') . ' <span class="badge off"><span class="glyphicon glyphicon-ban-circle"></span> ' . Text::get('sys_status_disabled') . '</span>';
		}
		
	}
	
	if ($item == 'debug') {
		
		if (AM_DEBUG_ENABLED) {
			$output['status'] = Text::get('sys_debug') . ' <span class="badge on"><span class="glyphicon glyphicon-ok"></span> ' . Text::get('sys_status_enabled') . '</span>';
		} else {
			$output['status'] = Text::get('sys_debug') . ' <span class="badge off"><span class="glyphicon glyphicon-ban-circle"></span> ' . Text::get('sys_status_disabled') . '</span>';
		}
		
	}
	
	if ($item == 'users') {
		
		$accounts = $this->accountsGetArray();		
		$output['status'] = Text::get('sys_user_registered') . ' <span class="badge on">' . count($accounts) . '</span>';

	}
	
}


echo json_encode($output);


?>