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

import {
	classes,
	htmlSpecialChars,
	listen,
	text,
	titleCase,
} from '../utils/core';
import { create } from '../utils/create';
import { BaseComponent } from './BaseComponent';

/**
 * Create an ID from a field key.
 *
 * @param {string} key
 * @returns {string}
 */
const createId = (key) => {
	return `am-field__${key.replace(/(?!^)([A-Z])/g, '-$1').toLowerCase()}`;
};

/**
 * Create a label text from a field key.
 *
 * @param {string} key
 * @returns {string}
 */
const createLabel = (key) => {
	return titleCase(key.replace(/\+(.)/, '+ $1'))
		.replace('+ ', '+')
		.replace('Color ', '')
		.replace('Checkbox ', '');
};

/**
 * A button that remove its parent field.
 *
 * @extends BaseComponent
 */
class FieldRemove extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		listen(this, 'click', () => {
			this.closest(`.${classes.field}`).remove();
		});
	}
}

/**
 * A standard input field with a label.
 *
 * @extends BaseComponent
 */
export class Field extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		this.classList.add(classes.field);
	}

	/**
	 * The field data.
	 *
	 * @param {Object} params
	 * @param {string} params.key
	 * @param {string} params.value
	 * @param {string} params.name
	 * @param {string} params.tooltip
	 * @param {string} params.label
	 * @param {boolean} params.removable
	 */
	set data({ key, value, name, tooltip, label, removable }) {
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

	/**
	 * Create a label.
	 */
	label() {
		const { id, label, tooltip, removable } = this._data;
		const removeButton = removable
			? '<am-field-remove><i class="bi bi-trash"></i></am-field-remove>'
			: '';
		const wrapper = create('div', [], {}, this);

		wrapper.innerHTML = `
			<label 
			class="${classes.fieldLabel}" 
			for="${id}" 
			title="${tooltip}">
				${label}
			</label>
			${removeButton}
		`;
	}

	/**
	 * Create an input field.
	 */
	input() {
		const { name, id, value } = this._data;
		const input = create(
			'input',
			[classes.input],
			{ id, name, value, type: 'text' },
			this
		);
	}

	/**
	 * Render the field.
	 */
	render() {
		this.label();
		this.input();
	}
}

/**
 * A block editor field.
 *
 * @extends Field
 */
class FieldEditor extends Field {
	/**
	 * Render the field.
	 */
	render() {
		super.render();
	}
}

/**
 * A checkbox field.
 *
 * @extends Field
 */
class FieldCheckbox extends Field {
	/**
	 * Render the field.
	 */
	render() {
		super.render();
	}
}

/**
 * A large checkbox field.
 *
 * @extends Field
 */
class FieldCheckboxLarge extends Field {
	/**
	 * Render the field.
	 */
	render() {
		super.render();
	}
}

/**
 * A checkbox field that can have a default global value.
 *
 * @extends Field
 */
class FieldCheckboxPage extends Field {
	/**
	 * Render the field.
	 */
	render() {
		super.render();
	}
}

/**
 * A color field.
 *
 * @extends Field
 */
class FieldColor extends Field {
	/**
	 * Render the field.
	 */
	render() {
		super.render();
	}
}

/**
 * A date field.
 *
 * @extends Field
 */
class FieldDate extends Field {
	/**
	 * Render the field.
	 */
	render() {
		super.render();
	}
}

/**
 * A Markdown editor field.
 *
 * @extends Field
 */
class FieldMarkdown extends Field {
	/**
	 * Render the field.
	 */
	render() {
		super.render();
	}
}

/**
 * An image field.
 *
 * @extends Field
 */
class FieldImage extends Field {
	/**
	 * Render the field.
	 */
	render() {
		super.render();
	}
}

/**
 * A multiline text field.
 *
 * @extends Field
 */
class FieldTextarea extends Field {
	/**
	 * Render the field.
	 */
	render() {
		super.render();
	}
}

/**
 * An URL field.
 *
 * @extends Field
 */
class FieldURL extends Field {
	/**
	 * Render the field.
	 */
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
