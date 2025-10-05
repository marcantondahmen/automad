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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { ModalComponent } from '@/admin/components/Modal/Modal';
import {
	EditorOutputData,
	FieldInitData,
	FieldSectionCollection,
	KeyValueMap,
	SelectComponentOption,
} from '@/admin/types';
import {
	App,
	Attr,
	Binding,
	Bindings,
	CSS,
	EventName,
	FieldTag,
	create,
	getComponentTargetContainer,
	getPageURL,
	html,
	listen,
	query,
	uniqueId,
} from '.';
import { PageDataFormComponent } from '@/admin/components/Forms/PageDataForm';
import { SwitcherSectionComponent } from '@/admin/components/Switcher/SwitcherSection';
import { FormComponent } from '@/admin/components/Forms/Form';
import { SharedDataFormComponent } from '@/admin/components/Forms/SharedDataForm';
import { AutocompleteUrlComponent } from '@/admin/components/AutocompleteUrl';
import { BaseFieldComponent } from '@/admin/components/Fields/BaseField';
import { SelectComponent } from '@/admin/components/Select';
import { EditorConfig } from 'automad-editorjs';
import { EditorJSComponent } from '@/admin/components/EditorJS';
import { Section } from '@/common';

/**
 * Create a new EditorJSComponent element.
 *
 * @param container
 * @param data
 * @param config
 * @param isSectionBlock
 */
export const createEditor = (
	container: HTMLElement,
	data: EditorOutputData,
	config: EditorConfig,
	isSectionBlock: boolean,
	readOnly: boolean = false
): EditorJSComponent => {
	const editorJS = create(
		EditorJSComponent.TAG_NAME,
		[],
		{},
		container
	) as EditorJSComponent;

	editorJS.init(data, config, isSectionBlock, readOnly);

	return editorJS;
};

/**
 * Create a form field and set its data.
 *
 * @param fieldType the field type name
 * @param parent the parent node where the field is created in
 * @param data the field data object
 * @param [cls] the array with optional class name
 * @param [attributes] additional attributes
 * @param [allowModal]
 * @returns the generated field
 */
export const createField = (
	fieldType: FieldTag,
	parent: HTMLElement,
	data: FieldInitData,
	cls: string[] = [],
	attributes: KeyValueMap = {},
	allowModal: boolean = false
): BaseFieldComponent => {
	const field = create(
		fieldType,
		cls,
		attributes,
		allowModal
			? create(
					'am-modal-field',
					[],
					{
						[Attr.page]: getPageURL(),
						[Attr.noClick]: '',
					},
					parent
				)
			: parent
	);

	field.data = data;

	return field;
};

/**
 * Create switcher sections for the different kind of variable fields.
 *
 * @param form - the main page data form that serves as wrapper
 * @returns the switcher section collection
 */
export const createFieldSections = (
	form: PageDataFormComponent | SharedDataFormComponent
): FieldSectionCollection => {
	const createSection = (section: string): SwitcherSectionComponent => {
		return create('am-switcher-section', [], { name: section }, form);
	};

	const sections: FieldSectionCollection = {
		settings: createSection(Section.settings),
		text: createSection(Section.text),
		customizations: createSection(Section.customizations),
	};

	return sections;
};

/**
 * Create a generic modal with an empty body element.
 *
 * @param title
 * @param api
 * @param event
 * @param submitText
 * @return an object that contains the modal and the form
 */
export const createFormModal = (
	api: string,
	event: string = '',
	title: string = '',
	submitText: string = App.text('submit')
): { modal: ModalComponent; form: FormComponent } => {
	const modal = create(
		ModalComponent.TAG_NAME,
		[],
		{ [Attr.destroy]: '' },
		getComponentTargetContainer(),
		html`
			<am-modal-dialog>
				${title ? `<am-modal-header>${title}</am-modal-header>` : ''}
				<am-modal-body>
					<am-form
						${Attr.api}="${api}"
						${event ? `${Attr.event}="${event}"` : ''}
					>
					</am-form>
				</am-modal-body>
				<am-modal-footer>
					<am-modal-close class=${CSS.button}>
						${App.text('cancel')}
					</am-modal-close>
					<am-submit
						class="${CSS.button} ${CSS.buttonPrimary}"
						${Attr.form}="${api}"
					>
						${submitText}
					</am-submit>
				</am-modal-footer>
			</am-modal-dialog>
		`
	) as ModalComponent;

	const form = query('am-form', modal) as FormComponent;

	return { modal, form };
};

