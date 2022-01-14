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
import { BaseComponent } from './Base';

interface FieldInitData {
	key: string;
	value: string;
	name: string;
	tooltip: string;
	label: string;
	removable: boolean;
}

interface FieldRenderData extends Omit<FieldInitData, 'key'> {
	id: string;
}

/**
 * Create an ID from a field key.
 *
 * @param key
 * @returns the generated ID
 */
const createId = (key: string): string => {
	return `am-field__${key.replace(/(?!^)([A-Z])/g, '-$1').toLowerCase()}`;
};

/**
 * Create a label text from a field key.
 *
 * @param key
 * @returns the generated label
 */
const createLabel = (key: string): string => {
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
class FieldRemoveComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
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
export class FieldComponent extends BaseComponent {
	/**
	 * The internal field data.
	 */
	protected _data: FieldRenderData;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.field);
	}

	/**
	 * The field data.
	 *
	 * @param params
	 * @param params.key
	 * @param params.value
	 * @param params.name
	 * @param params.tooltip
	 * @param params.label
	 * @param params.removable
	 */
	set data({ key, value, name, tooltip, label, removable }: FieldInitData) {
		const id = createId(key);

		value = value || '';
		tooltip = tooltip || '';
		label = label || createLabel(key);

		if (removable) {
			label = `${label} (${text('page_var_unused')})`;
		}

		if (typeof value === 'string') {
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
	label(): void {
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
	input(): void {
		const { name, id, value } = this._data;
		create(
			'input',
			[classes.input],
			{ id, name, value, type: 'text' },
			this
		);
	}

	/**
	 * Render the field.
	 */
	render(): void {
		this.label();
		this.input();
	}
}

/**
 * A block editor field.
 *
 * @extends FieldComponent
 */
class FieldEditorComponent extends FieldComponent {
	/**
	 * Render the field.
	 */
	render(): void {
		super.render();
	}
}

/**
 * A checkbox field.
 *
 * @extends FieldComponent
 */
class FieldCheckboxComponent extends FieldComponent {
	/**
	 * Render the field.
	 */
	render(): void {
		super.render();
	}
}

/**
 * A large checkbox field.
 *
 * @extends FieldComponent
 */
class FieldCheckboxLargeComponent extends FieldComponent {
	/**
	 * Render the field.
	 */
	render(): void {
		super.render();
	}
}

/**
 * A checkbox field that can have a default global value.
 *
 * @extends FieldComponent
 */
class FieldCheckboxPageComponent extends FieldComponent {
	/**
	 * Render the field.
	 */
	render(): void {
		super.render();
	}
}

/**
 * A color field.
 *
 * @extends FieldComponent
 */
class FieldColorComponent extends FieldComponent {
	/**
	 * Render the field.
	 */
	render(): void {
		super.render();
	}
}

/**
 * A date field.
 *
 * @extends FieldComponent
 */
class FieldDateComponent extends FieldComponent {
	/**
	 * Render the field.
	 */
	render(): void {
		super.render();
	}
}

/**
 * A Markdown editor field.
 *
 * @extends FieldComponent
 */
class FieldMarkdownComponent extends FieldComponent {
	/**
	 * Render the field.
	 */
	render(): void {
		super.render();
	}
}

/**
 * An image field.
 *
 * @extends FieldComponent
 */
class FieldImageComponent extends FieldComponent {
	/**
	 * Render the field.
	 */
	render(): void {
		super.render();
	}
}

/**
 * A multiline text field.
 *
 * @extends FieldComponent
 */
class FieldTextareaComponent extends FieldComponent {
	/**
	 * Render the field.
	 */
	render(): void {
		super.render();
	}
}

/**
 * An URL field.
 *
 * @extends FieldComponent
 */
class FieldURLComponent extends FieldComponent {
	/**
	 * Render the field.
	 */
	render(): void {
		super.render();
	}
}

customElements.define('am-field-remove', FieldRemoveComponent);
customElements.define('am-field', FieldComponent);
customElements.define('am-field-editor', FieldEditorComponent);
customElements.define('am-field-checkbox', FieldCheckboxComponent);
customElements.define('am-field-checkbox-large', FieldCheckboxLargeComponent);
customElements.define('am-field-checkbox-page', FieldCheckboxPageComponent);
customElements.define('am-field-color', FieldColorComponent);
customElements.define('am-field-date', FieldDateComponent);
customElements.define('am-field-markdown', FieldMarkdownComponent);
customElements.define('am-field-image', FieldImageComponent);
customElements.define('am-field-textarea', FieldTextareaComponent);
customElements.define('am-field-url', FieldURLComponent);
