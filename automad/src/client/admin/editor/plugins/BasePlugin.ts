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

import { EditorJS, BlockAPI } from '@/vendor/editorjs';
import { EditorJSComponent } from '@/admin/components/EditorJS';

/**
 * The abstract base plugin class.
 */
export abstract class BasePlugin {
	/**
	 * The component that is associated with plugin instance.
	 */
	protected component: EditorJSComponent;

	/**
	 * The EditorJS instance that is associated with plugin instance.
	 */
	protected editor: EditorJS;

	/**
	 * The plugin constructor.
	 *
	 * @param component
	 */
	constructor(component: EditorJSComponent) {
		this.component = component;
		this.editor = component.editor;

		this.init();
	}

	/**
	 * Get the currently selected (dragged) block.
	 *
	 * @return the current block
	 */
	protected getCurrentBlock(): BlockAPI {
		return this.editor.blocks.getBlockByIndex(
			this.editor.blocks.getCurrentBlockIndex()
		);
	}

	/**
	 * Get the index of a block element.
	 *
	 * @param element
	 * @return the index
	 * @static
	 */
	protected static getBlockIndex(element: HTMLElement): number {
		return Array.from(element.parentNode.children).indexOf(element);
	}

	/**
	 * Get the closest parent EditorJSComponent element for a given element.
	 *
	 * @param element
	 * @return the containing editor element
	 * @static
	 */
	protected static getParentEditorComponent(
		element: HTMLElement
	): EditorJSComponent {
		return element.closest<EditorJSComponent>(EditorJSComponent.TAG_NAME);
	}

	/**
	 * Set the selection of a block.
	 *
	 * @param element
	 * @static
	 */
	protected static setSelection(element: HTMLElement): void {
		if (!element) {
			return;
		}

		const range = document.createRange();
		const selection = window.getSelection();

		range.setStart(element.childNodes[0], 0);
		range.collapse(true);
		selection.removeAllRanges();
		selection.addRange(range);
		element.focus();
	}

	/**
	 * Re-render editor after a successful move.
	 *
	 * @param editor
	 * @static
	 */
	protected static async refresh(editor: EditorJS): Promise<void> {
		const saved = await editor.save();

		await editor.render(saved);
	}

	/**
	 * The actual plugin implementation.
	 */
	protected abstract init(): void;
}