/**
 * Create a generic modal with an empty body element.
 *
 * @param title
 * @param [buttonText]
 * @param [destroy]
 * @return an object that contains the modal, body and closing button
 */
export const createGenericModal = (
	title: string,
	buttonText: string = App.text('close'),
	destroy: boolean = true
): { modal: ModalComponent; body: HTMLElement; button: HTMLElement } => {
	const attr: KeyValueMap = {};

	if (destroy) {
		attr[Attr.destroy] = '';
	}

	const modal = create(
		ModalComponent.TAG_NAME,
		[],
		attr,
		getComponentTargetContainer(),
		html`
			<am-modal-dialog>
				<am-modal-header>${title}</am-modal-header>
				<am-modal-body></am-modal-body>
				<am-modal-footer>
					<am-modal-close class="${CSS.button} ${CSS.buttonPrimary}">
						${buttonText}
					</am-modal-close>
				</am-modal-footer>
			</am-modal-dialog>
		`
	) as ModalComponent;

	const body = query('am-modal-body', modal);
	const button = query('am-modal-footer am-modal-close', modal);

	return { modal, body, button };
};

/**
 * Create an image picker modal.
 *
 * @param onSelect
 * @param label
 * @param [url]
 */
export const createImagePickerModal = (
	onSelect: (value: string) => void,
	label: string,
	url: string = ''
): void => {
	const modal = create(
		ModalComponent.TAG_NAME,
		[],
		{ [Attr.destroy]: '' },
		getComponentTargetContainer()
	);

	const pickerBindingName = `imagePickerModal-temp-${uniqueId()}`;
	const idUrl = uniqueId();
	const idWidth = uniqueId();
	const idHeight = uniqueId();

	const pageUrl = getPageURL();

	new Binding(pickerBindingName, {
		onChange: (value) => {
			const inputWidth = query<HTMLInputElement>(`#${idWidth}`);
			const inputHeight = query<HTMLInputElement>(`#${idHeight}`);
			const width = inputWidth.value;
			const height = inputHeight.value;
			const querystring =
				width && height && !value.match(/\:\/\//)
					? `?${width}x${height}`
					: '';

			onSelect(`${value}${querystring}`);

			Bindings.delete(pickerBindingName);
			modal.close();
		},
	});

	const pageImagePicker = pageUrl
		? html`
				<am-image-picker
					${Attr.page}="${pageUrl}"
					${Attr.label}="${App.text('pageImages')}"
					${Attr.binding}="${pickerBindingName}"
				></am-image-picker>
			`
		: '';

	create(
		'am-modal-dialog',
		[CSS.modalDialogLarge],
		{},
		modal,
		html`
			<am-modal-header>${label}</am-modal-header>
			<am-modal-body>
				<p>${App.text('linkImage')}</p>
				<span class="${CSS.formGroup}">
					<input
						id="${idUrl}"
						type="text"
						class="${CSS.input} ${CSS.formGroupItem}"
						value="${url}"
						placeholder="${App.text('url')}"
					/>
					<button class="${CSS.button} ${CSS.formGroupItem}">
						${App.text('ok')}
					</button>
				</span>
				<hr />
				<div>
					<p>${App.text('useUploadedImage')}</p>
					<am-upload></am-upload>
					<div class="${CSS.flex} ${CSS.flexGap}">
						<div class="${CSS.flexItemGrow}">
							<div class="${CSS.field}">
								<label class="${CSS.fieldLabel}">
									${App.text('resizeWidthTitle')}
								</label>
								<input
									type="number"
									class="${CSS.input}"
									id="${idWidth}"
								/>
							</div>
						</div>
						<div class="${CSS.flexItemGrow}">
							<div class="${CSS.field} ${CSS.flexItemGrow}">
								<label class="${CSS.fieldLabel}">
									${App.text('resizeHeightTitle')}
								</label>
								<input
									type="number"
									class="${CSS.input}"
									id="${idHeight}"
								/>
							</div>
						</div>
					</div>
					${pageImagePicker}
					<am-image-picker
						${Attr.label}="${App.text('sharedImages')}"
						${Attr.binding}="${pickerBindingName}"
					></am-image-picker>
				</div>
			</am-modal-body>
		`
	);

	modal.listen(query('button', modal), 'click', () => {
		const inputUrl = query<HTMLInputElement>(`#${idUrl}`, modal);

		onSelect(inputUrl.value);

		Bindings.delete(pickerBindingName);
		modal.close();
	});

	setTimeout(() => {
		modal.open();
	}, 0);
};

/**
 * Create the autocomplete modal element.
 *
 * @param bindingName
 * @param label
 */
export const createLinkModal = (bindingName: string, label: string): void => {
	const modal = create(
		ModalComponent.TAG_NAME,
		[],
		{ [Attr.destroy]: '' },
		getComponentTargetContainer()
	);

	const dialog = create(
		'am-modal-dialog',
		[],
		{},
		modal,
		html`<am-modal-header>$${label}</am-modal-header>`
	);

	const body = create('am-modal-body', [], {}, dialog);
	const footer = create('am-modal-footer', [], {}, dialog);
	const binding = Bindings.get(bindingName);

	const autocomplete = create(
		'am-autocomplete-url',
		[],
		{},
		body
	) as AutocompleteUrlComponent;

	create('am-modal-close', [CSS.button], {}, footer, App.text('cancel'));

	const buttonOk = create(
		'button',
		[CSS.button, CSS.buttonPrimary],
		{},
		footer,
		App.text('ok')
	);

	modal.listen(modal, EventName.modalOpen, () => {
		autocomplete.input.value = '';
	});

	const select = () => {
		binding.value = autocomplete.input.value;
		modal.close();
	};

	autocomplete.listen(autocomplete, EventName.autocompleteSelect, select);
	modal.listen(buttonOk, 'click', select);

	setTimeout(() => {
		modal.open();
	}, 0);
};

/**
 * Create a blocking progress modal.
 *
 * @param text
 * @return The modal
 */
export const createProgressModal = (text: string): ModalComponent => {
	const modal = create(
		ModalComponent.TAG_NAME,
		[],
		{
			[Attr.noEsc]: '',
			[Attr.noClick]: '',
			[Attr.destroy]: '',
		},
		getComponentTargetContainer()
	);

	modal.innerHTML = html`
		<am-modal-dialog>
			<span class="${CSS.modalSpinner}">
				<span class="${CSS.modalSpinnerIcon}"></span>
				<span class="${CSS.modalSpinnerText}">${text}</span>
			</span>
		</am-modal-dialog>
	`;

	return modal;
};

/**
 * Create a select component based on an options object.
 *
 * @param options
 * @param selected
 * @param parent
 * @param name
 * @param id
 * @param prefix
 * @param cls
 * @param attributes
 * @returns the created component
 */
export const createSelect = (
	options: SelectComponentOption[],
	selected: string,
	parent: HTMLElement = null,
	name: string = null,
	id: string = null,
	prefix: string = '',
	cls: string[] = [],
	attributes: KeyValueMap = {}
): SelectComponent => {
	const select = create(SelectComponent.TAG_NAME, cls, {}, parent);

	if (name) {
		attributes['name'] = name;
	}

	if (id) {
		attributes['id'] = id;
	}

	select.init(options, selected, prefix, attributes);

	return select;
};

/**
 * Create a field wrapper for a given select component and
 * then return that select component
 *
 * @param label
 * @param select
 * @param container
 * @return an object containing the field element as well as the select component
 */
export const createSelectField = (
	label: string,
	select: SelectComponent,
	container: HTMLElement
): { select: SelectComponent; field: HTMLElement } => {
	const field = create(
		'div',
		[CSS.field],
		{},
		container,
		html`
			<div>
				<label class="${CSS.fieldLabel}">${label}</label>
			</div>
		`
	);

	field.appendChild(select);

	return { select, field };
};
