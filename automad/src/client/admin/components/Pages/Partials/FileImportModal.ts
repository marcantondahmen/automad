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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, EventName, FileController, html } from '@/admin/core';

export const renderFileImportModal = (): string => {
	return html`
		<am-modal id="am-file-import-modal" ${Attr.clearForm}>
			<am-modal-dialog>
				<am-form
					${Attr.api}="${FileController.import}"
					${Attr.event}="${EventName.filesChangeOnServer}"
				>
					<am-modal-body>
						<div class="${CSS.field}">
							<input
								class="${CSS.input}"
								name="importUrl"
								type="text"
								placeholder="URL"
							/>
						</div>
					</am-modal-body>
					<am-modal-footer>
						<am-modal-close class="${CSS.button}">
							${App.text('cancel')}
						</am-modal-close>
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							${App.text('importFromUrl')}
						</am-submit>
					</am-modal-footer>
				</am-form>
			</am-modal-dialog>
		</am-modal>
	`;
};
