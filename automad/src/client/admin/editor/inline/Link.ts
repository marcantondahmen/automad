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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { AutocompleteComponent } from '@/components/Autocomplete';
import { ToggleComponent } from '@/components/Fields/Toggle';
import {
	App,
	create,
	createField,
	CSS,
	EventName,
	FieldTag,
	html,
	listen,
	uniqueId,
} from '@/core';
import { KeyValueMap, Listener } from '@/types';
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
	private autocomplete: AutocompleteComponent;

	/**
	 * The toggle component.
	 */
	private targetToggle: ToggleComponent;

	/**
	 * The chanhe listener.
	 */
	private listener: Listener = null;

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

		this.autocomplete = create(
			'am-autocomplete',
			[],
			{},
			inputField
		) as AutocompleteComponent;

		this.targetToggle = createField(FieldTag.toggle, this.wrapper, {
			key: uniqueId(),
			value: false,
			name: 'target',
			label: App.text('openInNewTab'),
		}) as ToggleComponent;

		return this.wrapper;
	}

	/**
	 * Init the fields.
	 */
	showActions(node: HTMLAnchorElement): void {
		const input = this.autocomplete.input;
		const checkbox = this.targetToggle.input as HTMLInputElement;

		input.value = node.getAttribute('href') ?? '';
		checkbox.checked = node.getAttribute('target') == '_blank';

		this.listener = listen(
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

		this.wrapper.hidden = false;

		input.focus();
	}

	/**
	 * Hide the fields.
	 */
	hideActions(): void {
		this.wrapper.hidden = true;
	}

	/**
	 * Cleanup and remove listener.
	 */
	clear(): void {
		this.listener?.remove();
	}
}
