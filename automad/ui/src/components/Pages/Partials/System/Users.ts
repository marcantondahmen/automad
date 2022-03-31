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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, html } from '../../../../core';

export const renderUsersSection = (): string => {
	return html`
		<am-form>
			<p>$${App.text('systemUsersAdd')}</p>
			<p>$${App.text('systemUsersChangePassword')}</p>
			<p>$${App.text('systemUsersInfo')}</p>
			<p>$${App.text('systemUsersInvite')}</p>
			<p>$${App.text('systemUsersRegistered')}</p>
			<p>$${App.text('systemUsersRegisteredInfo')}</p>
			<p>$${App.text('systemUsersYou')}</p>
			<p>$${App.text('newPassword')}</p>
			<p>$${App.text('currentPassword')}</p>
		</am-form>
	`;
};
