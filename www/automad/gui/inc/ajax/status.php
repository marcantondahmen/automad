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
 *	Copyright (c) 2014-2016 by Marc Anton Dahmen
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
			$output['status'] = Text::get('sys_cache') . '&nbsp;&nbsp;<span class="uk-badge uk-badge-notification uk-badge-success">' . Text::get('sys_status_enabled') . '</span>';
		} else {
			$output['status'] = Text::get('sys_cache') . '&nbsp;&nbsp;<span class="uk-badge uk-badge-notification uk-badge-danger">' . Text::get('sys_status_disabled') . '</span>';
		}
		
	}
	
	if ($item == 'debug') {
		
		if (AM_DEBUG_ENABLED) {
			$output['status'] = Text::get('sys_debug') . '&nbsp;&nbsp;<span class="uk-badge uk-badge-notification uk-badge-success">' . Text::get('sys_status_enabled') . '</span>';
		} else {
			$output['status'] = Text::get('sys_debug') . '&nbsp;&nbsp;<span class="uk-badge uk-badge-notification uk-badge-danger">' . Text::get('sys_status_disabled') . '</span>';
		}
		
	}
	
	if ($item == 'users') {
				
		$output['status'] = '<i class="uk-icon-user"></i>&nbsp;&nbsp;' . Text::get('sys_user_registered') . '&nbsp;&nbsp;<span class="uk-badge uk-badge-notification">' . count(Accounts::get()) . '</span>';

	}
	
}


echo json_encode($output);


?>