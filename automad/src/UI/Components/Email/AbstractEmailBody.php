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
 * An abstract HTML email.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
abstract class AbstractEmailBody {
	/**
	 * The code paragraph style.
	 */
	protected static $codeStyle = <<< HTML
		style="
			text-align: center; 
			margin: 20px 0; 
			border: 1px solid #e5e5e5; 
			border-radius: 6px; 
			font-family: Menlo, Consolas, Monaco, Liberation Mono, Lucida Console, monospace; 
			font-size: 18px; 
			line-height: 48px;
		"
	HTML;

	/**
	 * The basic h1 style.
	 */
	protected static $h1Style = 'style="font-size: 27px; font-weight: 600;"';

	/**
	 * The basic paragraph style.
	 */
	protected static $paragraphStyle = 'style="font-size: 16px; line-height: 22px;"';

	/**
	 * The wrapping body markup.
	 *
	 * @param string $content
	 * @return string the rendered body
	 */
	protected static function body(string $content) {
		$pStyle = self::$paragraphStyle;
		$Text = Text::getObject();

		return <<< HTML
			<!doctype html>
			<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
				</head>
				<body style="
					padding: 15px 0; 
					font-family: -apple-system, BlinkMacSystemFont, helvetica, Ubuntu, roboto, noto, arial, sans-serif;
					font-size: 16px; 
					line-height: 22px;
				">
					<table border="0" cellpadding="0" cellspacing="0" height="90%" width="95%">
						<tbody>
							<tr>
								<td width="25%"></td>
								<td width="400px" style="min-width: 400px; max-width: 400px;">
									$content
									<p $pStyle>
										$Text->email_automatic
										<br>
										<br>
										<b>Automad</b>
									</p>
								</td>
								<td width="25%"></td>
							</tr>
						</tbody>
					</table>
				</body>
			</html>
			HTML;
	}
}
