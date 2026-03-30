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

import { BaseComponent } from '@/admin/components/Base';
import {
	App,
	Attr,
	Binding,
	Bindings,
	confirm,
	create,
	createField,
	createGenericModal,
	CSS,
	debounce,
	EventName,
	FieldTag,
	fire,
	html,
	query,
	uniqueId,
} from '@/admin/core';
import { ComponentEditorData } from '@/admin/types';
import { ComponentCollectionFormComponent } from './Forms/ComponentCollectionForm';
import { EditorFieldComponent } from './Fields/EditorField';

/**
 * A spinner component.
 *
 * @extends BaseComponent
 */
export class ComponentEditorComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME: string = 'am-component-editor';

	/**
	 * The component data object.
	 */
	private _data: ComponentEditorData;

	/**
	 * The editor field component instance.
	 */
	private editor: EditorFieldComponent;

	/**
	 * The component data getter.
	 */
	get data(): ComponentEditorData {
		return this._data;
	}

	/**
	 * An async function that sets the component data and waits for its initialization.
	 *
	 * @async
	 * @param data
	 */
	async setData(data: ComponentEditorData): Promise<void> {
		this._data = data;

		await this.init();
	}

	/**
	 * Initialize the component when data is set.
	 *
	 * @async
	 */
	async init(): Promise<void> {
		const hasName = this._data.name.length > 0;
		const nameBindingKey = `component-name-${this._data.id}-${Array.from(this.parentNode?.childNodes ?? [])?.indexOf(this) ?? 0}`;

		Bindings.delete(nameBindingKey);

		const nameBinding = new Binding(nameBindingKey, {
			onChange: (value) => {
				this._data.name = value;
				fire('change', this.editor);
			},
			initial: this._data.name,
		});

		const setupEditor = async () => {
			this.classList.add(CSS.componentEditor);

			this.innerHTML = html`
				<div class="${CSS.componentEditorHeader}">
					<div
						class="${CSS.componentEditorName}"
						${Attr.bind}="${nameBindingKey}"
						${Attr.bindTo}="title"
					>
						<i></i>
						<span ${Attr.bind}="${nameBindingKey}">
							${this._data.name}
						</span>
					</div>
					<div class="${CSS.componentEditorTools}"></div>
					<div class="${CSS.componentEditorHandle}">
						<i class="bi bi-grip-vertical"></i>
					</div>
				</div>
			`;

			const details = create(
				'details',
				[CSS.componentEditorDetails],
				{},
				this,
				html`
					<summary></summary>
					<div class="${CSS.componentEditorMain}"></div>
				`
			);

			Bindings.connectElements(this);

			const tools = query(`.${CSS.componentEditorTools}`, this);
			const editor = query(`.${CSS.componentEditorMain}`, this);
			const toggle = query(`.${CSS.componentEditorName}`, this);

			const rename = create(
				'span',
				[],
				{},
				tools,
				'<i class="bi bi-pencil"></i>'
			);

			const copy = create(
				'span',
				[],
				{},
				tools,
				'<i class="bi bi-copy"></i>'
			);

			const remove = create(
				'span',
				[],
				{},
				tools,
				'<i class="bi bi-trash3"></i>'
			);

			this.toggleEditor(details, toggle, this._data.collapsed);

			this.listen(toggle, 'click', () => {
				this.toggleEditor(details, toggle, !this._data.collapsed);
			});

			this.listen(copy, 'click', async () => {
				const collection =
					this.closest<ComponentCollectionFormComponent>(
						ComponentCollectionFormComponent.TAG_NAME
					);

				(
					await collection.add(
						{
							id: uniqueId(),
							name: `${this._data.name} (${App.text('componentCopy')})`,
							blocks: this.data.blocks,
							collapsed: false,
						},
						this
					)
				).fireOnReady();
			});

			this.listen(rename, 'click', () => {
				this.setName((name) => {
					nameBinding.value = name;
				});
			});

			this.listen(remove, 'click', async () => {
				if (
					await confirm(
						`${App.text('componentConfirmRemoval')} (${this._data.name})`
					)
				) {
					const form = this.closest<ComponentCollectionFormComponent>(
						ComponentCollectionFormComponent.TAG_NAME
					);

					this.remove();
					form.submit();
				}
			});

			this.editor = createField(FieldTag.editor, editor, {
				value: { blocks: this._data.blocks },
				name: '',
				key: '',
				label: '',
			}) as EditorFieldComponent;

			await this.editor.editorJS.editor.isReady;

			this.listen(this.editor, 'input', () => {
				this._data.blocks = this.editor.value.blocks;

				fire('change', this);
			});
		};

		if (hasName) {
			await setupEditor();
		} else {
			this.setName(async (name) => {
				nameBinding.value = name;
				await setupEditor();
				this.fireOnReady();
			});
		}
	}

	/**
	 * Fire input event when editor is ready.
	 */
	fireOnReady(): void {
		setTimeout(async () => {
			fire('change', this.editor);
		}, 0);
	}

	/**
	 * Toggle the editor.
	 *
	 * @param body
	 * @param toggle
	 * @param state
	 */
	private toggleEditor(
		details: HTMLDetailsElement,
		toggle: HTMLElement,
		collapsed: boolean
	): void {
		const icon = query('i', toggle);

		this._data.collapsed = collapsed;

		details.open = !this._data.collapsed;
		icon.className = `bi bi-chevron-${this._data.collapsed ? 'right' : 'down'}`;

		fire('change', this.editor);
	}

	/**
	 * Set a new name for the component.
	 *
	 * @param callback
	 */
	private setName(callback: (name: string) => void): void {
		const { modal, body, button } = createGenericModal(
			App.text('componentName'),
			App.text('ok')
		);

		const input = create(
			'input',
			[CSS.input],
			{ value: this._data.name ?? '' },
			body
		);

		button.setAttribute('disabled', '');

		modal.listen(
			input,
			'input paste cut',
			debounce(() => {
				if (input.value.length > 0) {
					button.removeAttribute('disabled');
				} else {
					button.setAttribute('disabled', '');
				}
			}, 100)
		);

		modal.listen(button, 'click', () => {
			const name = input.value;

			if (name.length) {
				callback(name);
				modal.close();
			}
		});

		modal.listen(modal, EventName.modalClose, () => {
			if (input.value.length == 0) {
				this.remove();
			}
		});

		setTimeout(() => modal.open(), 0);
	}
}

customElements.define(
	ComponentEditorComponent.TAG_NAME,
	ComponentEditorComponent
);
