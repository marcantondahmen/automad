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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Admin\Email;

use Automad\Admin\Email\Components\Body;
use Automad\Admin\Email\Components\Code;
use Automad\Admin\Email\Components\Heading;
use Automad\Admin\Email\Components\Paragraph;
use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A password reset email template.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PasswordResetEmail {
	/**
	 * Render a password reset email body.
	 *
	 * @param string $username
	 * @param string $token
	 * @return string The rendered password reset email body
	 */
	public static function render(string $username, string $token): string {
		$Text = Text::getObject();
		$website = $_SERVER['SERVER_NAME'] ?? AM_BASE_URL;

		return Body::render(
			array(
				Heading::render("$Text->emailHello $username"),
				Paragraph::render(str_replace('{}', "<b>$website</b>", Text::get('emailResetPasswordTextTop'))),
				Code::render($token),
				Paragraph::render($Text->emailResetPasswordTextBottom),
				Paragraph::render($Text->emailAutomatic)
			)
		);
	}
}
