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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { API } from '@/vendor/editorjs';
import {
	Attr,
	create,
	createEditor,
	CSS,
	debounce,
	FieldTag,
	fire,
	FormDataProviders,
	listenToClassChange,
	query,
	type UndoValue,
} from '@/admin/core';
import { outputIsEqual, saveEditorBlocks } from '@/admin/editor/utils';
import { BaseFieldComponent } from './BaseField';
import { LayoutTune } from '@/admin/editor/tunes/Layout';
import { EditorJSComponent } from '@/admin/components/EditorJS';
import type { EditorOutputData } from '@/admin/editor/types';

/**
 * A block editor field.
 *
 * @extends BaseFieldComponent
 */
export class EditorFieldComponent extends BaseFieldComponent {
	/**
	 * The editor component.
	 */
	editorJS: EditorJSComponent;

	/**
	 * Don't link the label.
	 */
	protected linkLabel = false;

	/**
	 * The editor value that serves a input value for the parent form.
	 */
	value: EditorOutputData;

	/**
	 * Render the field.
	 */
	createInput(): void {
		const { name, id, value } = this._data;

		this.setAttribute('name', name);
		this.value = value as EditorOutputData;

		const wrapper = create('div', [], { id }, this);

		create(
			'am-alert',
			[CSS.displaySmall],
			{
				[Attr.icon]: 'window',
				[Attr.text]: 'editorSmallDisplayAlert',
			},
			wrapper
		);

		this.editorJS = createEditor(
			wrapper,
			{ blocks: this.value.blocks },
			{
				onChange: async (api: API) => {
					const blocks = await saveEditorBlocks(api);

					if (outputIsEqual(blocks, this.value.blocks)) {
						return;
					}

					this.value = {
						blocks: JSON.parse(JSON.stringify(blocks)),
					};

					fire('input', this);
				},
			},
			false
		);

		this.attachToolbarPositionObservers();
		this.attachPopupHeightObservers();
	}

	/**
	 * Return the field that is observed for changes.
	 *
	 * @return the input field
	 */
	getValueProvider(): HTMLElement {
		return this;
	}

	/**
	 * A function that can be used to mutate the field value.
	 *
	 * @param value
	 */
	async mutate(value: UndoValue): Promise<void> {
		const scrolled = window.scrollY;

		this.value = value;

		if (value.blocks?.length > 0) {
			await this.editorJS.editor.render(value);
		} else {
			this.editorJS.editor.clear();
		}

		window.scrollTo(0, scrolled);

		this.editorJS.onRender();
	}

	/**
	 * Query the current field value.
	 *
	 * @return the current value
	 */
	query() {
		return this.value;
	}

	/**
	 * Expand editor field height whenever a popup is opened.
	 */
	private attachPopupHeightObservers(): void {
		// Expand height of editor when toolbar is open.
		this.addListener(
			listenToClassChange(this, (mutation) => {
				const target = mutation.target as HTMLElement;

				if (!target.classList.contains('codex-editor')) {
					return;
				}

				if (
					!target.classList.contains('codex-editor--toolbox-opened')
				) {
					this.style.removeProperty('min-height');

					return;
				}

				const popover = query('.ce-popover--opened', target);

				setTimeout(() => {
					const popoverRect = popover.getBoundingClientRect();
					const editorRect = this.getBoundingClientRect();
					const minHeight =
						popoverRect.top - editorRect.top + popoverRect.height;
					this.style.minHeight = `${minHeight}px`;
				}, 0);
			})
		);

		// Expand height of editor when tunes popover is open.
		this.addListener(
			listenToClassChange(this, (mutation) => {
				const target = mutation.target as HTMLElement;

				if (!target.classList.contains('ce-popover')) {
					return;
				}

				if (!target.closest('.ce-settings')) {
					return;
				}

				if (!target.classList.contains('ce-popover--opened')) {
					this.style.removeProperty('min-height');

					return;
				}

				setTimeout(() => {
					const popoverRect = target.getBoundingClientRect();
					const editorRect = this.getBoundingClientRect();
					const minHeight =
						popoverRect.top - editorRect.top + popoverRect.height;
					this.style.minHeight = `${minHeight}px`;
				}, 0);
			})
		);
	}

	/**
	 * Attach observer and listeners in order to update the toolbar positions within sections.
	 * Note that this should be done here in the parent component in order to be able to properly detach and destroy
	 * listeners and observers after changing views.
	 */
	private attachToolbarPositionObservers(): void {
		const handler = debounce((event: Event) => {
			event.stopPropagation();

			const target = event.target as HTMLElement;
			const block = target.closest<HTMLElement>('.ce-block');

			LayoutTune.updateToolbarPosition(block);
		}, 5);

		// When forward slash is pressed.
		this.listen(this, 'keydown', (event: KeyboardEvent) => {
			if (event.key != '/') {
				return;
			}

			handler(event);
		});

		// On mouseover.
		this.listen(
			this,
			'mouseover mousemove',
			handler.bind(this),
			'.ce-block'
		);
	}
}

FormDataProviders.add(FieldTag.editor);
customElements.define(FieldTag.editor, EditorFieldComponent);
