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
 *	Copyright (c) 2014-2017 by Marc Anton Dahmen
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
			$output['status'] = '<a href="?context=system_settings#0" class="uk-button uk-button-large uk-button-success uk-width-1-1"><i class="uk-icon-check"></i>&nbsp;&nbsp;' . 
					    Text::get('sys_status_cache_enabled') . 
					    '</a>';
		} else {
			$output['status'] = '<a href="?context=system_settings#0" class="uk-button uk-button-large uk-width-1-1"><i class="uk-icon-times"></i>&nbsp;&nbsp;' . 
					    Text::get('sys_status_cache_disabled') . 
					    '</a>';
		}
		
	}
	
	if ($item == 'debug') {
		
		if (AM_DEBUG_ENABLED) {
			$output['status'] = '<a href="?context=system_settings#3" class="uk-button uk-button-large uk-button-success uk-width-1-1"><i class="uk-icon-check"></i>&nbsp;&nbsp;' . 
					    Text::get('sys_status_debug_enabled') . 
					    '</a>';
		} else {
			$output['status'] = '<a href="?context=system_settings#3" class="uk-button uk-button-large uk-width-1-1"><i class="uk-icon-times"></i>&nbsp;&nbsp;' . 
					    Text::get('sys_status_debug_disabled') . 
					    '</a>';
		}
		
	}
	
	if ($item == 'users') {
				
		$output['status'] = '<i class="uk-icon-users uk-icon-justify"></i>&nbsp;&nbsp;' . count(Accounts::get()) . ' ' . Text::get('sys_user_registered');

	}
	
}


echo json_encode($output);


?>