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
import { BaseTune } from './BaseTune';

const alignOptions: TextAlignRadio[] = [
	{ value: 'left', icon: 'text-left' },
	{ value: 'center', icon: 'text-center' },
	{ value: 'right', icon: 'text-right' },
];

export class TextAlignTune extends BaseTune<TextAlignTuneData> {
	/**
	 * Prepare the tune data.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: TextAlignTuneData): TextAlignTuneData {
		return {
			align: data.align ?? 'left',
		};
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
			blockContent.classList.toggle(cls, this.data.align == option.value);
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
		const name = 'align';

		alignOptions.forEach((option) => {
			create(
				'label',
				[],
				{},
				radio,
				html`
					<input
						type="radio"
						name="${name}"
						value="${option.value}"
						${this.data.align == option.value ? 'checked' : ''}
					/>
					<span><i class="bi bi-${option.icon}"></i></span>
				`
			);
		});

		radio.align.value = this.data.align;

		listen(wrapper, 'change', () => {
			const { align } = collectFieldData(
				wrapper
			) as unknown as TextAlignTuneData;

			this.data.align = align;
			this.block.dispatchChange();

			this.apply(query('.ce-block__content', this.block.holder));
		});

		return wrapper;
	}
}
