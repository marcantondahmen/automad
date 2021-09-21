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
 * A password reset email body.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PasswordResetEmail {
	/**
	 * Render a password reset email body.
	 *
	 * @param string $website
	 * @param string $username
	 * @param string $token
	 * @return string The rendered password reset email body
	 */
	public static function render(string $website, string $username, string $token) {
		$pStyle = 'style="font-size: 16px; line-height: 22px;"';

		return <<< HTML
				<!doctype html>
				<html>
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
						<meta name="viewport" content="width=device-width, initial-scale=1">
					</head>
					<body style="padding: 15px 0; font-family: -apple-system, BlinkMacSystemFont, helvetica, Ubuntu, roboto, noto, arial, sans-serif;">
						<table border="0" cellpadding="0" cellspacing="0" height="90%" width="95%">
							<tbody>
								<tr>
									<td width="25%"> </td>
									<td width="400px" style="min-width: 400px; max-width: 400px;">
										<h1 style="font-size: 27px; font-weight: 600;">Dear $username,</h1>
										<p $pStyle>
											a password reset has been requested for your account on "$website".
											The following token can be used to reset your password:
										</p>
										<p style="text-align: center; margin: 30px 0; border: 1px solid #e5e5e5; border-radius: 6px; font-family: Menlo, Consolas, Monaco, Liberation Mono, Lucida Console, monospace; font-size: 18px; line-height: 48px;">
											$token
										</p>
										<p $pStyle>
											In case you did not request the password reset yourself, you can ignore this message.
										</p>
										<p $pStyle>
											<b>Automad</b>
										</p>
									</td>
									<td width="25%"> </td>
								</tr>
							</tbody>
						</table>
					</body>
				</html>
				HTML;
	}
}
