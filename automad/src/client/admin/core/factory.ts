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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { ModalComponent } from '@/components/Modal/Modal';
import {
	EditorOutputData,
	FieldInitData,
	FieldSectionCollection,
	FieldType,
	KeyValueMap,
	SelectComponentOption,
} from '@/types';
import {
	App,
	Attr,
	Binding,
	Bindings,
	CSS,
	EventName,
	getPageURL,
	html,
	listen,
	query,
	uniqueId,
} from '.';
import { PageDataFormComponent } from '@/components/Forms/PageDataForm';
import { SwitcherSectionComponent } from '@/components/Switcher/SwitcherSection';
import { Section } from '@/components/Switcher/Switcher';
import { SharedDataFormComponent } from '@/components/Forms/SharedDataForm';
import { AutocompleteComponent } from '@/components/Autocomplete';
import { BaseFieldComponent } from '@/components/Fields/BaseField';
import { SelectComponent } from '@/components/Select';
import { EditorConfig } from '@editorjs/editorjs';
import { EditorJSComponent } from '@/components/EditorJS';

/**
 * Create a new element including class names and attributes and optionally append it to a given parent node.
 *
 * @param tag - the tag name
 * @param classes - an array of class names that are added to the element
 * @param attributes - an object of attributes (key/value pairs) that are added to the element
 * @param [parent] - the optional node where the element will be appendend to
 * @returns the created element
 */
export const create = (
	tag: string,
	classes: string[] = [],
	attributes: object = {},
	parent: HTMLElement | null = null,
	innerHTML: string = null
): any => {
	const element = document.createElement(tag);

	classes.forEach((cls) => {
		element.classList.add(cls);
	});

	for (const [key, value] of Object.entries(attributes)) {
		element.setAttribute(key, value);
	}

	if (parent) {
		parent.appendChild(element);
	}

	if (innerHTML) {
		element.innerHTML = innerHTML;
	}

	return element;
};

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
	isSectionBlock: boolean
): EditorJSComponent => {
	const editorJS = create(
		EditorJSComponent.TAG_NAME,
		[],
		{},
		container
	) as EditorJSComponent;

	editorJS.init(data, config, isSectionBlock);

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
	fieldType: FieldType,
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
		colors: createSection(Section.colors),
	};

	return sections;
};

/**
 * Create an image picker modal.
 */
export const createImagePickerModal = (
	bindingName: string,
	label: string
): void => {
	const modal = create('am-modal', [], { [Attr.destroy]: '' }, App.root);

	const binding = Bindings.get(bindingName);
	const pickerBindingName = `picker_${bindingName}`;
	const idUrl = uniqueId();
	const idWidth = uniqueId();
	const idHeight = uniqueId();

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

			binding.value = `${value}${querystring}`;

			Bindings.delete(pickerBindingName);
			modal.close();
		},
	});

	create(
		'div',
		[CSS.modalDialog, CSS.modalDialogLarge],
		{},
		modal,
		html`
			<div class="${CSS.modalHeader}">
				<span>${label}</span>
				<am-modal-close class="${CSS.modalClose}"></am-modal-close>
			</div>
			<div class="${CSS.modalBody}">
				<span class="${CSS.formGroup}">
					<input
						id="${idUrl}"
						type="text"
						class="${CSS.input} ${CSS.formGroupItem}"
						placeholder="${App.text('url')}"
					/>
					<button class="${CSS.button} ${CSS.formGroupItem}">
						${App.text('ok')}
					</button>
				</span>
				<hr />
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
				<am-image-picker
					${Attr.page}="${getPageURL()}"
					${Attr.label}="${App.text('pageImages')}"
					${Attr.binding}="${pickerBindingName}"
				></am-image-picker>
				<am-image-picker
					${Attr.label}="${App.text('sharedImages')}"
					${Attr.binding}="${pickerBindingName}"
				></am-image-picker>
			</div>
		`
	);

	listen(query('button', modal), 'click', () => {
		const inputUrl = query<HTMLInputElement>(`#${idUrl}`, modal);
		binding.value = inputUrl.value;

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
	const modal = create('am-modal', [], { [Attr.destroy]: '' }, App.root);
	const dialog = create('div', [CSS.modalDialog], {}, modal);

	create(
		'div',
		[CSS.modalHeader],
		{},
		dialog,
		html`
			<span>${label}</span>
			<am-modal-close class="${CSS.modalClose}"></am-modal-close>
		`
	);

	const body = create('div', [CSS.modalBody], {}, dialog);
	const footer = create('div', [CSS.modalFooter], {}, dialog);
	const binding = Bindings.get(bindingName);

	const autocomplete = create(
		'am-autocomplete',
		[],
		{},
		body
	) as AutocompleteComponent;

	create(
		'am-modal-close',
		[CSS.button, CSS.buttonPrimary],
		{},
		footer
	).textContent = App.text('cancel');

	const buttonOk = create(
		'button',
		[CSS.button, CSS.buttonAccent],
		{},
		footer
	);

	buttonOk.textContent = App.text('ok');

	listen(modal, EventName.modalOpen, () => {
		autocomplete.input.value = '';
	});

	const select = () => {
		binding.value = autocomplete.input.value;
		modal.close();
	};

	listen(autocomplete, EventName.autocompleteSelect, select);
	listen(buttonOk, 'click', select);

	setTimeout(() => {
		modal.open();
	}, 0);
};

/**
 * Create a blocking progress modal.
 *
 * @param text
 */
export const createProgressModal = (text: string): ModalComponent => {
	const modal = create(
		'am-modal',
		[],
		{
			[Attr.noEsc]: '',
			[Attr.noClick]: '',
			[Attr.destroy]: '',
		},
		App.root
	);

	modal.innerHTML = html`
		<div class="${CSS.modalDialog}">
			<span class="${CSS.modalSpinner}">
				<span class="${CSS.modalSpinnerIcon}"></span>
				<span class="${CSS.modalSpinnerText}">${text}</span>
			</span>
		</div>
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
	name: string = '',
	id: string = '',
	prefix: string = '',
	cls: string[] = [],
	attributes: KeyValueMap = {}
): SelectComponent => {
	const select = create(SelectComponent.TAG_NAME, cls, {}, parent);
	let renderedOptions = '';
	let renderedAttributes = '';

	options.forEach((option) => {
		renderedOptions += html`
			<option
				value="${option.value}"
				${selected === option.value ? 'selected' : ''}
			>
				${typeof option.text !== 'undefined'
					? option.text
					: option.value}
			</option>
		`;
	});

	if (name) {
		attributes['name'] = name;
	}

	if (id) {
		attributes['id'] = id;
	}

	for (const [key, value] of Object.entries(attributes)) {
		renderedAttributes += html` ${key}="${value}"`;
	}

	select.innerHTML = html`
		${prefix}
		<span class="${CSS.flexItemGrow}"></span>
		<select ${renderedAttributes}>
			${renderedOptions}
		</select>
	`;

	return select;
};
