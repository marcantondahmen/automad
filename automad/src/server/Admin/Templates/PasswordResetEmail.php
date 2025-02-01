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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin\Templates;

use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A password reset email body.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
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
	public static function render(string $website, string $username, string $token): string {
		$h1Style = self::$h1Style;
		$pStyle = self::$paragraphStyle;
		$codeStyle = self::$codeStyle;
		$Text = Text::getObject();
		$textTop = str_replace('{}', "<b>$website</b>", Text::get('emailResetPasswordTextTop'));

		$content = <<< HTML
			<h1 $h1Style>$Text->emailHello $username,</h1>
			<p $pStyle>
				$textTop
			</p>
			<p $codeStyle>
				$token
			</p>
			<p $pStyle>
				$Text->emailResetPasswordTextBottom
			</p>
		HTML;

		return self::body($content);
	}
}
