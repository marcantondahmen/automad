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
use Automad\Admin\Email\Components\Button;
use Automad\Admin\Email\Components\Heading;
use Automad\Admin\Email\Components\Paragraph;
use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * An invitation email template.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class InvitationEmail {
	/**
	 * Render an invitation email body.
	 *
	 * @param string $username
	 * @return string The rendered invitation email body
	 */
	public static function render(string $username): string {
		$Text = Text::getObject();
		$website = $_SERVER['SCRIPT_NAME'] ?? AM_BASE_URL;

		return Body::render(
			array(
				Heading::render("$Text->emailHello $username"),
				Paragraph::render(str_replace(
					array('{1}', '{2}'),
					array("<b>$website</b>", "<b>«{$username}»</b>"),
					Text::get('emailInviteText')
				)),
				Button::render(
					$Text->emailInviteButton,
					AM_SERVER . AM_BASE_INDEX . AM_PAGE_DASHBOARD . '/password?action=create&username=' . urlencode($username)
				),
				Paragraph::render($Text->emailAutomatic)
			)
		);
	}
}
