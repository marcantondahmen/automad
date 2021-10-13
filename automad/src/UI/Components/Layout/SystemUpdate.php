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

namespace Automad\UI\Components\Layout;

use Automad\Core\Parse;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The system update layout.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SystemUpdate {
	/**
	 * Render the system update layout.
	 *
	 * @param string $version
	 * @return string the rendered system update layout.
	 */
	public static function render(string $version) {
		$winAlert = '';
		$items = '';
		$fn = function ($expression) {
			return $expression;
		};

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$winAlert = <<< HTML
				<div class="uk-alert uk-alert-danger uk-margin-top uk-margin-bottom-remove">
					{$fn(Text::get('sys_update_windows_warning'))}
				</div>
			HTML;
		}

		foreach (Parse::csv(AM_UPDATE_ITEMS) as $item) {
			$items .= "<li>$item</li>";
		}

		return <<< HTML
			<div class="am-update-progress">
				<input type="hidden" name="update" value="run" />
				$winAlert
				<div class="uk-alert uk-alert-success" data-icon="&#xf021">
					{$fn(Text::get('sys_update_current_version'))} 
					<b>{$fn(AM_VERSION)}</b>.
					<br>
					{$fn(Text::get('sys_update_available'))}
					<div class="uk-margin-top">
						<button 
						type="submit" 
						class="uk-button uk-button-success uk-text-nowrap" 
						data-uk-toggle="{target:'.am-update-progress',cls:'uk-hidden'}"
						>
							<i class="uk-icon-download"></i>&nbsp;
							{$fn(Text::get('sys_update_to'))}
							<b>$version</b>
						</button>
						<a 
						href="https://automad.org/release-notes" 
						class="uk-button uk-button-link uk-hidden-small" 
						target="_blank"
						>
							<i class="uk-icon-file-text-o"></i>&nbsp;
							Release Notes
						</a>
					</div>
				</div>
				<p>{$fn(Text::get('sys_update_items'))}</p>
				<ul>$items</ul>
			</div>
			<div class="am-update-progress am-progress-panel uk-progress uk-progress-striped uk-active uk-hidden">
				<div class="uk-progress-bar" style="width: 100%;">
					{$fn(Text::get('sys_update_progress'))}
				</div>
			</div>
		HTML;
	}
}
