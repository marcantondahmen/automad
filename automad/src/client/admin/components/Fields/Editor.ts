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

import { create, FormDataProviders } from '../../core';
import { KeyValueMap } from '../../types';
import { BaseFieldComponent } from './BaseField';

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
	value: KeyValueMap;

	/**
	 * Render the field.
	 */
	createInput(): void {
		const { name, id, value } = this._data;
		const editor = create('div', [], { id }, this);

		this.setAttribute('name', name);
		this.value = value as KeyValueMap;
	}
}

FormDataProviders.add(EditorComponent.TAG_NAME);
customElements.define(EditorComponent.TAG_NAME, EditorComponent);
