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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	create,
	createEditor,
	CSS,
	debounce,
	fire,
	FormDataProviders,
	getLogger,
	listen,
	listenToClassChange,
	queryAll,
} from '@/core';
import { BaseFieldComponent } from './BaseField';
import { EditorOutputData, UndoValue } from '@/types';
import { LayoutTune } from '@/editor/tunes/Layout';
import { EditorJSComponent } from '../EditorJS';

/**
 * A block editor field.
 *
 * @extends BaseFieldComponent
 */
export class EditorComponent extends BaseFieldComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 * @readonly
	 */
	static readonly TAG_NAME = 'am-editor';

	/**
	 * The editor component.
	 */
	private editorJS: EditorJSComponent;

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

		this.editorJS = createEditor(
			create('div', [], { id }, this),
			this.value,
			{
				onChange: async (api, event) => {
					getLogger().log(event);

					const _value = (await api.saver.save()) as EditorOutputData;

					if (
						JSON.stringify(this.value.blocks) ===
						JSON.stringify(_value.blocks)
					) {
						return;
					}

					this.value = _value;
					this.value['automadVersion'] = App.version;

					fire('input', this);
				},
			},
			false
		);

		this.attachToolbarPositionObserver();
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
	mutate(value: UndoValue): void {
		this.editorJS.editor.render(value);
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
	 * Attach observer and listeners in order to update the toolbar positions within sections.
	 * Note that this should be done here in the parent component in order to be able to properly detach and destroy
	 * listeners and observers after changing views.
	 */
	private attachToolbarPositionObserver(): void {
		this.addListener(
			listenToClassChange(this, (mutation) => {
				const target = mutation.target as HTMLElement;

				if (target.className.indexOf('ce-block--focused') === -1) {
					return;
				}

				LayoutTune.updateToolbarPosition(target);
			})
		);

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

		// This is required to hide the section layout toolbar when clicking outside of the editor.
		this.addListener(
			listen(window, 'click', (event: Event) => {
				event.stopPropagation();

				queryAll(
					`.${CSS.editorBlockSectionSettings}.${CSS.active}`
				).forEach((item) => {
					item.classList.remove(CSS.active);
				});
			})
		);
	}
}

FormDataProviders.add(EditorComponent.TAG_NAME);
customElements.define(EditorComponent.TAG_NAME, EditorComponent);
