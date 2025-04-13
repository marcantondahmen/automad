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

import {
	App,
	Attr,
	create,
	createSelect,
	CSS,
	listen,
	query,
} from '@/admin/core';
import {
	LayoutFraction,
	LayoutTuneData,
	SelectComponentOption,
} from '@/admin/types';
import { BaseElementTune } from './BaseElementTune';

/**
 * The list of blocks that can be stretched.
 */
const stretchableBlocks = [
	'section',
	'component',
	'delimiter',
	'image',
	'video',
	'gallery',
	'slider',
	'pagelist',
	'embed',
	'table',
];

/**
 * The fractions that can be used as width.
 */
export const fractions = ['1/4', '1/3', '1/2', '2/3', '3/4', '1/1'] as const;

/**
 * The LayoutTune class allows for block layout based on flexbox.
 */
export class LayoutTune extends BaseElementTune<LayoutTuneData> {
	/**
	 * The stretched property name.
	 *
	 * @static
	 */
	static STRETCHED = 'stretched';

	/**
	 * Test whether the block is allowd to stretch.
	 */
	get isStretchable(): boolean {
		return stretchableBlocks.includes(this.block.name || '');
	}

	/**
	 * Test whether flex can be enabled.
	 */
	get isFlex(): boolean {
		return this.config.isSectionBlock || false;
	}

	/**
	 * Get the selected option.
	 */
	get selected(): string {
		const { stretched, width } = this.data;
		const selected = stretched ? LayoutTune.STRETCHED : width ? width : '';

		return selected as string;
	}

	/**
	 * Create data object based on selected option string.
	 *
	 * @param data
	 */
	set selected(value) {
		this.data.stretched = value === LayoutTune.STRETCHED;
		this.data.width =
			value && value !== LayoutTune.STRETCHED
				? (value as LayoutFraction)
				: null;

		this.apply();
	}

	/**
	 * Apply layout to block.
	 *
	 * @param block
	 * @param layout
	 */
	private apply(): void {
		if (!this.data) {
			return;
		}

		setTimeout(() => {
			const element = this.block.holder;
			const { stretched, width } = this.data;

			element.classList.toggle(
				`${CSS.editorStyleBase}--stretched`,
				stretched
			);

			width
				? element.setAttribute(Attr.width, width)
				: element.removeAttribute(Attr.width);
		}, 0);
	}

	/**
	 * Apply tune to block content element.
	 *
	 * @param the block content element
	 * @return the element
	 */
	wrap(blockElement: HTMLElement): HTMLElement {
		this.apply();

		return blockElement;
	}

	/**
	 * Update the toolbar handle position based on the block's position in the layout.
	 *
	 * @param blockHolder
	 */
	static updateToolbarPosition(blockHolder: HTMLElement): void {
		if (!blockHolder) {
			return;
		}

		const content = query(':scope > .ce-block__content', blockHolder);
		const editor = blockHolder.closest<HTMLElement>('.codex-editor');
		const toolbar = query(':scope > .ce-toolbar', editor);

		if (!toolbar) {
			return;
		}

		const settings = query('.ce-settings', toolbar);

		if (
			settings?.hasChildNodes() ||
			editor.matches('.codex-editor--toolbox-opened')
		) {
			// Return early in order keep focus in case a settings panel is opened.
			return;
		}

		const offsetX = Math.round(
			content.getBoundingClientRect().x -
				blockHolder.parentElement.getBoundingClientRect().x
		);

		const offsetY = Math.round(
			content.getBoundingClientRect().y -
				blockHolder.parentElement.getBoundingClientRect().y
		);

		toolbar.setAttribute('style', `--x: ${offsetX}px; --y: ${offsetY}px;`);
	}

	/**
	 * Prepare the tune data.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: LayoutTuneData): LayoutTuneData {
		return {
			stretched: data?.stretched || false,
			width: data?.width || null,
		};
	}

	/**
	 * Render the wrapper element.
	 *
	 * @return the wrapper
	 */
	renderSettings(): HTMLElement {
		const wrapper = create('div', [CSS.editorPopoverForm]);

		if (!this.isFlex && !this.isStretchable) {
			this.data = null;

			return wrapper;
		}

		const options: SelectComponentOption[] = [
			{ value: '', text: App.text('layoutDefault') },
		];

		if (this.isStretchable) {
			options.push({
				value: LayoutTune.STRETCHED,
				text: App.text('layoutStretch'),
			});
		}

		if (this.isFlex) {
			fractions.forEach((fraction) => {
				options.push({
					value: fraction,
					text: `${App.text('layoutWidth')}: ${fraction.replace(
						'/',
						'⁄'
					)}`,
				});
			});
		}

		const select = createSelect(options, this.selected, wrapper);

		listen(select.select, 'change', () => {
			this.selected = select.value;
			this.block.dispatchChange();
		});

		return wrapper;
	}
}
