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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { App, create, CSS, fire, resolveFileUrl } from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';
import type { FocalPoint } from '@/admin/types';

/**
 * Pick the focal point.
 *
 * @param event
 * @param img
 * @return the focal point
 */
const pickFocalPoint = (event: MouseEvent, img: HTMLElement): FocalPoint => {
	const { left, width, top, height } = img.getBoundingClientRect();
	const { clientX, clientY } = event;

	return {
		x: Math.max(
			0,
			Math.min(100, Math.round(((clientX - left) / width) * 100))
		),
		y: Math.max(
			0,
			Math.min(100, Math.round(((clientY - top) / height) * 100))
		),
	};
};

/**
 * A responsive image settings editor component.
 *
 * @extends BaseComponent
 */
export class FocalPointPickerComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 * @readonly
	 */
	static readonly TAG_NAME = 'am-focal-point-picker';

	/**
	 * The value.
	 */
	private _value: FocalPoint = null;

	private set value(value: FocalPoint) {
		this._value = value;

		fire('change', this);
		this.update();
	}

	get value(): FocalPoint {
		return this._value;
	}

	/**
	 * The image url.
	 */
	private image: string = '';

	/**
	 * Inititalize the settings editor from outside.
	 *
	 * @param image
	 * @param value
	 */
	init(image: string, value: FocalPoint): void {
		this.image = resolveFileUrl(image);

		// Set private props directly in order to trigger change event
		// manually and only once without setters.
		this._value = value;

		this.createPicker();
		this.update();
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.focalPoint);
	}

	/**
	 * Update and rerender the component.
	 */
	private update(): void {
		if (!this.value) {
			this.removeAttribute('style');
		} else {
			const { x, y } = this.value;

			this.setAttribute('style', `--x: ${x}%; --y: ${y}%`);
		}
	}

	/**
	 * The focal point picker.
	 */
	private createPicker(): void {
		const area = create(
			'div',
			[CSS.flex, CSS.flexColumn, CSS.flexGap],
			{},
			this
		);

		const wrapper = create('div', [CSS.focalPointWrapper], {}, area);
		const picker = create('div', [CSS.focalPointPicker], {}, wrapper);
		const img = create('img', [], { src: this.image }, picker);
		create('span', [CSS.focalPointMarker], {}, picker);

		const reset = create(
			'button',
			[CSS.button],
			{},
			area,
			App.text('focalPointReset')
		);

		this.listen(reset, 'click', () => {
			this.value = null;
		});

		let dragging = false;

		this.listen(picker, 'pointerdown', (event: PointerEvent) => {
			dragging = true;
			this.value = pickFocalPoint(event, img);
		});

		this.listen(picker, 'pointermove', (event: PointerEvent) => {
			if (dragging) {
				this.value = pickFocalPoint(event, img);
			}
		});

		this.listen(picker, 'pointerup', (event: PointerEvent) => {
			dragging = false;
			picker.releasePointerCapture(event.pointerId);
		});
	}
}

customElements.define(
	FocalPointPickerComponent.TAG_NAME,
	FocalPointPickerComponent
);
