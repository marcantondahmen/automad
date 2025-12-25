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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { TunesMenuConfig } from '@/vendor/editorjs';
import { App, CSS, html } from '@/admin/core';
import { TableOfContentsBlockData } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

export const tableOfContentsTypes = ['ordered', 'unordered'] as const;

export class TableOfContentsBlock extends BaseBlock<TableOfContentsBlockData> {
	/**
	 * Toolbox settings.
	 *
	 * @static
	 */
	static get toolbox() {
		return {
			title: App.text('tableOfContentsBlockTitle'),
			icon: '<i class="bi bi-list-columns"></i>',
		};
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(
		data: TableOfContentsBlockData
	): TableOfContentsBlockData {
		return {
			type: data.type ?? 'ordered',
		};
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		this.wrapper.innerHTML = html`
			<div class="${CSS.card}">
				<div class="${CSS.cardIcon}">
					<i
						class="bi bi-list-${this.data.type == 'ordered'
							? 'ol'
							: 'ul'}"
					></i>
				</div>
				<div class="${CSS.cardTitle}">
					${TableOfContentsBlock.toolbox.title}
				</div>
			</div>
		`;

		return this.wrapper;
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): TableOfContentsBlockData {
		return this.data;
	}

	/**
	 * Create the tunes menu configuration.
	 *
	 * @return the tunes menu configuration
	 */
	renderSettings(): TunesMenuConfig {
		return [
			{
				icon: '<i class="bi bi-list-ol"></i>',
				label: App.text('orderedList'),
				closeOnActivate: true,
				onActivate: () => {
					this.data.type = 'ordered';
					this.render();
				},
				toggle: 'type',
				isActive: this.data.type == 'ordered',
			},
			{
				icon: '<i class="bi bi-list-ul"></i>',
				label: App.text('unorderedList'),
				closeOnActivate: true,
				onActivate: () => {
					this.data.type = 'unordered';
					this.render();
				},
				toggle: 'type',
				isActive: this.data.type == 'unordered',
			},
		];
	}
}
