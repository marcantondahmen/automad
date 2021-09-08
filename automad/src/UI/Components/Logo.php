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

namespace Automad\UI\Components;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Logo class handles the loading of the Automad logo and the headless indicator.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2019-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Logo {
	/**
	 * Return the Automad logo and if the headless mode is enabled also the headless indicator.
	 *
	 * @return string The HTML of the logos
	 */
	public static function render() {
		$logo = file_get_contents(AM_BASE_DIR . '/automad/ui/svg/logo.svg');

		if (AM_HEADLESS_ENABLED) {
			$class = 'am-logo-container-headless';
			$headless = file_get_contents(AM_BASE_DIR . '/automad/ui/svg/headless.svg');
		} else {
			$class = 'am-logo-container';
			$headless = '';
		}

		return '<div class="' . $class . '">' . $logo . $headless . '</div>';
	}
}
