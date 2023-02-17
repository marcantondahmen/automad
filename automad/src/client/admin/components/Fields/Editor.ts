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

import { App, create, fire, FormDataProviders } from '../../core';
import { BaseFieldComponent } from './BaseField';
import EditorJS from '@editorjs/editorjs';
import { EditorOutputData } from '../../types';

/**
 * A block editor field.
 *
 * @extends BaseFieldComponent
 */
class EditorComponent extends BaseFieldComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 * @readonly
	 */
	static readonly TAG_NAME = 'am-editor';

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

		const editor = new EditorJS({
			holder: create('div', [], { id }, this),
			data: this.value,

			onChange: async (api, event) => {
				this.value = (await editor.save()) as EditorOutputData;
				this.value['automadVersion'] = App.version;

				fire('input', this);
			},
		});
	}
}

FormDataProviders.add(EditorComponent.TAG_NAME);
customElements.define(EditorComponent.TAG_NAME, EditorComponent);
