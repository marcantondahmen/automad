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
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\GUI\Controllers;

use Automad\GUI\Components\Alert\Danger;
use Automad\GUI\Components\Alert\Success;
use Automad\GUI\Components\Layout\SystemUpdate;
use Automad\GUI\Utils\Text;
use Automad\System\Update;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The system controller.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class System {


	/**
	 *	System updates.
	 *
	 *	@return array the $output array
	 */

	public static function update() {

		$output = array();

		// To prevent accidental updates within the development repository, exit updater in case the base directoy contains "/automad-dev".
		if (strpos(AM_BASE_DIR, '/automad-dev') !== false) {

			$output['html'] = Danger::render("Can't run updates within the development repository!");
			
		} else {

			// Test if server supports all required functions/extensions.
			if (Update::supported()) {

				if (!empty($_POST['update'])) {

					$output = Update::run();

				} else {

					if ($version = Update::getVersion()) {

						// Check if an the current installation is outdated.
						if (version_compare(AM_VERSION, $version, '<')) {

							$output['html'] = SystemUpdate::render($version);

						} else {

							$output['html'] = Success::render(
								Text::get('sys_update_not_required') . ' ' . 
								Text::get('sys_update_current_version') . ' ' . 
								AM_VERSION
							);

						}

					} else {

						$output['html'] = Danger::render(Text::get('error_update_connection'));

					}

				}

			} else {

				$output['html'] = Danger::render(Text::get('error_update_not_supported'));
			
			}
		
		}

		return $output;

	}


}