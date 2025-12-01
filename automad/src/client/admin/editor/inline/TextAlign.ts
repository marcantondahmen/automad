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

import { API, InlineToolConstructorOptions } from '@/admin/vendor/editorjs';
import { App, create, query } from '@/admin/core';
import {
	KeyValueMap,
	TextAlignOption,
	TextAlignSelection,
} from '@/admin/types';

abstract class BaseTextAlignInline {
	/**
	 * The align option.
	 */
	protected abstract align: TextAlignOption;

	/**
	 * The editor API.
	 */
	private api: API;

	/**
	 * Make tool inline.
	 *
	 * @static
	 */
	static get isInline() {
		return true;
	}

	/**
	 * The enabled or disabled state for the selection.
	 */
	private _state: boolean;

	/**
	 * The state getter.
	 */
	get state() {
		return this._state;
	}

	/**
	 * The state setter.
	 */
	set state(state) {
		this._state = state;

		this.button.classList.toggle(
			this.api.styles.inlineToolButtonActive,
			state
		);
	}

	/**
	 * The tool sanitize config.
	 *
	 * @static
	 */
	static get sanitize(): KeyValueMap {
		return {
			'am-inline-align': true,
		};
	}

	/**
	 * The tag that is used to wrap the paragraph.
	 */
	private tag = 'AM-INLINE-ALIGN';

	/**
	 * The toolbar icon button.
	 */
	private button: HTMLElement;

	/**
	 * The temporay selection cache.
	 */
	private selection: TextAlignSelection;

	/**
	 * The tool constructor.
	 *
	 * @param params
	 */
	constructor({ api }: InlineToolConstructorOptions) {
		this.api = api;

		this.button = create(
			'button',
			[this.api.styles.inlineToolButton],
			{ type: 'button' },
			null
		);
	}

	/**
	 * Render the toolbar button.
	 *
	 * @return the button element
	 */
	render(): HTMLElement {
		this.button.innerHTML = `<i class="bi bi-text-${this.align}"></i>`;

		return this.button;
	}

	/**
	 * Is called when the toolbar button is clicked.
	 *
	 * @param range
	 */
	surround(): void {
		this.saveSelection();

		if (this.state) {
			const node = this.api.selection.findParentTag(this.tag);

			if (node) {
				this.api.selection.expandToTag(node);

				const fullRange = window.getSelection().getRangeAt(0);
				const unwrappedContent = fullRange.extractContents();

				node.remove();
				fullRange.insertNode(unwrappedContent);
			}
		} else {
			const createNode = (): HTMLElement => {
				const div = this.api.selection.findParentTag('DIV');

				this.api.selection.expandToTag(div);

				const fullRange = window.getSelection().getRangeAt(0);
				const unwrappedContent = fullRange.extractContents();

				const node = create(this.tag, [], {});

				node.appendChild(unwrappedContent);
				fullRange.insertNode(node);

				return node;
			};

			let node =
				this.api.selection.findParentTag(this.tag) ?? createNode();

			node.style.textAlign = this.align;
			node.style.display = 'block';
		}

		this.restoreSelection();
	}

	/**
	 * Check the state of the selection.
	 */
	checkState(): void {
		const node = this.api.selection.findParentTag(this.tag);

		if (node) {
			const style = node.getAttribute('style');
			const regex = new RegExp(this.align, 'g');

			if (style?.match(regex)) {
				this.state = true;

				return;
			}
		}

		this.state = false;
	}

	/**
	 * Cache the selection.
	 */
	private saveSelection(): void {
		const sel = window.getSelection();

		this.selection = {
			start: sel.anchorOffset,
			end: sel.focusOffset,
			div: this.api.selection.findParentTag('DIV'),
		};
	}

	/**
	 * Restore the selection.
	 */
	private restoreSelection(): void {
		const selection = window.getSelection();

		selection.removeAllRanges();

		const node = query(
			`:scope > ${this.tag.toLowerCase()}`,
			this.selection.div
		);

		const element = node ?? this.selection.div;

		selection.setBaseAndExtent(
			element.childNodes[0],
			this.selection.start,
			element.childNodes[0],
			this.selection.end
		);
	}
}

export class TextAlignLeftInline extends BaseTextAlignInline {
	/**
	 * The align option.
	 */
	protected align: TextAlignOption = 'left';

	/**
	 * The tool title.
	 *
	 * @static
	 */
	static get title(): string {
		return App.text('alignLeft');
	}
}

export class TextAlignCenterInline extends BaseTextAlignInline {
	/**
	 * The align option.
	 */
	protected align: TextAlignOption = 'center';

	/**
	 * The tool title.
	 *
	 * @static
	 */
	static get title(): string {
		return App.text('alignLeft');
	}
}

export class TextAlignRightInline extends BaseTextAlignInline {
	/**
	 * The align option.
	 */
	protected align: TextAlignOption = 'right';

	/**
	 * The tool title.
	 *
	 * @static
	 */
	static get title(): string {
		return App.text('alignLeft');
	}
}
