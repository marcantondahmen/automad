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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

class AutomadColor extends AutomadInlineTool {
	static get title() {
		return 'Text Color';
	}

	static get sanitize() {
		return {
			'am-color': true,
		};
	}

	get tag() {
		return 'AM-COLOR';
	}

	get icon() {
		return '<path d="M18,20H2c-1.1,0-2-0.9-2-2v0c0-1.1,0.9-2,2-2h16c1.1,0,2,0.9,2,2v0C20,19.1,19.1,20,18,20z"/><path d="M3.61,13.01l4.32-12.5C8.03,0.2,8.31,0,8.63,0h2.75c0.32,0,0.6,0.2,0.71,0.51l4.31,12.5C16.56,13.49,16.2,14,15.69,14h-1.58 c-0.32,0-0.61-0.21-0.71-0.52l-0.7-2.16c-0.1-0.31-0.39-0.52-0.71-0.52H8.02c-0.32,0-0.61,0.21-0.71,0.52l-0.7,2.16 C6.5,13.79,6.22,14,5.89,14H4.32C3.8,14,3.44,13.49,3.61,13.01z M10.75,8.5c0.51,0,0.87-0.5,0.71-0.98l-1.4-4.32H9.95l-1.4,4.32 C8.39,8,8.75,8.5,9.26,8.5H10.75z"/>';
	}

	get defaultColor() {
		return '#FF5252';
	}

	renderActions() {
		const create = Automad.Util.create,
			label = create.label(this.constructor.title);

		this.colorPicker = create.element('input', [this.cls.input]);
		this.colorPicker.type = 'color';
		this.colorPicker.value = this.defaultColor;

		this.wrapper = create.element('span', [this.cls.wrapper]);
		this.wrapper.appendChild(label);
		this.wrapper.appendChild(this.colorPicker);
		this.wrapper.hidden = true;

		return this.wrapper;
	}

	showActions(node) {
		const { color } = node.style;
		this.colorPicker.value = color
			? this.convertToHex(color)
			: this.defaultColor;
		node.style.color = this.colorPicker.value;

		this.colorPicker.onchange = () => {
			node.style.color = this.colorPicker.value;
		};

		this.wrapper.hidden = false;
	}

	hideActions() {
		this.colorPicker.onchange = null;
		this.wrapper.hidden = true;
	}

	convertToHex(color) {
		const rgb = color.match(/(\d+)/g);

		let hexr = parseInt(rgb[0]).toString(16);
		let hexg = parseInt(rgb[1]).toString(16);
		let hexb = parseInt(rgb[2]).toString(16);

		hexr = hexr.length === 1 ? '0' + hexr : hexr;
		hexg = hexg.length === 1 ? '0' + hexg : hexg;
		hexb = hexb.length === 1 ? '0' + hexb : hexb;

		return '#' + hexr + hexg + hexb;
	}
}

class AutomadBackground extends AutomadColor {
	static get title() {
		return 'Background';
	}

	static get sanitize() {
		return {
			'am-background': true,
		};
	}

	get tag() {
		return 'AM-BACKGROUND';
	}

	get icon() {
		return '<path d="M3.55,14l-2.91,2.91C0.24,17.31,0.52,18,1.09,18h6.13c0.21,0,0.41-0.08,0.55-0.23L8,17.55c0.31-0.31,0.31-0.8,0-1.11 L5.1,13.55c-0.31-0.31-0.8-0.31-1.11,0L3.55,14z"/><path d="M19.26,5.71l-3.41-3.41C15.65,2.11,15.4,2,15.14,2h-1.17c-0.27,0-0.52,0.11-0.71,0.29L5.84,9.71 c-0.19,0.19-0.29,0.44-0.29,0.71v1.17c0,0.27,0.11,0.52,0.29,0.71l3.41,3.41C9.44,15.89,9.7,16,9.96,16h1.17 c0.27,0,0.52-0.11,0.71-0.29l7.41-7.41c0.19-0.19,0.29-0.44,0.29-0.71V6.41C19.55,6.15,19.44,5.89,19.26,5.71z"/>';
	}

	get defaultColor() {
		return '#FFFDC7';
	}

	showActions(node) {
		const { backgroundColor } = node.style;
		this.colorPicker.value = backgroundColor
			? this.convertToHex(backgroundColor)
			: this.defaultColor;
		node.style.backgroundColor = this.colorPicker.value;

		this.colorPicker.onchange = () => {
			node.style.backgroundColor = this.colorPicker.value;
		};
		this.wrapper.hidden = false;
	}
}
