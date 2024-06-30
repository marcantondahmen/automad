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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, EventName, MailConfigController } from '@/admin/core';

/**
 * Render the email section.
 *
 * @returns the rendered HTML
 */
export const renderMailSection = (): string => {
	return `
		<p>${App.text('systemMailInfo')}</p>
		<div class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}">
			<am-mail-config-form 
				${Attr.api}="${MailConfigController.save}"
				${Attr.event}="${EventName.appStateRequireUpdate}"
			></am-mail-config-form>
			<am-form ${Attr.api}=${MailConfigController.test}>
				<am-submit class="${CSS.button}">
					${App.text('systemMailSendTest')}
				</am-submit>
			</am-form>
		</div>
	`;
};
