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
	collectFieldData,
	create,
	CSS,
	listen,
	query,
	queryAll,
} from '@/admin/core';
import { LayoutFraction, LayoutOption, LayoutTuneData } from '@/admin/types';
import { getBlockTools } from '../blocks';
import { BaseElementTune } from './BaseElementTune';
import layout11 from '@/common/svg/layout/layout-1-1.svg';
import layout12 from '@/common/svg/layout/layout-1-2.svg';
import layout13 from '@/common/svg/layout/layout-1-3.svg';
import layout14 from '@/common/svg/layout/layout-1-4.svg';
import layout23 from '@/common/svg/layout/layout-2-3.svg';
import layout34 from '@/common/svg/layout/layout-3-4.svg';
import layoutDefault from '@/common/svg/layout/layout-default.svg';
import layoutStretched from '@/common/svg/layout/layout-stretched.svg';

/**
 * The fractions that can be used as width.
 */
export const fractions = ['1/4', '1/3', '1/2', '2/3', '3/4', '1/1'] as const;

/**
 * The layout value when elements are stretched.
 */
export const STRETCHED = 'stretched';

/**
 * The icon map for fractions.
 */
const iconMap = {
	'1/1': layout11,
	'1/2': layout12,
	'1/3': layout13,
	'1/4': layout14,
	'2/3': layout23,
	'3/4': layout34,
} as const;

/**
 * The main layout options that are always available, also outside of sections.
 */
const getMainOptions = (): LayoutOption[] => [
	{
		layout: '',
		tooltip: App.text('layoutDefault'),
		icon: layoutDefault,
		cls: [CSS.editorTunesLayoutOption, CSS.editorTunesLayoutOptionLarge],
	},
	{
		layout: STRETCHED,
		tooltip: App.text('layoutStretch'),
		icon: layoutStretched,
		cls: [CSS.editorTunesLayoutOption, CSS.editorTunesLayoutOptionLarge],
	},
];

/**
 * Additional fraction options that are available in flex sections.
 */
const getFlexOptions = (): LayoutOption[] =>
	fractions.map((fraction) => {
		return {
			layout: fraction,
			tooltip: `${App.text('layoutWidth')}: ${fraction}`,
			icon: iconMap[fraction],
			cls: [CSS.editorTunesLayoutOption],
		};
	});

/**
 * Create a radio input.
 *
 * @param option
 * @param wrapper
 */
const createLayoutOption = (
	option: LayoutOption,
	selected: string,
	wrapper: HTMLElement
) => {
	const active = (option.layout || '') === (selected || '');
	const cls = [
		...option.cls,
		...(active ? [CSS.editorTunesLayoutOptionActive] : []),
	];

	(
		create(
			'input',
			[],
			{
				type: 'radio',
				name: 'layout',
				value: option.layout || '',
			},
			create(
				'label',
				cls,
				{ [Attr.tooltip]: option.tooltip },
				wrapper,
				option.icon
			)
		) as HTMLInputElement
	).checked = active;
};

/**
 * The LayoutTune class allows for block layout based on flexbox.
 */
export class LayoutTune extends BaseElementTune<LayoutTuneData> {
	/**
	 * Test whether the block is allowed to stretch.
	 * In order to make a block stretchable, set the
	 * "stretchable: true" in the block tool configuration.
	 */
	get isStretchable(): boolean {
		return getBlockTools(false)[this.block.name]?.stretchable ?? false;
	}

	/**
	 * The sort order for this tune.
	 */
	protected sort: number = 1;

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
		const selected = stretched ? STRETCHED : width ? width : '';

		return selected as string;
	}

	/**
	 * Create data object based on selected option string.
	 *
	 * @param data
	 */
	set selected(value) {
		this.data.stretched = value === STRETCHED;
		this.data.width =
			value && value !== STRETCHED ? (value as LayoutFraction) : '';

		queryAll<HTMLInputElement>('input', this.wrapper).forEach((input) => {
			input.parentElement.classList.toggle(
				CSS.editorTunesLayoutOptionActive,
				input.checked
			);
		});

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
	 * @param blockElement
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

		const contentRect = content.getBoundingClientRect();
		const blockRect = blockHolder.parentElement.getBoundingClientRect();

		const offsetX = Math.round(contentRect.x - blockRect.x);
		const offsetY = Math.round(contentRect.y - blockRect.y);

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
		const wrapper = create('div', [CSS.editorTunesLayout]);

		if (!this.isFlex && !this.isStretchable) {
			this.data = null;

			return wrapper;
		}

		if (this.isStretchable) {
			getMainOptions().forEach((option) => {
				createLayoutOption(option, this.selected, wrapper);
			});
		}

		if (this.isFlex) {
			getFlexOptions().forEach((option) => {
				createLayoutOption(option, this.selected, wrapper);
			});
		}

		listen(wrapper, 'change', () => {
			const { layout } = collectFieldData(wrapper);

			this.selected = layout;
			this.block.dispatchChange();
		});

		return wrapper;
	}
}
