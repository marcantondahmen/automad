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

import { htmlSpecialChars, listen, titleCase } from '../utils/core';
import { create } from '../utils/create';
import { BaseComponent } from './BaseComponent';

const createId = (key) => {
	return `am-field__${key.replace(/(?!^)([A-Z])/g, '-$1').toLowerCase()}`;
};

const createLabel = (key) => {
	return titleCase(key.replace(/\+(.)/, '+ $1'))
		.replace('+ ', '+')
		.replace('Color ', '')
		.replace('Checkbox ', '');
};

class FieldRemove extends BaseComponent {
	connectedCallback() {
		listen(this, 'click', () => {
			this.closest(`.${this.cls.field}`).remove();
		});
	}
}

export class Field extends BaseComponent {
	connectedCallback() {
		this.classList.add(this.cls.field);
	}

	set data({ key, value, name, tooltip, removable, label }) {
		const id = createId(key);

		value = value || '';
		tooltip = tooltip || '';
		label = label || createLabel(key);

		if (typeof value === 'string' || value instanceof String) {
			value = htmlSpecialChars(value);
		}

		this._data = {
			name,
			id,
			label,
			value,
			tooltip,
			removable,
		};

		this.render();
	}

	label() {
		const { id, label, tooltip, removable } = this._data;
		const removeButton = removable
			? '<am-field-remove><i class="bi bi-trash"></i></am-field-remove>'
			: '';
		const wrapper = create('div', [], {}, this);

		wrapper.innerHTML = `
			<label 
			class="${this.cls.fieldLabel}" 
			for="${id}" 
			title="${tooltip}">
				${label}
			</label>
			${removeButton}
		`;
	}

	input() {
		const { name, id, value } = this._data;
		const input = create(
			'input',
			[this.cls.input],
			{ id, name, value, type: 'text' },
			this
		);
	}

	render() {
		this.label();
		this.input();
	}
}

class FieldEditor extends Field {
	render() {
		super.render();
	}
}

class FieldCheckbox extends Field {
	render() {
		super.render();
	}
}

class FieldCheckboxLarge extends Field {
	render() {
		super.render();
	}
}

class FieldCheckboxPage extends Field {
	render() {
		super.render();
	}
}

class FieldColor extends Field {
	render() {
		super.render();
	}
}

class FieldDate extends Field {
	render() {
		super.render();
	}
}

class FieldMarkdown extends Field {
	render() {
		super.render();
	}
}

class FieldImage extends Field {
	render() {
		super.render();
	}
}

class FieldTextarea extends Field {
	render() {
		super.render();
	}
}

class FieldURL extends Field {
	render() {
		super.render();
	}
}

customElements.define('am-field-remove', FieldRemove);
customElements.define('am-field', Field);
customElements.define('am-field-editor', FieldEditor);
customElements.define('am-field-checkbox', FieldCheckbox);
customElements.define('am-field-checkbox-large', FieldCheckboxLarge);
customElements.define('am-field-checkbox-page', FieldCheckboxPage);
customElements.define('am-field-color', FieldColor);
customElements.define('am-field-date', FieldDate);
customElements.define('am-field-markdown', FieldMarkdown);
customElements.define('am-field-image', FieldImage);
customElements.define('am-field-textarea', FieldTextarea);
customElements.define('am-field-url', FieldURL);
