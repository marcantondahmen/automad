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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
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
	queryAll,
	uniqueId,
} from '@/admin/core';
import { ComponentEditorComponent } from '@/admin/components/ComponentEditor';
import { FormComponent } from './Form';
import {
	KeyValueMap,
	ComponentEditorData,
	DeduplicationSettings,
} from '@/admin/types';
import Sortable from 'sortablejs';

export const newComponentButtonClass = 'am-new-component-button';

export class ComponentCollectionFormComponent extends FormComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME: string = 'am-component-collection-form';

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
	 * The time of the latest fetch.
	 */
	private fetchTime: number;

	/**
	 * The sortable instance.
	 */
	private sortable: Sortable;

	/**
	 * The deduplication settings for the form.
	 */
	protected get deduplicationSettings(): DeduplicationSettings {
		return {
			getFormData: () => {
				const data = this.formData;

				data.fetchTime = null;

				return data;
			},
			enabled: true,
		};
	}

	/**
	 * The form data object.
	 */
	get formData(): KeyValueMap {
		if (!this.isInitialized) {
			return {};
		}

		const componentEditors = queryAll<ComponentEditorComponent>(
			ComponentEditorComponent.TAG_NAME,
			this
		);

		return {
			components: componentEditors.map((editor) => editor.data),
			fetchTime: this.fetchTime,
		};
	}

	/**
	 * The function that is called when the form is connected.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGap);

		queryAll(`.${newComponentButtonClass}`).forEach((button) => {
			this.listen(button, 'click', () => {
				this.add({
					id: uniqueId(),
					name: '',
					blocks: [],
					collapsed: false,
				});
			});
		});
	}

	/**
	 * Add a component.
	 *
	 * @param data
	 * @param [insertAfter]
	 */
	add(
		data: ComponentEditorData,
		insertAfter: ComponentEditorComponent | null = null
	): ComponentEditorComponent {
		const component = create(
			ComponentEditorComponent.TAG_NAME,
			[],
			{},
			this
		);

		if (insertAfter !== null) {
			insertAfter.insertAdjacentElement('afterend', component);
		}

		component.data = data;

		return component;
	}

	/**
	 * Initialize the form.
	 */
	protected init(): void {
		super.init();

		this.listen(window, EventName.contentPublished, () => {
			this.fetchTime = Math.ceil(new Date().getTime() / 1000);
		});
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
			this.sortable?.destroy();
			this.innerHTML = '';

			response.data.components.forEach(
				(component: ComponentEditorData) => {
					this.add(component);
				}
			);

			this.sortable = new Sortable(this, {
				handle: `.${CSS.componentEditorHandle}, .${CSS.componentEditorName}`,
				animation: 200,
				draggable: ComponentEditorComponent.TAG_NAME,
				ghostClass: CSS.componentEditorGhost,
				chosenClass: CSS.componentEditorChosen,
				dragClass: CSS.componentEditorDrag,
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
				onChange: (event) => {
					(event.item as ComponentEditorComponent).reconnect();

					this.submit();
				},
			});
		} else {
			fire(EventName.contentSaved);
			fire(EventName.appStateRequireUpdate);
		}

		this.fetchTime = response.time;
		this.isInitialized = true;
	}

	/**
	 * Remove listeners on disconnect.
	 */
	disconnectedCallback(): void {
		this.sortable?.destroy();
		super.disconnectedCallback();
	}
}

customElements.define(
	ComponentCollectionFormComponent.TAG_NAME,
	ComponentCollectionFormComponent
);
