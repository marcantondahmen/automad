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

import { create } from '@/admin/core';
import { API, InlineToolConstructorOptions } from 'automad-editorjs';

/**
 * Define interface with same name in order to merge definition for base class.
 */
export interface BaseInline {
	readonly tag: string;
	readonly icon: string;
	renderActions(): HTMLElement;
	clear(): void;
}

/**
 * The abstract base class for inline tools.
 */
export abstract class BaseInline {
	/**
	 * Make tool inline.
	 *
	 * @static
	 */
	static get isInline() {
		return true;
	}

	/**
	 * The api object.
	 */
	private api: API;

	/**
	 * The enabled or disabled state for the selection.
	 */
	private _state = false;

	/**
	 * The temporay selection cache.
	 */
	private range: Range;

	/**
	 * The toolbar icon button.
	 */
	private button: HTMLElement;

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
			null,
			this.icon
		);
	}

	/**
	 * Render the toolbar button.
	 *
	 * @return the button element
	 */
	render(): HTMLElement {
		return this.button;
	}

	/**
	 * Is called when the toolbar button is clicked.
	 *
	 * @param range
	 */
	surround(range: Range): void {
		if (this.state) {
			this.unwrap();

			return;
		}

		this.wrap(range);
	}

	/**
	 * Wrap selection in the tool's tag.
	 *
	 * @param range
	 */
	wrap(range: Range): void {
		const selectedText = range.extractContents();
		const node = create(this.tag, [], {});

		node.appendChild(selectedText);
		range.insertNode(node);

		this.api.selection.expandToTag(node);
	}

	/**
	 * Unwrap the selection
	 */
	unwrap(): void {
		this.restoreSelection();

		const node = this.api.selection.findParentTag(this.tag);

		this.api.selection.expandToTag(node);

		const range = window.getSelection().getRangeAt(0);
		const unwrappedContent = range.extractContents();

		node.remove();
		range.insertNode(unwrappedContent);
	}

	/**
	 * Check the state of the selection.
	 */
	checkState(): boolean {
		const node = this.api.selection.findParentTag(this.tag);

		this.state = !!node;

		if (this.state) {
			this.saveSelection();

			setTimeout(() => {
				this.showActions(node);
			}, 0);
		} else {
			this.hideActions();
		}

		return this.state;
	}

	/**
	 * Cache the selection.
	 */
	private saveSelection(): void {
		this.range = window.getSelection().getRangeAt(0);
	}

	/**
	 * Restore the selection.
	 */
	private restoreSelection(): void {
		const selection = window.getSelection();

		selection.removeAllRanges();
		selection.addRange(this.range);
	}

	/**
	 * Runs when menu is shown.
	 *
	 * @param node
	 */
	protected showActions(node: HTMLElement): void {}

	/**
	 * Runs when menu is hidden.
	 */
	protected hideActions(): void {}
}
