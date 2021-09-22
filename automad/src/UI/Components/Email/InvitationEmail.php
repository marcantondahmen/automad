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

namespace Automad\UI\Components\Email;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * An invitation email body.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class InvitationEmail extends AbstractEmailBody {
	/**
	 * Render an invitation email body.
	 *
	 * @param string $website
	 * @param string $username
	 * @param string $link
	 * @return string The rendered invitation email body
	 */
	public static function render(string $website, string $username, string $link) {
		$h1Style = self::$h1Style;
		$pStyle = self::$paragraphStyle;

		$content = <<< HTML
			<h1 $h1Style>Welcome $username,</h1>
			<p $pStyle>
				a new user account on <b>$website</b> has been created for you.
				You can use the following link in order to create a password and finish your account setup.
			</p>
			<p $pStyle>
				<a 
				href="$link" 
				style="
					display: block;
					text-align: center; 
					margin: 5px 0 20px 0; 
					color: #ffffff;
					background-color: #1070ff;
					border-radius: 6px; 
					text-decoration: none;
					font-size: 18px; 
					line-height: 48px;
				">
					Create Password
				</a>
			</p>
		HTML;

		return self::body($content);
	}
}
