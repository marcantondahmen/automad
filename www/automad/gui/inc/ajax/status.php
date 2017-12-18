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
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	Return the current status of a config item.
 */


$output = array();


if (isset($_POST['item'])) {
	
	$item = $_POST['item'];
	
	if ($item == 'cache') {
		
		if (AM_CACHE_ENABLED) {
			$output['status'] = $this->Html->status(
						'uk-icon-rocket',
						'', 
						'uk-icon-toggle-on',
						Text::get('sys_status_cache_enabled')
					);
		} else {
			$output['status'] = $this->Html->status(
						'uk-icon-rocket',
						'',
						'uk-icon-toggle-off',
						Text::get('sys_status_cache_disabled')
					);
		}
		
	}
	
	if ($item == 'debug') {
		
		if (AM_DEBUG_ENABLED) {
			$output['status'] = $this->Html->status(
						'uk-icon-bug',
						'',
						'uk-icon-toggle-on',
						Text::get('sys_status_debug_enabled')
					);
		} else {
			$output['status'] = $this->Html->status(
						'uk-icon-bug',
						'',
						'uk-icon-toggle-off',
						Text::get('sys_status_debug_disabled')
					);
		}
		
	}
	
	if ($item == 'update') {
		
		$updateVersion = Update::getVersion();
		
		if (version_compare(AM_VERSION, $updateVersion, '<')) {
			$output['status'] = $this->Html->status(
						'uk-icon-code-fork',
						AM_VERSION,
						'uk-icon-refresh',
						'<span class="uk-badge">' . Text::get('sys_status_update_available') . '&nbsp;' . $updateVersion . '</span>'
					);		
		} else {
			$output['status'] = $this->Html->status(
						'uk-icon-code-fork',
						AM_VERSION,
						'uk-icon-check',
						Text::get('sys_status_update_not_available')
					);
		}
		
	}
	
	if ($item == 'users') {
				
		$output['status'] = '<i class="uk-icon-users uk-icon-justify"></i>&nbsp;&nbsp;' . count(Accounts::get()) . ' ' . Text::get('sys_user_registered');

	}
	
}


echo json_encode($output);


?>