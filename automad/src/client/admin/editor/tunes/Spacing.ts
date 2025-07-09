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
	collectFieldData,
	create,
	createField,
	CSS,
	FieldTag,
	html,
	query,
	uniqueId,
} from '@/admin/core';
import { SpacingTuneData } from '@/admin/types';
import { BaseModalTune } from './BaseModalTune';

export class SpacingTune extends BaseModalTune<SpacingTuneData> {
	/**
	 *
	 * The sort order for this tune.
	 */
	protected sort: number = 2;

	/**
	 * The tune title.
	 */
	get title() {
		return App.text('padding');
	}

	/**
	 * The tune icon.
	 */
	get icon() {
		return '<i class="bi bi-distribute-vertical"></i>';
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: SpacingTuneData): SpacingTuneData {
		return {
			top: data?.top || '',
			right: data?.right || '',
			bottom: data?.bottom || '',
			left: data?.left || '',
		};
	}

	/**
	 * Sanitize form data before setting the current state.
	 *
	 * @param data
	 * @return the sanitized data
	 */
	protected sanitize(data: SpacingTuneData): SpacingTuneData {
		const sanitize = (str: string) => {
			return str.replace(/[^\w_\.\%]+/g, '').trim();
		};

		return {
			top: sanitize(data.top),
			right: sanitize(data.right),
			bottom: sanitize(data.bottom),
			left: sanitize(data.left),
		};
	}

	/**
	 * Extract field data from the modal.
	 *
	 * @param modal
	 * @return the extracted data
	 */
	protected getFormData(modal: HTMLElement): SpacingTuneData {
		return collectFieldData(modal) as SpacingTuneData;
	}

	/**
	 * Create the form fields inside of the modal.
	 *
	 * @return the fields wrapper
	 */
	protected createForm(): HTMLElement {
		const wrapper = create(
			'div',
			[CSS.flex, CSS.flexColumn, CSS.flexGapLarge],
			{},
			null,
			html`
				<div class="${CSS.flex} ${CSS.flexGapLarge}">
					<div class="top"></div>
					<div class="bottom"></div>
				</div>
				<div class="${CSS.flex} ${CSS.flexGapLarge}">
					<div class="left"></div>
					<div class="right"></div>
				</div>
			`
		);

		createField(FieldTag.numberUnit, query('.top', wrapper), {
			label: App.text('paddingTop'),
			value: this.data.top,
			key: uniqueId(),
			name: 'top',
		});

		createField(FieldTag.numberUnit, query('.bottom', wrapper), {
			label: App.text('paddingBottom'),
			value: this.data.bottom,
			key: uniqueId(),
			name: 'bottom',
		});

		createField(FieldTag.numberUnit, query('.left', wrapper), {
			label: App.text('paddingLeft'),
			value: this.data.left,
			key: uniqueId(),
			name: 'left',
		});

		createField(FieldTag.numberUnit, query('.right', wrapper), {
			label: App.text('paddingRight'),
			value: this.data.right,
			key: uniqueId(),
			name: 'right',
		});

		return wrapper;
	}

	/**
	 * Render the label.
	 *
	 * @return the rendered label
	 */
	protected renderLabel(): string {
		const { top, right, bottom, left } = this.data;

		return `${top || 0} / ${right || 0} / ${bottom || 0} / ${left || 0}`;
	}

	/**
	 * Apply tune to block content element.
	 *
	 * @param the block content element
	 * @return the element
	 */
	protected wrap(blockElement: HTMLElement): HTMLElement {
		const props = {
			top: 'paddingTop',
			right: 'paddingRight',
			bottom: 'paddingBottom',
			left: 'paddingLeft',
		} as const;

		const block = query(':scope > .cdx-block', blockElement);

		for (const [key, value] of Object.entries(this.data)) {
			block.style[props[key as keyof typeof props]] = value;
		}

		return blockElement;
	}
}
