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

import { ColorFieldComponent } from '@/admin/components/Fields/ColorField';
import {
	App,
	convertRgbToHex,
	create,
	createField,
	CSS,
	FieldTag,
	fire,
	listen,
	uniqueId,
} from '@/admin/core';
import { KeyValueMap } from '@/admin/types';
import { BaseInline } from './BaseInline';

export class ColorInline extends BaseInline {
	/**
	 * The tool title.
	 *
	 * @static
	 */
	static get title(): string {
		return App.text('color');
	}

	/**
	 * The tool sanitize config.
	 *
	 * @static
	 */
	static get sanitize(): KeyValueMap {
		return {
			'am-inline-color': true,
		};
	}

	/**
	 * The tool tag.
	 */
	get tag(): string {
		return 'AM-INLINE-COLOR';
	}

	/**
	 * The tool icon.
	 */
	get icon(): string {
		return '<i class="bi bi-paint-bucket"></i>';
	}

	/**
	 * The menu wrapper.
	 */
	private wrapper: HTMLElement;

	/**
	 * Text-color picker.
	 */
	private textColorPicker: ColorFieldComponent;

	/**
	 * Background-color picker.
	 */
	private backgroundColorPicker: ColorFieldComponent;

	/**
	 * Render the menu fields.
	 *
	 * @return the rendered fields
	 */
	renderActions(): HTMLElement {
		this.wrapper = create('div', [CSS.grid, CSS.gridAuto], {});

		this.textColorPicker = createField(FieldTag.color, this.wrapper, {
			key: uniqueId(),
			value: '',
			name: 'color',
			label: App.text('textColor'),
		}) as ColorFieldComponent;

		this.backgroundColorPicker = createField(FieldTag.color, this.wrapper, {
			key: uniqueId(),
			value: '',
			name: 'backgroundColor',
			label: App.text('backgroundColor'),
		}) as ColorFieldComponent;

		return this.wrapper;
	}

	/**
	 * Init the fields.
	 */
	showActions(node: HTMLAnchorElement): void {
		const { color, backgroundColor } = node.style;

		if (color) {
			this.textColorPicker.mutate(convertRgbToHex(color));
			fire('change', this.textColorPicker.input);
		}

		if (backgroundColor) {
			this.backgroundColorPicker.mutate(convertRgbToHex(backgroundColor));
			fire('change', this.backgroundColorPicker.input);
		}

		this.listen(this.wrapper, 'keyup change', () => {
			node.style.color = this.textColorPicker.query();
			node.style.backgroundColor = this.backgroundColorPicker.query();
		});

		this.wrapper.hidden = false;
	}

	/**
	 * Hide the fields.
	 */
	hideActions(): void {
		this.wrapper.hidden = true;
	}
}
