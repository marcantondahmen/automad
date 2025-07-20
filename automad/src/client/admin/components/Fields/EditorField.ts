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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	create,
	createEditor,
	CSS,
	debounce,
	FieldTag,
	fire,
	FormDataProviders,
	listen,
	listenToClassChange,
	query,
} from '@/admin/core';
import { BaseFieldComponent } from './BaseField';
import { EditorOutputData, KeyValueMap, UndoValue } from '@/admin/types';
import { LayoutTune } from '@/admin/editor/tunes/Layout';
import { EditorJSComponent } from '@/admin/components/EditorJS';
import { API } from 'automad-editorjs';
import { filterEmptyData } from '@/admin/editor/utils';

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
					const { blocks: raw } =
						(await api.saver.save()) as EditorOutputData;

					const blocks = raw.map((block) => {
						block.tunes = filterEmptyData<KeyValueMap>(block.tunes);

						return block;
					});

					if (
						JSON.stringify(this.value.blocks) ===
						JSON.stringify(blocks)
					) {
						return;
					}

					this.value = {
						blocks: [...blocks],
						automadVersion: App.version,
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
		this.value = value;

		if (value.blocks?.length > 0) {
			await this.editorJS.editor.render(value);
		} else {
			this.editorJS.editor.clear();
		}

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
		// When forward slash is pressed.
		this.addListener(
			listen(this, 'keydown', (event: KeyboardEvent) => {
				if (event.key != '/') {
					return;
				}

				const target = event.target as HTMLElement;
				const block = target.closest<HTMLElement>('.ce-block');

				LayoutTune.updateToolbarPosition(block);
			})
		);

		// On mouseover.
		this.addListener(
			listen(
				this,
				'mouseover',
				debounce((event: Event) => {
					event.stopPropagation();

					const target = event.target as HTMLElement;
					const block = target.closest<HTMLElement>('.ce-block');

					LayoutTune.updateToolbarPosition(block);
				}, 10),
				'.ce-block'
			)
		);
	}
}

FormDataProviders.add(FieldTag.editor);
customElements.define(FieldTag.editor, EditorFieldComponent);
