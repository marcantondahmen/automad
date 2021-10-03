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

use Automad\UI\Utils\Text;

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
		$codeStyle = self::$codeStyle;
		$Text = Text::getObject();
		$inviteText = str_replace('{}', "<b>$website</b>", Text::get('email_invite_text'));

		$content = <<< HTML
			<h1 $h1Style>$Text->email_hello $username,</h1>
			<p $pStyle>
				$inviteText
			</p>
			<p $codeStyle>
				$Text->sys_user_name: $username
			</p>
			<p $pStyle>
				<a 
				href="$link" 
				style="
					display: block;
					text-align: center; 
					margin: 0 0 20px 0; 
					color: #ffffff;
					background-color: #121212;
					border-radius: 6px; 
					text-decoration: none;
					font-size: 18px; 
					line-height: 48px;
				">
					$Text->email_invite_button
				</a>
			</p>
		HTML;

		return self::body($content);
	}
}
