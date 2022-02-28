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
 */

import { create } from '../../core';
import { KeyValueMap } from '../../types';
import { FieldComponent } from './Field';

/**
 * A block editor field.
 *
 * @extends FieldComponent
 */
class EditorComponent extends FieldComponent {
	/**
	 * The editor value that serves a input value for the parent form.
	 */
	value: KeyValueMap;

	/**
	 * If true the field data is sanitized.
	 */
	protected sanitize = false;

	/**
	 * Render the field.
	 */
	renderInput(): void {
		const { name, id, value } = this._data;
		const editor = create('div', [], { id }, this);

		this.setAttribute('name', name);
		this.value = value as KeyValueMap;
	}
}

customElements.define('am-editor', EditorComponent);
