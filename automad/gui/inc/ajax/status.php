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
 *	Copyright (c) 2014-2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;
use Automad\System as System;



defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	Return the current status of a config item.
 */


$output = array();


if (isset($_POST['item'])) {
	
	$item = $_POST['item'];
	
	if ($item == 'cache') {
		
		if (AM_CACHE_ENABLED) {
			$output['status'] = '<i class="uk-icon-toggle-on uk-icon-justify"></i>&nbsp;&nbsp;' .
								Text::get('sys_status_cache_enabled');
		} else {
			$output['status'] = '<i class="uk-icon-toggle-off uk-icon-justify"></i>&nbsp;&nbsp;' .
								Text::get('sys_status_cache_disabled');
		}
		
	}
	
	if ($item == 'debug') {
		
		if (AM_DEBUG_ENABLED) {
			$output['status'] = '<i class="uk-icon-toggle-on uk-icon-justify"></i>&nbsp;&nbsp;' .
								Text::get('sys_status_debug_enabled');
		} else {
			$output['status'] = '<i class="uk-icon-toggle-off uk-icon-justify"></i>&nbsp;&nbsp;' .
								Text::get('sys_status_debug_disabled');
		}
		
	}
	
	if ($item == 'update') {
		
		$updateVersion = System\Update::getVersion();
		
		if (version_compare(AM_VERSION, $updateVersion, '<')) {
			$output['status'] = '<i class="uk-icon-refresh uk-icon-justify"></i>&nbsp;&nbsp;' .
								Text::get('sys_status_update_available') . 
								'&nbsp;&nbsp;<span class="uk-badge uk-badge-success">' . $updateVersion . '</span>';
		} else {
			$output['status'] = '<i class="uk-icon-check uk-icon-justify"></i>&nbsp;&nbsp;' .
								Text::get('sys_status_update_not_available');
		}
		
	}
	
	if ($item == 'users') {
				
		$output['status'] = '<i class="uk-icon-users uk-icon-justify"></i>&nbsp;&nbsp;' . 
							Text::get('sys_user_registered') . 
							'&nbsp;&nbsp;<span class="uk-badge">' . count(Accounts::get()) . '</span>';

	}
	
}


echo json_encode($output);


?>