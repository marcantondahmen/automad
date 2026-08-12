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

import {
	App,
	Attr,
	collectFieldData,
	create,
	CSS,
	debounce,
	fire,
	html,
	resolveFileUrl,
} from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';
import { ImageBreakpoints, ImageFocalPoint } from '../types';

/**
 * Convert a breakpoints object into the input formatted string.
 *
 * @param breakpoints
 * @return the formatted string
 */
const breakpointsToString = (breakpoints: ImageBreakpoints): string => {
	return Object.keys(breakpoints).reduce((out: string, maxWidth: string) => {
		return `${out} ${maxWidth}:${breakpoints[maxWidth].aspectRatio}`.trim();
	}, '');
};

/**
 * Convert a formatted input string into a breakpoints object.
 *
 * @param breakpointsString
 * @return the breakpoints object
 */
const stringToBreakpoints = (breakpointsString: string): ImageBreakpoints => {
	const breakpoints: ImageBreakpoints = {};

	breakpointsString.split(' ').forEach((pair: string) => {
		const [maxWidth, aspectRatio] = pair.split(':');

		if (!maxWidth || !aspectRatio) {
			return;
		}

		if (!maxWidth.match(/^\d+$/)) {
			return;
		}

		if (!aspectRatio.match(/^\d+(\.\d+)?\/\d+(\.\d+)?$/)) {
			return;
		}

		breakpoints[maxWidth] = { aspectRatio };
	});

	return breakpoints;
};

/**
 * Pick the focal point.
 *
 * @param event
 * @param img
 * @return the focal point
 */
