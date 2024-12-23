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
	FieldTag,
	fire,
	html,
	listen,
	query,
	uniqueId,
} from '@/admin/core';
import { ComponentEditorData } from '@/admin/types';
import { EditorFieldComponent } from './Fields/EditorField';
import { ComponentsFormComponent } from './Forms/ComponentsForm';

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
		this._data.blocks = this.editor.value.blocks;

		return this._data;
	}

	/**
	 * The component data setter.
	 *
	 * @param data
	 */
	set data(data: ComponentEditorData) {
		this._data = data;

		setTimeout(this.init.bind(this), 0);
	}

	/**
	 * Initialize the component when data is set.
	 */
	private init(): void {
		const hasName = this._data.name.length > 0;
		const nameBindingKey = `shared-component-name-${this._data.id}-${Array.from(this.parentNode?.childNodes ?? [])?.indexOf(this) ?? 0}`;
		const nameBinding = new Binding(nameBindingKey, {
			onChange: (value) => {
				this._data.name = value;
				fire('change', this.editor);
			},
			initial: this._data.name,
		});

		const setupEditor = async () => {
			this.classList.add(CSS.card);

			this.innerHTML = html`
				<div class="${CSS.cardHeader}">
					<div
						class="${CSS.cardTitle} ${CSS.cursorPointer} ${CSS.flex} ${CSS.flexGap} ${CSS.flexItemGrow}"
					>
						<i></i>
						<span ${Attr.bind}="${nameBindingKey}">
							${this._data.name}
						</span>
					</div>
					<div class="${CSS.cardHeaderIcons}"></div>
					<div class="${CSS.cardHeaderDrag}">
						<i class="bi bi-grip-vertical"></i>
					</div>
				</div>
				<div class="${CSS.cardEditor}"></div>
			`;

			Bindings.connectElements(this);

			const header = query(`.${CSS.cardHeaderIcons}`, this);
			const body = query(`.${CSS.cardEditor}`, this);
			const toggle = query(`.${CSS.cardTitle}`, this);

			const rename = create(
				'span',
				[],
				{},
				header,
				'<i class="bi bi-pencil"></i>'
			);

			const copy = create(
				'span',
				[],
				{},
				header,
				'<i class="bi bi-copy"></i>'
			);

			const remove = create(
				'span',
				[],
				{},
				header,
				'<i class="bi bi-trash3"></i>'
			);

			this.toggleEditor(body, toggle, this._data.collapsed);

			listen(toggle, 'click', () => {
				this.toggleEditor(body, toggle, !this._data.collapsed);
			});

			listen(copy, 'click', () => {
				const collection = this.closest<ComponentsFormComponent>(
					ComponentsFormComponent.TAG_NAME
				);

				collection.add(
					{
						id: uniqueId(),
						name: `${this._data.name} (${App.text('componentCopy')})`,
						blocks: this.data.blocks,
						collapsed: false,
					},
					this
				);
			});

			listen(rename, 'click', () => {
				this.setName((name) => {
					nameBinding.value = name;
				});
			});

			listen(remove, 'click', async () => {
				if (
					await confirm(
						`${App.text('componentConfirmRemoval')} (${this._data.name})`
					)
				) {
					const form = this.closest<ComponentsFormComponent>(
						ComponentsFormComponent.TAG_NAME
					);

					this.remove();
					form.submit();
				}
			});

			this.editor = createField(FieldTag.editor, body, {
				value: { blocks: this._data.blocks },
				name: '',
				key: '',
				label: '',
			}) as EditorFieldComponent;

			await this.editor.editorJS.editor.isReady;

			fire('input', this.editor);
		};

		if (hasName) {
			setupEditor();
		} else {
			this.setName((name) => {
				nameBinding.value = name;
				setupEditor();
			});
		}
	}

	/**
	 * Toggle the editor.
	 *
	 * @param body
	 * @param toggle
	 * @param state
	 */
	private toggleEditor(
		body: HTMLElement,
		toggle: HTMLElement,
		collapsed: boolean
	): void {
		const icon = query('i', toggle);

		this._data.collapsed = collapsed;

		body.classList.toggle(CSS.displayNone, this._data.collapsed);
		icon.className = `bi bi-${this._data.collapsed ? 'plus' : 'dash'}-lg`;

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
		modal.open();

		listen(
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

		listen(button, 'click', () => {
			const name = input.value;

			if (name.length) {
				callback(name);
				modal.close();
			}
		});
	}
}

customElements.define(
	ComponentEditorComponent.TAG_NAME,
	ComponentEditorComponent
);
