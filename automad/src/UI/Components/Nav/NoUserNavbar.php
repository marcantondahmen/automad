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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Components\Nav;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The no user navbar component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class NoUserNavbar {
	/**
	 * Create a no user navbar.
	 *
	 * @param string $sitename
	 * @param string $title
	 * @return string The HTML for no user navbar
	 */
	public static function render(string $sitename, string $title) {
		$home = AM_BASE_INDEX . '/';

		return <<< HTML
			<div class="am-fullscreen-bar uk-display-block">
				<div class="uk-flex uk-flex-space-between uk-flex-middle uk-height-1-1">
					<div class="uk-flex-item-1 uk-text-truncate uk-margin-small-right">
						$sitename &mdash; $title
					</div>
					<a href="$home" class="am-fullscreen-bar-button">
						<i class="am-u-icon-close"></i>
					</a>
				</div>
			</div>
		HTML;
	}
}
