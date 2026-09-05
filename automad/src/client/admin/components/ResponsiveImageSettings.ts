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
	CSS,
	FieldTag,
	collectFieldData,
	create,
	createField,
	debounce,
	fire,
	html,
	resizeImageUrl,
	resolveFileUrl,
	uniqueId,
	type AspectRatioBreakpoints,
} from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';
import { FocalPointPickerComponent } from './FocalPointPicker';
import type { FocalPoint } from '@/admin/types';
import { AspectRatioBreakpointsFieldComponent } from './Fields/AspectRatioBreakpointsField';

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
	private _focalPoint: FocalPoint = null;

	private set focalPoint(focalPoint: FocalPoint) {
		this._focalPoint = focalPoint;

		fire('change', this);
		this.update();
	}

	get focalPoint(): FocalPoint {
		return this._focalPoint;
	}

	/**
	 * The breakpoints.
	 */
	private _breakpoints: AspectRatioBreakpoints = {};

	private set breakpoints(breakpoints: AspectRatioBreakpoints) {
		this._breakpoints = breakpoints;

		fire('change', this);
		this.update();
	}

	get breakpoints(): AspectRatioBreakpoints {
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
		breakpoints: AspectRatioBreakpoints,
		focalPoint: FocalPoint
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
						src="${resizeImageUrl(this.image)}"
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
			[
				CSS.responsiveImageSettingsAreaBreakpointsHelp,
				CSS.textWrapPretty,
			],
			{},
			this,
			App.text('aspectRatioBreakpointsHelp')
		);

		const fieldWrapper = create(
			'div',
			[CSS.responsiveImageSettingsAreaBreakpoints],
			{},
			this
		);

		const input = createField<AspectRatioBreakpointsFieldComponent>(
			FieldTag.aspectRatioBreakpoints,
			fieldWrapper,
			{
				value: this.breakpoints,
				name: 'breakpoints',
				key: uniqueId(),
			}
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
				const [maxWidth, aspectRatio] = p.split(':');

				input.value = { ...input.value, [maxWidth]: { aspectRatio } };

				fire('input', input);
			});
		});

		this.listen(
			input,
			'input',
			debounce(() => {
				this.breakpoints = input.value;
			}, 200)
		);
	}

	/**
	 * The focal point picker.
	 */
	private createFocalPointPicker(): void {
		create(
			'small',
			[CSS.responsiveImageSettingsAreaFocalPointHelp, CSS.textWrapPretty],
			{},
			this,
			App.text('focalPointHelp')
		);

		const picker = create<FocalPointPickerComponent>(
			FocalPointPickerComponent.TAG_NAME,
			[CSS.responsiveImageSettingsAreaFocalPoint],
			{},
			this
		);

		picker.init(this.image, this.focalPoint);

		this.listen(picker, 'change', () => {
			this.focalPoint = picker.value;
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
