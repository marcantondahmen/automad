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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App } from '@/admin/core';
import { NestedList } from '@/vendor/editorjs';

export class NestedListBlock extends NestedList {
	/**
	 * Get Toolbox settings.
	 *
	 * @returns the settings object
	 */
	static get toolbox() {
		return {
			icon: '<i class="bi bi-list-ul"></i>',
			title: App.text('list'),
		};
	}

	/**
	 * Returns plugin settings
	 *
	 * @returns the settings object
	 */
	renderSettings() {
		const [unordered, ordered] = super.renderSettings();

		return [
			{
				...unordered,
				icon: '<i class="bi bi-list-ul"></i>',
				label: App.text('unorderedList'),
			},
			{
				...ordered,
				icon: '<i class="bi bi-list-ol"></i>',
				label: App.text('orderedList'),
			},
		];
	}

	/**
	 * Merge two lists.
	 *
	 * @param data
	 */
	merge(data: { items: any[] }) {
		// @ts-ignore
		this.appendItems(data.items, this.nodes.wrapper);
	}
}
