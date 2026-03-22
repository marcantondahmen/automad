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
 * Copyright (c) 2024-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	App,
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

/**
 * The component editor form component.
 *
 * @extends FormComponent
 */
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
	 * Set a lock for this controller.
	 */
	protected get setLock(): boolean {
		return true;
	}

	/**
	 * This is false until the first request was made and the store components are rendered
	 * in order to prevent submitting an empty form and therefore deleting the components store.
	 */
	private isInitialized = false;

	/**
	 * The sortable instance.
	 */
	private sortable: Sortable;

	/**
	 * The deduplication settings for the form.
	 */
	protected get deduplicationSettings(): DeduplicationSettings {
		return {
			getFormData: () => this.formData,
			enabled: true,
		};
	}

	/**
	 * The form data object.
	 */
	get formData(): KeyValueMap {
		const data: KeyValueMap = {
			instanceId: App.instanceId,
		};

		if (this.isInitialized) {
			const componentEditors = queryAll<ComponentEditorComponent>(
				ComponentEditorComponent.TAG_NAME,
				this
			);

			data.components = componentEditors
				.map((editor) => editor.data)
				.filter((editor) => !!editor);
		}

		return data;
	}

	/**
	 * Add a component.
	 *
	 * @async
	 * @param data
	 * @param [insertAfter]
	 */
	async add(
		data: ComponentEditorData,
		insertAfter: ComponentEditorComponent | null = null
	): Promise<ComponentEditorComponent> {
		const component = create(
			ComponentEditorComponent.TAG_NAME,
			[],
			{},
			this
		);

		if (insertAfter !== null) {
			insertAfter.insertAdjacentElement('afterend', component);
		}

		await component.setData(data);

		return component;
	}

	/**
	 * Initialize the form.
	 */
	protected init(): void {
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
	 * Process the response that is received after submitting the form.
	 *
	 * @param response
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		if (response.data?.components) {
			this.sortable?.destroy();
			this.innerHTML = '';

			await Promise.all(
				response.data.components.map(
					async (component: ComponentEditorData) => {
						await this.add(component);
					}
				)
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
				onChange: async (event) => {
					const editor = event.item as ComponentEditorComponent;

					await editor.init();
					fire('change', this);
				},
			});
		} else {
			fire(EventName.contentSaved);
		}

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
