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
 *	Copyright (c) 2019 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;
use Automad\System as System;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Status class provides all methods related to the status of the system config. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2019 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Status {

	/**
	 *  Create a status button for an AJAX status request with loading animation.
	 *      
	 *  @param string $status
	 *  @param string $tab
	 *  @return string The HTML for the status button
	 */

	public static function button($status, $tab) {
		
		return	'<a '.
		 		'href="?context=system_settings#' . $tab . '" ' .
				'class="uk-button uk-button-large uk-width-1-1 uk-text-left" ' .
				'data-am-status="' . $status . '"' .
		 		'>' .
					'<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-justify"></i>&nbsp;&nbsp;' . 
					Text::get('btn_getting_data') .
				'</a>';
				
	}


	/**
	 * 	Get the current status of a given system setting.
	 * 	
	 * 	@param string $item
	 * 	@return array The output array with the generated status return markup
	 */

	public static function get($item) {

		Core\Debug::log($item, 'Getting status');
		$output = array();

		if ($item == 'cache') {
				
			if (AM_CACHE_ENABLED) {
				$output['status'] = '<i class="uk-icon-toggle-on uk-icon-justify"></i>&nbsp;&nbsp;' .
									Text::get('sys_status_cache_enabled');
			} else {
				$output['status'] = '<i class="uk-icon-toggle-off uk-icon-justify"></i>&nbsp;&nbsp;' .
									Text::get('sys_status_cache_disabled');
			}
			
		}
		
		if ($item == 'headless') {
			
			// The AJAX request for the headless icon is just used to 
			// return the output of the Logo::headless() method. 
			// The actual test whether the headless mode is enabled is done
			// already by that method.
			$output['status'] = Logo::headless();
			
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

		return $output;

	}


}