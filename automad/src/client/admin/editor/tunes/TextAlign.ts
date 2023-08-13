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

import { collectFieldData, create, CSS, html, listen, query } from '@/core';
import { TextAlignRadio, TextAlignTuneData } from '@/types';
import { BaseElementTune } from './BaseElementTune';

const alignOptions: TextAlignRadio[] = [
	{ value: 'left', icon: 'text-left' },
	{ value: 'center', icon: 'text-center' },
	{ value: 'right', icon: 'text-right' },
];

export class TextAlignTune extends BaseElementTune<TextAlignTuneData> {
	/**
	 * Prepare the tune data.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: TextAlignTuneData): TextAlignTuneData {
		if (['left', 'center', 'right'].includes(data ?? '')) {
			return data;
		}

		return 'left';
	}

	/**
	 * Apply the align option to the block on load.
	 */
	wrap(blockContent: HTMLElement): HTMLElement {
		this.apply(blockContent);

		return blockContent;
	}

	/**
	 * Apply the align option to the block.
	 */
	private apply(blockContent: HTMLElement): void {
		const base = `${CSS.editorStyleBase}--text-`;

		alignOptions.forEach((option) => {
			const cls = `${base}${option.value}`;
			blockContent.classList.toggle(cls, this.data == option.value);
		});
	}

	/**
	 * Render the wrapper content.
	 *
	 * @return the rendered wrapper
	 */
	renderSettings(): HTMLElement {
		const wrapper = create('div', [CSS.editorPopoverForm], {});
		const radio = create('form', [CSS.radio], {}, wrapper);

		alignOptions.forEach((option) => {
			create(
				'label',
				[],
				{},
				radio,
				html`
					<input type="radio" name="align" value="${option.value}" />
					<span><i class="bi bi-${option.icon}"></i></span>
				`
			);
		});

		radio.align.value = this.data;

		listen(wrapper, 'change', () => {
			const { align } = collectFieldData(wrapper);

			this.data = align;
			this.block.dispatchChange();

			this.apply(query(':scope > *', this.block.holder));
		});

		return wrapper;
	}
}
