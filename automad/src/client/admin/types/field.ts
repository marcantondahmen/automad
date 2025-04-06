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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { KeyValueMap, ThemeOptions } from '.';
import { SwitcherSectionComponent } from '@/admin/components/Switcher/SwitcherSection';

export type FieldSectionName = 'settings' | 'text' | 'customizations';

export type FieldSectionCollection = {
	[name in FieldSectionName]: SwitcherSectionComponent;
};

export type FieldGroups = {
	[name in FieldSectionName]: KeyValueMap;
};

export type InputElement = HTMLInputElement | HTMLTextAreaElement;

export interface TemplateButtonStatus {
	buttonLabel: string;
	buttonIcon: string;
	selectedTemplate: string;
}

export interface TemplateFieldData {
	fields: KeyValueMap;
	template: string;
	themeKey: string;
	readme: string;
}

export interface FieldGroupData {
	section: SwitcherSectionComponent;
	fields: KeyValueMap;
	tooltips: KeyValueMap;
	themeOptions: ThemeOptions;
	labels: KeyValueMap;
	renderEmptyAlert: boolean;
	shared?: KeyValueMap;
}

export interface FieldInitData {
	key: string;
	value: string | number | KeyValueMap | boolean;
	name: string;
	id?: string;
	tooltip?: string;
	options?: KeyValueMap;
	label?: string;
	placeholder?: string | number | KeyValueMap | boolean;
	isInPage?: boolean;
}

export interface FieldRenderData extends Omit<FieldInitData, 'key'> {
	id: string;
}
