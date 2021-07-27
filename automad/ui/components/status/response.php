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
 * Copyright (c) 2019-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Components\Status;

use Automad\Core\Debug;
use Automad\Core\Str;
use Automad\System\Update;
use Automad\UI\Utils\Text;
use Automad\UI\Controllers\Headless;
use Automad\UI\Controllers\PackageManager;
use Automad\UI\Models\Accounts;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The status response component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2019-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Response {
	/**
	 * 	Get the current status response of a given system item or packages.
	 *
	 * @param string $item
	 * @return array The output array with the generated status return markup
	 */
	public static function render($item) {
		Debug::log($item, 'Getting status');
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
			$output['status'] = '';
			$tooltip = Text::get('sys_status_debug_enabled');
			$tab = Str::sanitize(Text::get('sys_debug'));

			if (AM_DEBUG_ENABLED) {
				$output['status'] = <<< HTML
					<a 
					href="?view=System#$tab" 
					class="am-u-button am-u-button-danger" 
					title="$tooltip" 
					data-uk-tooltip="{pos:'bottom-right'}"
					>
						<i class="am-u-icon-bug"></i>
					</a>	
HTML;
			}
		}

		if ($item == 'headless_template') {
			$template = Str::stripStart(Headless::getTemplate(), AM_BASE_DIR);
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
			$updateVersion = Update::getVersion();

			if (version_compare(AM_VERSION, $updateVersion, '<')) {
				$output['status'] = '<i class="uk-icon-download uk-icon-justify"></i>&nbsp;&nbsp;' .
									Text::get('sys_status_update_available') .
									'&nbsp;&nbsp;<span class="uk-badge uk-badge-success">' . $updateVersion . '</span>';
			} else {
				$output['status'] = '<i class="uk-icon-check uk-icon-justify"></i>&nbsp;&nbsp;' .
									Text::get('sys_status_update_not_available');
			}
		}

		if ($item == 'update_badge') {
			$updateVersion = Update::getVersion();

			if (version_compare(AM_VERSION, $updateVersion, '<')) {
				$output['status'] = '<span class="uk-badge uk-badge-success"><i class="uk-icon-refresh"></i></span>';
			} else {
				$output['status'] = '';
			}
		}

		if ($item == 'users') {
			$output['status'] = '<i class="uk-icon-users uk-icon-justify"></i>&nbsp;&nbsp;' .
								Text::get('sys_user_registered') .
								'&nbsp;&nbsp;<span class="uk-badge">' . count(Accounts::get()) . '</span>';
		}

		if ($item == 'outdated_packages') {
			$output = PackageManager::getOutdatedPackages();

			if (!empty($output['buffer'])) {
				$data = json_decode($output['buffer']);

				if (!empty($data->installed)) {
					$count = count($data->installed);
					$output['status'] = '<span class="uk-badge uk-badge-success"><i class="uk-icon-refresh"></i>&nbsp; ' . $count . '</span>';
				} else {
					$output['status'] = '';
				}
			}
		}

		return $output;
	}
}