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

import { BlockAPI } from '@editorjs/editorjs';
import {
	App,
	Attr,
	create,
	createSelect,
	CSS,
	listen,
	query,
	queryAll,
} from '@/core';
import { LayoutTuneData, SelectComponentOption } from '@/types';
import { BaseTune } from './BaseTune';

/**
 * The list of blocks that can be stretched.
 */
const stretchableBlocks = [
	'section',
	'delimiter',
	'image',
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
export class LayoutTune extends BaseTune<LayoutTuneData> {
	static STRETCHED = 'stretched';

	get isStretchable(): boolean {
		return stretchableBlocks.includes(this.block.name || '');
	}

	get isFlex(): boolean {
		return this.config.isSectionBlock || false;
	}

	get selected(): string {
		const { stretched, width } = this.data;
		const selected = stretched ? LayoutTune.STRETCHED : width ? width : '';

		return selected as string;
	}

	set selected(value) {
		this.data.stretched = value === LayoutTune.STRETCHED;
		this.data.width =
			value && value !== LayoutTune.STRETCHED
				? (value as unknown as typeof fractions)
				: null;

		LayoutTune.apply(this.block, this.data);
	}

	static apply(block: BlockAPI, layout: LayoutTuneData): void {
		if (!layout) {
			return;
		}

		const element = block.holder;
		const { stretched } = layout;
		const width = (layout?.width as unknown as string) || null;

		element.classList.toggle(
			`${CSS.editorStyleBase}--stretched`,
			stretched
		);

		width
			? element.setAttribute(Attr.width, width)
			: element.removeAttribute(Attr.width);
	}

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

		if (
			query('.ce-settings', toolbar) ||
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

		queryAll('.ce-toolbar').forEach((_toolbar) => {
			_toolbar.classList.toggle(CSS.active, _toolbar === toolbar);
		});
	}

	protected prepareData(data: LayoutTuneData): LayoutTuneData {
		return {
			stretched: data.stretched || false,
			width: data.width || null,
		};
	}

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
