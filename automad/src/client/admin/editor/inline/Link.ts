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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { AutocompleteUrlComponent } from '@/admin/components/AutocompleteUrl';
import { ToggleFieldComponent } from '@/admin/components/Fields/ToggleField';
import {
	App,
	create,
	createField,
	CSS,
	EventName,
	FieldTag,
	html,
	uniqueId,
} from '@/admin/core';
import { KeyValueMap } from '@/admin/types';
import { BaseInline } from './BaseInline';

/**
 * An inline link tool with autocomplete and target toggle.
 */
export class LinkInline extends BaseInline {
	/**
	 * The tool title.
	 *
	 * @static
	 */
	static get title(): string {
		return App.text('link');
	}

	/**
	 * The tool sanitize config.
	 *
	 * @static
	 */
	static get sanitize(): KeyValueMap {
		return {
			a: true,
		};
	}

	/**
	 * The tool tag.
	 */
	get tag(): string {
		return 'A';
	}

	/**
	 * The tool icon.
	 */
	get icon(): string {
		return '<i class="bi bi-link-45deg"></i>';
	}

	/**
	 * The menu wrapper.
	 */
	private wrapper: HTMLElement;

	/**
	 * The autocomplete component.
	 */
	private autocomplete: AutocompleteUrlComponent;

	/**
	 * The toggle component.
	 */
	private targetToggle: ToggleFieldComponent;

	/**
	 * Render the menu fields.
	 *
	 * @return the rendered fields
	 */
	renderActions(): HTMLElement {
		this.wrapper = create('div', [], {});

		const inputField = create(
			'div',
			[CSS.field],
			{},
			this.wrapper,
			html`<label class="${CSS.fieldLabel}">${LinkInline.title}</label>`
		);

		this.autocomplete = create<AutocompleteUrlComponent>(
			'am-autocomplete-url',
			[],
			{},
			inputField
		);

		this.targetToggle = createField(FieldTag.toggle, this.wrapper, {
			key: uniqueId(),
			value: false,
			name: 'target',
			label: App.text('openInNewTab'),
		}) as ToggleFieldComponent;

		return this.wrapper;
	}

	/**
	 * Init the fields.
	 */
	showActions(node: HTMLAnchorElement): void {
		this.wrapper.hidden = false;

		const input = this.autocomplete.input;
		const checkbox = this.targetToggle.input as HTMLInputElement;

		input.value = node.getAttribute('href') ?? '';
		checkbox.checked = node.getAttribute('target') == '_blank';

		this.listen(
			this.wrapper,
			`input ${EventName.autocompleteSelect}`,
			() => {
				node.href = input.value;

				if (checkbox.checked) {
					node.setAttribute('target', '_blank');
				} else {
					node.removeAttribute('target');
				}
			}
		);

		setTimeout(() => {
			if (!input.value) {
				input.focus();
			}
		}, 0);
	}

	/**
	 * Hide the fields.
	 */
	hideActions(): void {
		this.wrapper.hidden = true;
	}
}
