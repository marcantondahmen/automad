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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App } from '@/admin/core';
// @ts-ignore
import Table from '@editorjs/table';

export class TableBlock extends Table {
	/**
	 * Get Toolbox settings.
	 *
	 * @returns the settings object
	 */
	static get toolbox() {
		return {
			title: App.text('table'),
			icon: '<i class="bi bi-grid-3x3"></i>',
		};
	}

	/**
	 * Returns plugin settings
	 *
	 * @returns the settings object
	 */
	renderSettings() {
		const [withHeadings, withoutHeadings] = super.renderSettings();

		return [
			{
				...withHeadings,
				icon: '<i class="bi bi-table"></i>',
				label: App.text('tableWithHeadings'),
			},
			{
				...withoutHeadings,
				icon: '<i class="bi bi-grid-3x3"></i>',
				label: App.text('tableWithoutHeadings'),
			},
		];
	}
}
