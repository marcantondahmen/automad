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
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { App } from '@/admin/core';
import { Table } from '@/vendor/editorjs';

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
	 * The sanitizer config.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			content: {
				br: true,
			},
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
