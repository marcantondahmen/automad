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

class AutomadLineHeight extends AutomadInlineTool {
	static get title() {
		return 'Line Height';
	}

	static get sanitize() {
		return {
			'am-lineheight': true,
		};
	}

	get tag() {
		return 'AM-LINEHEIGHT';
	}

	get icon() {
		return '<path d="M9,20H1c-0.55,0-1-0.45-1-1v0c0-0.55,0.45-1,1-1h8c0.55,0,1,0.45,1,1v0C10,19.55,9.55,20,9,20z"/><path d="M1,0l18,0c0.55,0,1,0.45,1,1v0c0,0.55-0.45,1-1,1H1C0.45,2,0,1.55,0,1v0C0,0.45,0.45,0,1,0z"/><path d="M8.75,6h10.68C19.74,6,20,6.26,20,6.58v1.59c0,0.32-0.26,0.58-0.58,0.58h-3.09c-0.32,0-0.58,0.26-0.58,0.58v10.1 c0,0.32-0.26,0.58-0.58,0.58H13c-0.32,0-0.58-0.26-0.58-0.58V9.33c0-0.32-0.26-0.58-0.58-0.58h-3.1c-0.32,0-0.58-0.26-0.58-0.58 V6.58C8.17,6.26,8.43,6,8.75,6z"/>';
	}

	get options() {
		return [
			'inherit',
			'0.8',
			'0.9',
			'1.0',
			'1.1',
			'1.2',
			'1.3',
			'1.4',
			'1.5',
			'1.6',
			'1.7',
			'1.8',
			'1.9',
			'2.0',
		];
	}

	renderActions() {
		const create = Automad.Util.create,
			label = create.label(AutomadLineHeight.title);

		this.select = create.select(
			[this.cls.input],
			this.options,
			this.selected
		);
		this.wrapper = create.element('span', [this.cls.wrapper]);
		this.wrapper.appendChild(label);
		this.wrapper.appendChild(this.select);
		this.wrapper.hidden = true;

		return this.wrapper;
	}

	showActions(node) {
		const { lineHeight } = node.style;

		this.select.value = lineHeight ? lineHeight : 'inherit';
		node.style.lineHeight = this.select.value;
		node.style.display = 'inline-block';

		this.select.onchange = () => {
			node.style.lineHeight = this.select.value;
		};

		this.wrapper.hidden = false;
	}

	hideActions() {
		this.select.onchange = null;
		this.wrapper.hidden = true;
	}
}