const pickFocalPoint = (
	event: MouseEvent,
	img: HTMLElement
): ImageFocalPoint => {
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
export class ResponsiveImageSettingsComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 * @readonly
	 */
	static readonly TAG_NAME = 'am-responsive-image-settings';

	/**
	 * The preview container element.
	 */
	private preview: HTMLElement;

	/**
	 * The focal point
	 */
	private _focalPoint: ImageFocalPoint = null;

	private set focalPoint(focalPoint: ImageFocalPoint) {
		this._focalPoint = focalPoint;

		fire('change', this);
		this.update();
	}

	get focalPoint(): ImageFocalPoint {
		return this._focalPoint;
	}

	/**
	 * The breakpoints.
	 */
	private _breakpoints: ImageBreakpoints = {};

	private set breakpoints(breakpoints: ImageBreakpoints) {
		this._breakpoints = breakpoints;

		fire('change', this);
		this.update();
	}

	get breakpoints(): ImageBreakpoints {
		return this._breakpoints;
	}

	/**
	 * The image url.
	 */
	private image: string = '';

	/**
	 * Inititalize the settings editor from outside.
	 *
	 * @param image
	 * @param breakpoints
	 * @param focalPoint
	 */
	init(
		image: string,
		breakpoints: ImageBreakpoints,
		focalPoint: ImageFocalPoint
	): void {
		this.image = resolveFileUrl(image);

		// Set private props directly in order to trigger change event
		// manually and only once without setters.
		this._breakpoints = breakpoints;
		this._focalPoint = focalPoint;

		this.createBreakpointsInput();
		this.createFocalPointPicker();

		this.preview = this.createPreview();

		this.update();
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.responsiveImageSettings);
	}

	/**
	 * Update and rerender the component.
	 */
	private update(): void {
		if (!this.focalPoint) {
			this.removeAttribute('style');
		} else {
			const { x, y } = this.focalPoint;

			this.setAttribute('style', `--x: ${x}%; --y: ${y}%`);
		}

		this.preview.innerHTML = '';

		for (const [maxWidth, item] of Object.entries(this.breakpoints)) {
			create(
				'div',
				[CSS.responsiveImageSettingsPreviewItem],
				{},
				this.preview,
				html`
					<img
						src="${this.image}"
						style="aspect-ratio: ${item.aspectRatio}"
					/>
					<small>${maxWidth}:${item.aspectRatio}</small>
				`
			);
		}
	}

	/**
	 * The breakpoints input.
	 */
	private createBreakpointsInput(): void {
		create(
			'small',
			[CSS.responsiveImageSettingsAreaBreakpointsHelp],
			{},
			this,
			App.text('responsiveImageBreakpointsHelp')
		);

		const fieldWrapper = create(
			'div',
			[CSS.responsiveImageSettingsAreaBreakpoints],
			{},
			this
		);

		const inputField = create(
			'div',
			[CSS.field],
			{
				[Attr.error]: App.text('responsiveImageBreakpointsError'),
			},
			fieldWrapper
		);

		const input = create<HTMLInputElement>(
			'input',
			[CSS.input, CSS.textMono],
			{
				type: 'text',
				value: breakpointsToString(this.breakpoints),
				name: 'breakpointsString',
				placeholder: '600:1/1 900:2/3',
				pattern: '([1-9][0-9]+:[0-9.]+/[0-9.]+( |$))*',
			},
			inputField
		);

		const presetsField = create(
			'div',
			[CSS.field, CSS.flex, CSS.flexColumn],
			{},
			fieldWrapper
		);

		create(
			'label',
			[CSS.fieldLabel],
			{},
			presetsField,
			App.text('responsiveImageBreakpointsPresets')
		);

		const presetButtons = create('div', [CSS.formGroup], {}, presetsField);
		const presets = ['500:2/3', '700:1/1', '900:4/3'];

		presets.forEach((p) => {
			const button = create(
				'button',
				[
					CSS.button,
					CSS.formGroupItem,
					CSS.textMono,
					CSS.flexItemGrow,
					CSS.textMuted,
				],
				{},
				presetButtons,
				p
			);

			this.listen(button, 'click', () => {
				input.value = `${input.value} ${p}`.trim();

				fire('input', input);
			});
		});

		this.listen(
			fieldWrapper,
			'input',
			debounce(() => {
				const data = collectFieldData(fieldWrapper);

				this.breakpoints = stringToBreakpoints(data.breakpointsString);
			}, 200)
		);
	}

	/**
	 * The focal point picker.
	 */
	private createFocalPointPicker(): void {
		create(
			'small',
			[CSS.responsiveImageSettingsAreaFocalPointHelp],
			{},
			this,
			App.text('responsiveImageFocalPointHelp')
		);

		const area = create(
			'div',
			[
				CSS.responsiveImageSettingsAreaFocalPoint,
				CSS.flex,
				CSS.flexColumn,
				CSS.flexGap,
			],
			{},
			this
		);

		const wrapper = create(
			'div',
			[CSS.responsiveImageSettingsFocalPointWrapper],
			{},
			area
		);

		const picker = create(
			'div',
			[CSS.responsiveImageSettingsFocalPointPicker],
			{},
			wrapper
		);

		const img = create('img', [], { src: this.image }, picker);

		create(
			'span',
			[CSS.responsiveImageSettingsFocalPointMarker],
			{},
			picker
		);

		const reset = create(
			'button',
			[CSS.button],
			{},
			area,
			App.text('responsiveImageResetFocalPoint')
		);

		this.listen(reset, 'click', () => {
			this.focalPoint = null;
		});

		let dragging = false;

		this.listen(picker, 'pointerdown', (event: PointerEvent) => {
			dragging = true;
			this.focalPoint = pickFocalPoint(event, img);
		});

		this.listen(picker, 'pointermove', (event: PointerEvent) => {
			if (dragging) {
				this.focalPoint = pickFocalPoint(event, img);
			}
		});

		this.listen(picker, 'pointerup', (event: PointerEvent) => {
			dragging = false;
			picker.releasePointerCapture(event.pointerId);
		});
	}

	/**
	 * The preview container.
	 *
	 * @return the preview container
	 */
	private createPreview(): HTMLElement {
		return create(
			'div',
			[
				CSS.responsiveImageSettingsAreaPreview,
				CSS.responsiveImageSettingsPreview,
			],
			{},
			this
		);
	}
}

customElements.define(
	ResponsiveImageSettingsComponent.TAG_NAME,
	ResponsiveImageSettingsComponent
);
