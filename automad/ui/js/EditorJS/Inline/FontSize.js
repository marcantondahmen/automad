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

class AutomadFontSize extends AutomadInlineTool {
	static get title() {
		return 'Font Size';
	}

	static get sanitize() {
		return {
			'am-fontsize': true,
		};
	}

	get tag() {
		return 'AM-FONTSIZE';
	}

	get icon() {
		return '<path d="M7.14,2h12.2C19.7,2,20,2.3,20,2.66v1.82c0,0.36-0.3,0.66-0.66,0.66h-3.53c-0.36,0-0.66,0.3-0.66,0.66v11.54 c0,0.36-0.3,0.66-0.66,0.66H12c-0.36,0-0.66-0.3-0.66-0.66V5.8c0-0.36-0.3-0.66-0.66-0.66H7.14c-0.36,0-0.66-0.3-0.66-0.66V2.66 C6.48,2.3,6.77,2,7.14,2z"/><path d="M0.45,7h8.39C9.09,7,9.3,7.2,9.3,7.45v1.25c0,0.25-0.2,0.45-0.45,0.45H6.42c-0.25,0-0.45,0.2-0.45,0.45v7.93 c0,0.25-0.2,0.45-0.45,0.45H3.79c-0.25,0-0.45-0.2-0.45-0.45V9.61c0-0.25-0.2-0.45-0.45-0.45H0.45C0.2,9.16,0,8.96,0,8.71V7.45 C0,7.2,0.2,7,0.45,7z"/>';
	}

	get options() {
		return [
			'70%',
			'80%',
			'90%',
			'100%',
			'110%',
			'120%',
			'130%',
			'140%',
			'150%',
			'160%',
			'170%',
			'180%',
			'190%',
			'200%',
		];
	}

	renderActions() {
		const create = Automad.Util.create,
			label = create.label(AutomadFontSize.title);

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
		const { fontSize } = node.style;

		this.select.value = fontSize ? fontSize : '100%';
		node.style.fontSize = this.select.value;

		this.select.onchange = () => {
			node.style.fontSize = this.select.value;
		};

		this.wrapper.hidden = false;
	}

	hideActions() {
		this.select.onchange = null;
		this.wrapper.hidden = true;
	}
}
