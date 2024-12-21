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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	create,
	CSS,
	EventName,
	FieldTag,
	fire,
	listen,
	query,
	queryAll,
	uniqueId,
} from '@/admin/core';
import { SharedComponentEditorComponent } from '@/admin/components/SharedComponentEditor';
import { FormComponent } from './Form';
import { KeyValueMap, SharedComponentEditorData } from '@/admin/types';
import Sortable from 'sortablejs';

export const newComponentButtonId = 'am-new-component-button';

export class SharedComponentsFormComponent extends FormComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME: string = 'am-shared-component-form';

	/**
	 * The form inits itself when created.
	 */
	protected get initSelf(): boolean {
		return true;
	}

	/**
	 * Submit form data on changes.
	 */
	protected get auto(): boolean {
		return true;
	}

	/**
	 * This is false until the first request was made and the store components are rendered
	 * in order to prevent submitting an empty form and therefore deleting the components store.
	 */
	private isInitialized = false;

	/**
	 * The form data object.
	 */
	get formData(): KeyValueMap {
		if (!this.isInitialized) {
			return {};
		}

		const componentEditors = queryAll<SharedComponentEditorComponent>(
			SharedComponentEditorComponent.TAG_NAME,
			this
		);

		return { components: componentEditors.map((editor) => editor.data) };
	}

	/**
	 * The function that is called when the form is connected.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGap);

		this.addListener(
			listen(query(`#${newComponentButtonId}`), 'click', () => {
				this.add({
					id: uniqueId(),
					name: '',
					blocks: [],
					collapsed: false,
				});
			})
		);
	}

	/**
	 * Add a component.
	 *
	 * @param data
	 * @param [insertAfter]
	 */
	add(
		data: SharedComponentEditorData,
		insertAfter: SharedComponentEditorComponent | null = null
	): void {
		const component = create(
			SharedComponentEditorComponent.TAG_NAME,
			[],
			{},
			this
		);

		if (insertAfter !== null) {
			insertAfter.insertAdjacentElement('afterend', component);
		}

		component.data = data;
	}

	/**
	 * Process the response that is received after submitting the form.
	 *
	 * @param response
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		await super.processResponse(response);

		if (response.data?.components) {
			this.innerHTML = '';

			response.data?.components.forEach(
				(component: SharedComponentEditorData) => {
					this.add(component);
				}
			);

			new Sortable(this, {
				handle: `.${CSS.cardHeaderDrag}`,
				animation: 200,
				draggable: SharedComponentEditorComponent.TAG_NAME,
				ghostClass: CSS.cardGhost,
				chosenClass: CSS.cardChosen,
				dragClass: CSS.cardDrag,
				direction: 'vertical',
				onStart: () => {
					queryAll(`${FieldTag.editor}`, this).forEach((editor) => {
						editor.style.pointerEvents = 'none';
					});
				},
				onEnd: () => {
					queryAll(`${FieldTag.editor}`, this).forEach((editor) => {
						editor.style.removeProperty('pointer-events');
					});
				},
				onChange: () => {
					this.submit();
				},
			});

			this.isInitialized = true;
		}

		fire(EventName.contentSaved);
	}
}

customElements.define(
	SharedComponentsFormComponent.TAG_NAME,
	SharedComponentsFormComponent
);
