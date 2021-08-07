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

class AutomadInlineTool {
	static get isInline() {
		return true;
	}

	static get title() {}

	static get sanitize() {}

	get cls() {
		return {
			input: 'am-inline-tool-input',
			wrapper: 'am-inline-tool-action',
		};
	}

	get tag() {}

	get icon() {}

	get state() {
		return this._state;
	}

	set state(state) {
		this._state = state;
		this.button.classList.toggle(
			this.api.styles.inlineToolButtonActive,
			state
		);
	}

	constructor({ api }) {
		this.api = api;
		this.button = null;
		this._state = false;
	}

	render() {
		this.button = document.createElement('button');
		this.button.type = 'button';
		this.button.innerHTML = `<svg width="20px" height="20px" viewBox="0 0 20 20">${this.icon}</svg>`;
		this.button.classList.add(this.api.styles.inlineToolButton);

		return this.button;
	}

	surround(range) {
		if (this.state) {
			this.unwrap();
			return;
		}

		this.wrap(range);
	}

	wrap(range) {
		const selectedText = range.extractContents(),
			node = document.createElement(this.tag);

		node.appendChild(selectedText);
		range.insertNode(node);

		this.api.selection.expandToTag(node);
	}

	unwrap() {
		try {
			const node = this.api.selection.findParentTag(this.tag);

			this.api.selection.expandToTag(node);

			const sel = window.getSelection(),
				range = sel.getRangeAt(0),
				unwrappedContent = range.extractContents();

			node.remove();
			range.insertNode(unwrappedContent);
		} catch (e) {}
	}

	checkState() {
		const node = this.api.selection.findParentTag(this.tag);

		this.state = !!node;

		if (
			typeof this.showActions !== 'undefined' &&
			typeof this.hideActions !== 'undefined'
		) {
			if (this.state) {
				this.showActions(node);
			} else {
				this.hideActions();
			}
		}
	}
}
