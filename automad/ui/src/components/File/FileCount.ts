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
 */

import { BaseComponent } from '../Base';
import { create, listen, queryAll } from '../../core';
import { fileCollectionRenderedEventName } from '../Forms/FileCollectionList';

/**
 * A file count badge component that displays the total number of file card elements.
 *
 * @example
 * <am-file-count></am-file-count>
 *
 * @extends BaseComponent
 */
class FileCountComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const badge = create('span', [], {}, this);
		const update = () => {
			const count = queryAll('am-file-card').length;

			badge.textContent = `${count}`;
		};

		listen(window, fileCollectionRenderedEventName, update.bind(this));
		setTimeout(update.bind(this), 0);
	}
}

customElements.define('am-file-count', FileCountComponent);
