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

import Sortable, { SortableEvent, SortableOptions } from 'sortablejs';
import {
	App,
	classes,
	create,
	eventNames,
	fire,
	html,
	listen,
	query,
} from '../../core';
import { FormComponent } from '../Forms/Form';
import { BaseFieldComponent } from './BaseField';

const options: SortableOptions = {
	group: 'feedFields',
	direction: 'vertical',
	animation: 200,
	dataIdAttr: 'data-field',
	draggable: `.${classes.feedFieldSelectItem}`,
	handle: `.${classes.feedFieldSelectItem}`,
	ghostClass: classes.feedFieldSelectItemGhost,
	chosenClass: classes.feedFieldSelectItemChosen,
	dragClass: classes.feedFieldSelectItemDrag,
};

/**
 * A RSS field selection field.
 *
 * @extends BaseFieldComponent
 */
class FeedFieldSelectComponent extends BaseFieldComponent {
	/**
	 * If true the field data is sanitized.
	 */
	protected sanitize = false;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		super.connectedCallback();

		setTimeout(() => {
			this.init();

			listen(
				this.input,
				eventNames.changeByBinding,
				this.init.bind(this)
			);
		}, 0);
	}

	/**
	 * Render no label.
	 */
	createLabel(): void {}

	/**
	 * Render the field.
	 */
	createInput(): void {
		const { name, value } = this._data;

		create('input', [], { type: 'hidden', name, value }, this);
	}

	/**
	 * Init sortable functionality.
	 */
	private init(): void {
		let section = query('section', this);

		if (section) {
			section.remove();
		}

		section = create('section', [], {}, this);

		const fieldsUsed: string[] = JSON.parse(
			(this.input.value as string) || '[]'
		);
		const unusedFields = App.contentFields.filter((item) => {
			return !(fieldsUsed as string[]).includes(item);
		});

		const usedContainer = this.createSortable(
			fieldsUsed,
			App.text('systemRssFeedFieldsInfoUsed'),
			[],
			section
		);

		create('div', [classes.feedFieldSelectArrows], {}, section).innerHTML =
			'<i class="bi bi-arrow-down-up"></i>';

		const unusedContainer = this.createSortable(
			unusedFields,
			App.text('systemRssFeedFieldsInfoUnused'),
			[classes.feedFieldSelectMuted],
			section
		);

		const usedSortable = new Sortable(
			usedContainer,
			Object.assign({}, options, {
				onSort: (event: SortableEvent) => {
					this.input.value = JSON.stringify(usedSortable.toArray());
					fire('input', this.input);
				},
			})
		);

		new Sortable(unusedContainer, options);
	}

	/**
	 * Render a selection area.
	 *
	 * @param fields
	 * @param text
	 * @param cls
	 * @param container
	 * @return the sortable container
	 */
	private createSortable(
		fields: string[],
		text: string,
		cls: string[],
		container: HTMLElement
	): HTMLElement {
		const sortable = create(
			'div',
			[].concat(cls, [classes.feedFieldSelect]),
			{ 'data-text': text },
			container
		);

		fields.forEach((field) => {
			const element = create(
				'div',
				[classes.feedFieldSelectItem],
				{ [options.dataIdAttr]: field },
				sortable
			);

			element.innerHTML = html`
				<span>${field}</span>
				<i class="bi bi-grip-vertical"></i>
			`;
		});

		return sortable;
	}
}

customElements.define('am-feed-field-select', FeedFieldSelectComponent);
