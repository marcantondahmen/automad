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

import { App, Attr, CSS, html } from '../../../../core';
import { Listener } from '../../../../types';

/**
 * Render the config file section.
 *
 * @param listeners
 * @returns the rendered HTML
 */
export const renderConfigFileSection = (listeners: Listener[]): string => {
	return html`
		<div class="${CSS.alert}">
			<div class="${CSS.alertIcon}">
				<i class="bi bi-fire"></i>
			</div>
			<div class="${CSS.alertText}">
				<div>${App.text('systemConfigFileInfo')}</div>
				<div>
					<am-config-file-form
						${Attr.api}="Config/file"
					></am-config-file-form>
				</div>
			</div>
		</div>
	`;
};
