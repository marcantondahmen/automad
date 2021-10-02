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
 * A password reset email body.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PasswordResetEmail extends AbstractEmailBody {
	/**
	 * Render a password reset email body.
	 *
	 * @param string $website
	 * @param string $username
	 * @param string $token
	 * @return string The rendered password reset email body
	 */
	public static function render(string $website, string $username, string $token) {
		$h1Style = self::$h1Style;
		$pStyle = self::$paragraphStyle;
		$codeStyle = self::$codeStyle;
		$Text = Text::getObject();
		$textTop = str_replace('{}', "<b>$website</b>", Text::get('email_reset_password_text_top'));

		$content = <<< HTML
			<h1 $h1Style>$Text->email_hello $username,</h1>
			<p $pStyle>
				$textTop
			</p>
			<p $codeStyle>
				$token
			</p>
			<p $pStyle>
				$Text->email_reset_password_text_bottom
			</p>
		HTML;

		return self::body($content);
	}
}
