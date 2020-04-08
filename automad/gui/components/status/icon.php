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
 *	Copyright (c) 2019-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI\Components\Status;
use Automad\Core as Core;
use Automad\System as System;
use Automad\GUI\Text as Text;
use Automad\GUI\Accounts as Accounts;
use Automad\GUI\Headless as Headless;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The status icon component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2019-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Icon {

	
	/**
	 * 	Get the current status icon of a given system setting.
	 * 	
	 * 	@param string $item
	 * 	@return array The output array with the generated status return markup
	 */

	public static function render($item) {

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
		
		if ($item == 'debug') {
			
			if (AM_DEBUG_ENABLED) {
				$output['status'] = '<i class="uk-icon-toggle-on uk-icon-justify"></i>&nbsp;&nbsp;' .
									Text::get('sys_status_debug_enabled');
			} else {
				$output['status'] = '<i class="uk-icon-toggle-off uk-icon-justify"></i>&nbsp;&nbsp;' .
									Text::get('sys_status_debug_disabled');
			}
			
		}
		
		if ($item == 'headless_template') {

			$template = Core\Str::stripStart(Headless::getTemplate(), AM_BASE_DIR);
			$badge = '';

			if ($template != AM_HEADLESS_TEMPLATE) {
				$badge = ' uk-badge-success';
			} 

			$output['status'] = '<span class="uk-badge uk-badge-notification uk-margin-top-remove' . $badge . '">' . 
								'<i class="uk-icon-file-text"></i>&nbsp&nbsp;' . 
				            	trim($template, '\\/') . 
				                '</span>';

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