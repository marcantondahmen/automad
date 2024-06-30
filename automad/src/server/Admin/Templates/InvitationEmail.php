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
 * Copyright (c) 2021-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin\Templates;

use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * An invitation email body.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2024 by Marc Anton Dahmen - https://marcdahmen.de
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
	public static function render(string $website, string $username, string $link): string {
		$h1Style = self::$h1Style;
		$pStyle = self::$paragraphStyle;
		$codeStyle = self::$codeStyle;
		$Text = Text::getObject();
		$inviteText = str_replace('{}', "<b>$website</b>", Text::get('emailInviteText'));

		$content = <<< HTML
			<h1 $h1Style>$Text->emailHello $username,</h1>
			<p $pStyle>
				$inviteText
			</p>
			<p $codeStyle>
				$Text->username: $username
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
					$Text->emailInviteButton
				</a>
			</p>
		HTML;

		return self::body($content);
	}
}
