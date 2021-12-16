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

import { BaseComponent } from './BaseComponent';

class Field extends BaseComponent {
	connectedCallback() {
		this.classList.add(this.cls.field);
	}

	set data({ key, value, name, tooltip }) {
		const id = this.createId(key);
		const label = this.createLabel(key);

		if (typeof value === 'undefined') {
			value = '';
		}

		value = this.convertHtmlSpecialChars(value);

		this.innerHTML = this.render({ name, id, label, value, tooltip });
	}

	convertHtmlSpecialChars(value) {
		const chars = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;',
		};

		return value.replace(/[&<>"']/g, (char) => {
			return chars[char];
		});
	}

	createId(key) {
		return `am-field__${key.replace(/(?!^)([A-Z])/g, '-$1').toLowerCase()}`;
	}

	createLabel(key) {
		return key
			.replace(/(?!^)([A-Z])/g, ' $1')
			.replace(/\+(.)/, '+ $1')
			.split(' ')
			.map((word) => word.charAt(0).toUpperCase() + word.slice(1))
			.join(' ')
			.replace('+ ', '+')
			.replace('Checkbox ', '');
	}

	render({ name, id, label, value, tooltip }) {
		return `
			<label for="${id}" title="${tooltip}">${label}</label>
			<textarea 
			id="${id}" 
			name="${name}" 
			class="${this.cls.input}"
			>${value}</textarea>
		`;
	}
}

class FieldEditor extends Field {
	render({ name, id, label, value, tooltip }) {
		return super.render({ name, id, label, value, tooltip });
	}
}

class FieldCheckbox extends Field {
	render({ name, id, label, value, tooltip }) {
		return super.render({ name, id, label, value, tooltip });
	}
}

class FieldColor extends Field {
	render({ name, id, label, value, tooltip }) {
		return super.render({ name, id, label, value, tooltip });
	}
}

class FieldDate extends Field {
	render({ name, id, label, value, tooltip }) {
		return super.render({ name, id, label, value, tooltip });
	}
}

class FieldMarkdown extends Field {
	render({ name, id, label, value, tooltip }) {
		return super.render({ name, id, label, value, tooltip });
	}
}

class FieldImage extends Field {
	render({ name, id, label, value, tooltip }) {
		return super.render({ name, id, label, value, tooltip });
	}
}

class FieldURL extends Field {
	render({ name, id, label, value, tooltip }) {
		return super.render({ name, id, label, value, tooltip });
	}
}

customElements.define('am-field', Field);
customElements.define('am-field-editor', FieldEditor);
customElements.define('am-field-checkbox', FieldCheckbox);
customElements.define('am-field-color', FieldColor);
customElements.define('am-field-date', FieldDate);
customElements.define('am-field-markdown', FieldMarkdown);
customElements.define('am-field-image', FieldImage);
customElements.define('am-field-url', FieldURL);
