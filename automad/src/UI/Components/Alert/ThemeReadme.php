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

namespace Automad\UI\Components\Alert;

use Automad\Types\Theme;
use Automad\UI\Components\Modal\Readme;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The theme readme alert component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ThemeReadme {
	/**
	 * Render a theme readme alert box.
	 *
	 * @param Theme|null $Theme
	 * @param string $id
	 * @return string The rendered alert box markup
	 */
	public static function render(?Theme $Theme = null, string $id = 'am-readme-modal') {
		if (!AM_HEADLESS_ENABLED) {
			$Text = Text::getObject();

			if ($Theme && $Theme->readme) {
				$html = Readme::render($id, $Theme->readme);
				$html .= <<< HTML
					<a href="#$id" class="am-alert-readme uk-alert" data-uk-modal>
						$Text->theme_readme_alert
					</a>
				HTML;

				return $html;
			}
		}
	}
}
