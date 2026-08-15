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
 * An account setup completion email template.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class AccountSetupEmail {
	/**
	 * Render an account setup completion email body.
	 *
	 * @param string $username
	 * @param string $code
	 * @param string $sitename
	 * @return string The rendered email body
	 */
	public static function render(string $username, string $code, string $sitename): string {
		$Text = Text::getObject();

		return Body::render(
			array(
				Heading::render("$Text->emailHello $username"),
				Paragraph::render(str_replace('{}', "<b>«{$sitename}»</b>", Text::get('emailAccountSetupTextTop'))),
				Code::render($code),
				Paragraph::render($Text->emailAutomatic)
			),
			$sitename
		);
	}
}
