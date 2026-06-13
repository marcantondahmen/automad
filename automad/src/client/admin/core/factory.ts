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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { EditorConfig } from '@/vendor/editorjs';
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
	Bindings,
	CSS,
	EventName,
	FieldTag,
	collectFieldData,
	create,
	getComponentTargetContainer,
	getPageURL,
	html,
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
import { EditorJSComponent } from '@/admin/components/EditorJS';
import { debounce, queryAll, Section } from '@/common';

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
		allowModal && !data.isUnused
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
 * @param [onClick]
 * @return an object that contains the modal, body and closing button
 */
export const createGenericModal = (
	title: string,
	buttonText: string = App.text('close'),
	destroy: boolean = true,
	onClick: (modal: ModalComponent) => void = (modal) => {
		modal.close();
	}
): { modal: ModalComponent; body: HTMLElement; button: HTMLButtonElement } => {
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
					<button class="${CSS.button} ${CSS.buttonPrimary}">
						${buttonText}
					</button>
				</am-modal-footer>
			</am-modal-dialog>
		`
	) as ModalComponent;

	const body = query('am-modal-body', modal);
	const button = query<HTMLButtonElement>('am-modal-footer button', modal);

	modal.listen(button, 'click', () => {
		onClick(modal);
	});

	return { modal, body, button };
};

/**
 * Create an image picker modal.
 *
 * @param onSelect
 * @param label
 * @param [url]
 * @param [isMultiSelect]
 */
export const createImagePickerModal = (
	onSelect: (files: string[]) => void,
	label: string,
	url: string = '',
	isMultiSelect: boolean = false
): void => {
	const modal = create(
		ModalComponent.TAG_NAME,
		[],
		{ [Attr.destroy]: '' },
		getComponentTargetContainer()
	);

	const idUrl = uniqueId();
	const idUrlButton = uniqueId();
	const idWidth = uniqueId();
	const idHeight = uniqueId();
	const idSelectButton = uniqueId();
	const pageUrl = getPageURL();
	const multipleAttr = isMultiSelect ? Attr.multiple : '';

	const resizeForm = isMultiSelect
		? ''
		: html`
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
			`;

	const pageImagePicker = pageUrl
		? html`
				<am-image-picker
					${Attr.page}="${pageUrl}"
					${Attr.label}="${App.text('pageImages')}"
					${multipleAttr}
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
				<span
					class="${CSS.flex} ${CSS.flexGap} ${CSS.flexColumn} ${CSS.flexAlignEnd}"
				>
					<input
						id="${idUrl}"
						type="text"
						class="${CSS.input}"
						value="${url}"
						placeholder="${App.text('url')}"
					/>
					<button
						id="${idUrlButton}"
						class="${CSS.button} ${CSS.buttonPrimary}"
					>
						${App.text('linkExternalImage')}
					</button>
				</span>
				<hr />
				<div>
					<p>${App.text('useUploadedImage')}</p>
					<am-upload></am-upload>
					${resizeForm} ${pageImagePicker}
					<am-image-picker
						${Attr.label}="${App.text('sharedImages')}"
						${multipleAttr}
					></am-image-picker>
				</div>
			</am-modal-body>
			<am-modal-footer class="${CSS.modalFooterSticky}">
				<am-modal-close class="${CSS.button}">
					${App.text('close')}
				</am-modal-close>
				<button
					id="${idSelectButton}"
					class="${CSS.button} ${CSS.buttonPrimary}"
					disabled
				>
					${isMultiSelect
						? App.text('addSelectedImages')
						: App.text('selectImage')}
				</button>
			</am-modal-footer>
		`
	);

	const urlInput = query<HTMLInputElement>(`#${idUrl}`, modal);
	const urlButton = query<HTMLButtonElement>(`#${idUrlButton}`, modal);
	const selectButton = query<HTMLButtonElement>(`#${idSelectButton}`, modal);

	const getResizeQuery = (file: string) => {
		const inputWidth = query<HTMLInputElement>(`#${idWidth}`);
		const inputHeight = query<HTMLInputElement>(`#${idHeight}`);
		const width = inputWidth.value;
		const height = inputHeight.value;

		return `${file}${
			width && height && !file.match(/\:\/\//)
				? `?${width}x${height}`
				: ''
		}`;
	};

	const getSelection = () => {
		return queryAll('am-image-picker', modal).reduce(
			(acc, picker) => [
				...acc,
				...Object.values(collectFieldData(picker)),
			],
			[]
		);
	};

	modal.listen(urlButton, 'click', () => {
		onSelect([urlInput.value]);

		modal.close();
	});

	modal.listen(
		modal,
		'change',
		debounce(() => {
			if (getSelection().length) {
				selectButton.removeAttribute('disabled');

				return;
			}

			selectButton.setAttribute('disabled', '');
		}),
		'input[type]'
	);

	modal.listen(selectButton, 'click', () => {
		const files = getSelection();

		if (!files.length) {
			return;
		}

		onSelect(isMultiSelect ? files : [getResizeQuery(files[0])]);

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
 * @param envKey
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
	attributes: KeyValueMap = {},
	envKey: string = ''
): SelectComponent => {
	const select = create(SelectComponent.TAG_NAME, cls, {}, parent);

	if (name) {
		attributes['name'] = name;
	}

	if (id) {
		attributes['id'] = id;
	}

	select.init(options, selected, prefix, attributes, envKey);

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
