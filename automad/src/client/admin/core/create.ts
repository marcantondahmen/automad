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

import { InputComponent } from '../components/Fields/Input';
import { ModalComponent } from '../components/Modal/Modal';
import { FieldInitData, FieldSectionCollection, KeyValueMap } from '../types';
import { App, Attr, CSS, html } from '.';
import { PageDataFormComponent } from '../components/Forms/PageDataForm';
import { SwitcherSectionComponent } from '../components/Switcher/SwitcherSection';
import { Section } from '../components/Switcher/Switcher';
import { SharedDataFormComponent } from '../components/Forms/SharedDataForm';

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
	parent: HTMLElement | null = null
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

	return element;
};

/**
 * Create a form field and set its data.
 *
 * @param fieldType the field type name
 * @param parent the parent node where the field is created in
 * @param data the field data object
 * @param [cls] the array with optional class name
 * @param [attributes] additional attributes
 * @returns the generated field
 */
export const createField = (
	fieldType: string,
	parent: HTMLElement,
	data: FieldInitData,
	cls: string[] = [],
	attributes: KeyValueMap = {}
): InputComponent => {
	const field = create(fieldType, cls, attributes, parent);

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
