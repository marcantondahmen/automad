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
use Automad\UI\Controllers\HeadlessController;
use Automad\UI\Controllers\PackageManagerController;
use Automad\UI\Models\UserCollectionModel;
use Automad\UI\Response as UIResponse;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\URLHashes;

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
	 * Get the current status response of a given system item or packages.
	 *
	 * @param string $item
	 * @return UIResponse the response object
	 */
	public static function render(string $item) {
		Debug::log($item, 'Getting status');
		$Response = new UIResponse();

		if ($item == 'cache') {
			if (AM_CACHE_ENABLED) {
				$Response->setStatus(
					'<i class="uk-icon-toggle-on uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_cache_enabled')
				);
			} else {
				$Response->setStatus(
					'<i class="uk-icon-toggle-off uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_cache_disabled')
				);
			}
		}

		if ($item == 'feed') {
			if (AM_FEED_ENABLED) {
				$Response->setStatus(
					'<i class="uk-icon-toggle-on uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_feed_enabled')
				);
			} else {
				$Response->setStatus(
					'<i class="uk-icon-toggle-off uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_feed_disabled')
				);
			}
		}

		if ($item == 'debug') {
			if (AM_DEBUG_ENABLED) {
				$Response->setStatus(
					'<i class="uk-icon-toggle-on uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_debug_enabled')
				);
			} else {
				$Response->setStatus(
					'<i class="uk-icon-toggle-off uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_debug_disabled')
				);
			}
		}

		if ($item == 'debug_navbar') {
			$Response->setStatus('<span></span>');
			$tooltip = Text::get('sys_status_debug_enabled');
			$tab = URLHashes::get()->system->debug;

			if (AM_DEBUG_ENABLED) {
				$html = <<< HTML
					<a 
					href="?view=System#$tab" 
					class="am-u-button am-u-button-danger" 
					title="$tooltip" 
					data-uk-tooltip="{pos:'bottom-right'}"
					>
						<i class="am-u-icon-bug"></i>
					</a>
				HTML;

				$Response->setStatus($html);
			}
		}

		if ($item == 'headless') {
			if (AM_HEADLESS_ENABLED) {
				$Response->setStatus(
					'<i class="uk-icon-toggle-on uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_headless_enabled')
				);
			} else {
				$Response->setStatus(
					'<i class="uk-icon-toggle-off uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_headless_disabled')
				);
			}
		}

		if ($item == 'headless_template') {
			$template = Str::stripStart(HeadlessController::getTemplate(), AM_BASE_DIR);
			$badge = '';

			if ($template != AM_HEADLESS_TEMPLATE) {
				$badge = ' uk-badge-success';
			}

			$Response->setStatus(
				'<span class="uk-badge uk-badge-notification uk-margin-top-remove' . $badge . '">' .
				'<i class="uk-icon-file-text"></i>&nbsp&nbsp;' .
				trim($template, '\\/') .
				'</span>'
			);
		}

		if ($item == 'update') {
			$updateVersion = Update::getVersion();

			if (version_compare(AM_VERSION, $updateVersion, '<')) {
				$Response->setStatus(
					'<i class="uk-icon-download uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_update_available') .
					'&nbsp;&nbsp;<span class="uk-badge uk-badge-success">' . $updateVersion . '</span>'
				);
			} else {
				$Response->setStatus(
					'<i class="uk-icon-check uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_status_update_not_available')
				);
			}
		}

		if ($item == 'update_badge') {
			$updateVersion = Update::getVersion();

			if (version_compare(AM_VERSION, $updateVersion, '<')) {
				$Response->setStatus(
					'<span class="uk-badge uk-badge-success"><i class="uk-icon-refresh"></i></span>'
				);
			}
		}

		if ($item == 'users') {
			$UserCollectionModel = new UserCollectionModel();

			$Response->setStatus(
				Text::get('sys_user_registered') .
				'&nbsp;&nbsp;<span class="uk-badge">' . count($UserCollectionModel->getCollection()) . '</span>'
			);
		}

		if ($item == 'outdated_packages') {
			$Response = PackageManagerController::getOutdatedPackages();

			if ($Response->getBuffer()) {
				$data = json_decode($Response->getBuffer());

				if (!empty($data->installed)) {
					$count = count($data->installed);
					$Response->setStatus(
						'<span class="uk-badge uk-badge-success"><i class="uk-icon-refresh"></i>&nbsp ' .
						$count . '</span>'
					);
				}
			}
		}

		return $Response;
	}
}
