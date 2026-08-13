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
	CSS,
	aspectRatioBreakpointsFromString,
	aspectRatioBreakpointsToString,
	collectFieldData,
	create,
	debounce,
	fire,
	html,
	resolveFileUrl,
} from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';
import { AspectRatioBreakpoints, FocalPoint } from '@/admin/types';
import { FocalPointPickerComponent } from './FocalPointPicker';

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
			App.text('aspectRatioBreakpointsHelp')
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
				[Attr.error]: App.text('aspectRatioBreakpointsError'),
			},
			fieldWrapper
		);

		const input = create<HTMLInputElement>(
			'input',
			[CSS.input, CSS.textMono],
			{
				type: 'text',
				value: aspectRatioBreakpointsToString(this.breakpoints),
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

				this.breakpoints = aspectRatioBreakpointsFromString(
					data.breakpointsString
				);
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
