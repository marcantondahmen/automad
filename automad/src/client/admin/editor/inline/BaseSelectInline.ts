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

import { SelectComponent } from '@/admin/components/Select';
import { create, createSelect, CSS, html, listen } from '@/admin/core';
import { Listener, SelectComponentOption } from '@/admin/types';
import { BaseInline } from './BaseInline';

export abstract class BaseSelectInline extends BaseInline {
	/**
	 * The tool title.
	 *
	 * @static
	 */
	static get title() {
		return '';
	}

	/**
	 * The tool options.
	 */
	protected readonly options: string[] = [];

	/**
	 * The tool default.
	 */
	protected default = '';

	/**
	 * The tool property.
	 */
	protected property = '';

	/**
	 * The menu wrapper.
	 */
	private wrapper: HTMLElement;

	/**
	 * The select element.
	 */
	private select: SelectComponent;

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

		const field = create(
			'div',
			[CSS.field],
			{},
			this.wrapper,
			html`
				<label class="${CSS.fieldLabel}">
					${(this.constructor as typeof BaseSelectInline).title}
				</label>
			`
		);

		this.select = createSelect(
			this.options.reduce(
				(
					res: SelectComponentOption[],
					value: string
				): SelectComponentOption[] => {
					return [...res, { value }];
				},
				[]
			),
			this.default,
			field
		);

		return this.wrapper;
	}

	/**
	 * Init the fields.
	 */
	showActions(node: HTMLAnchorElement): void {
		this.wrapper.hidden = false;

		const prop = node.style.getPropertyValue(this.property);

		if (prop) {
			this.select.value = prop;
		}

		this.listener = listen(this.wrapper, 'change', () => {
			node.style.setProperty(this.property, this.select.value);
		});
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
