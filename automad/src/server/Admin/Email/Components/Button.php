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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Admin\Email\Components;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * An email button component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Button {
	/**
	 * Render the email button.
	 *
	 * @param string $text
	 * @param string $href
	 * @return string
	 */
	public static function render(string $text, string $href): string {
		return <<<HTML
			<div
				style="
					text-align: left;
					padding: 16px 24px 16px 24px;
				"
			>
				<a
					href="$href"
					style="
						color: #ffffff;
						font-size: 16px;
						font-weight: bold;
						background-color: #202327;
						border-radius: 4px;
						display: inline-block;
						padding: 10px 26px;
						text-decoration: none;
					"
					target="_blank"
					><span
						><!--[if mso
							]><i
								style="
									letter-spacing: 20px;
									mso-font-width: -100%;
									mso-text-raise: 30;
								"
								hidden
								>&nbsp;</i
							><!
						[endif]--></span
					><span>$text</span
					><span
						><!--[if mso
							]><i
								style="
									letter-spacing: 20px;
									mso-font-width: -100%;
								"
								hidden
								>&nbsp;</i
							><!
						[endif]--></span
					></a
				>
			</div>
			HTML;
	}
}
