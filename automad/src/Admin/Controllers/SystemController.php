<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Controllers;

use Automad\UI\Components\Alert\Danger;
use Automad\UI\Components\Alert\Success;
use Automad\UI\Components\Layout\SystemUpdate;
use Automad\UI\Utils\Text;
use Automad\System\Update;
use Automad\UI\Response;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The system controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SystemController {
	/**
	 * System updates.
	 *
	 * @return Response the response object
	 */
	public static function update() {
		$Response = new Response();

		// To prevent accidental updates within the development repository, exit updater in case the base directoy contains "/automad-dev".
		if (strpos(AM_BASE_DIR, '/automad-dev') !== false) {
			$Response->setHtml(Danger::render("Can't run updates within the development repository!"));
		} else {
			// Test if server supports all required functions/extensions.
			if (Update::supported()) {
				if (!empty($_POST['update'])) {
					$Response = Update::run();
				} else {
					if ($version = Update::getVersion()) {
						// Check if an the current installation is outdated.
						if (version_compare(AM_VERSION, $version, '<')) {
							$Response->setHtml(SystemUpdate::render($version));
						} else {
							$Response->setHtml(
								Success::render(
									Text::get('sys_update_not_required') . ' ' .
									Text::get('sys_update_current_version') . ' ' .
									AM_VERSION
								)
							);
						}
					} else {
						$Response->setHtml(Danger::render(Text::get('error_update_connection')));
					}
				}
			} else {
				$Response->setHtml(Danger::render(Text::get('error_update_not_supported')));
			}
		}

		return $Response;
	}
}
