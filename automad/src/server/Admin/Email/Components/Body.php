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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Admin\Email\Components;

use Automad\Core\Text;
use Automad\System\Mail;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The email body component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Body {
	/**
	 * Render the email body.
	 *
	 * @param string[] $components
	 * @return string
	 */
	public static function render(array $components): string {
		$Text = Text::getObject();
		$cid = Mail::LOGO_CID;
		$website = $_SERVER['SERVER_NAME'] ?? AM_BASE_URL;
		$content = join('', $components);

		return <<<HTML
			<!doctype html>
			<html>
				<body style="background-color: #101113; margin: 0;">
					<div
						style="
							background-color: #101113;
							color: #ffffff;
							font-family: -apple-system, BlinkMacSystemFont, Inter, Roboto, 'Helvetica Neue', 'Arial Nova', 'Nimbus Sans', Arial, system-ui, sans-serif;
							font-size: 16px;
							font-weight: 400;
							letter-spacing: 0.15008px;
							line-height: 1.5;
							margin: 0;
							padding: 32px 0;
							min-height: 100%;
							width: 100%;
						"
					>
						<table
							align="center"
							width="100%"
							style="
								margin: 0 auto;
								max-width: 600px;
								background-color: #101113;
							"
							role="presentation"
							cellspacing="0"
							cellpadding="0"
							border="0"
						>
							<tbody>
								<tr style="width: 100%">
									<td>
										<div style="padding: 24px 24px 32px 24px">
											<img
												alt="Automad logo"
												src="cid:$cid"
												width="43"
												style="
													outline: none;
													border: none;
													text-decoration: none;
													vertical-align: middle;
													display: inline-block;
													max-width: 100%;
												"
											/>
										</div>
										$content
										<div style="padding: 16px 24px 16px 24px">
											<hr
												style="
													width: 100%;
													border: none;
													border-top: 2px solid #2a2b2e;
													margin: 0;
												"
											/>
										</div>
										<div
											style="
												color: #5a5b5e;
												font-weight: normal;
												padding: 16px 24px 20px 24px;
											"
										>
											Automad on $website
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</body>
			</html>
			HTML;
	}
}
