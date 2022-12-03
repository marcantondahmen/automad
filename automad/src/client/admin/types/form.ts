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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { KeyValueMap } from '.';
import { SwitcherSectionComponent } from '../components/Switcher/SwitcherSection';

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
}

export interface FieldGroupData {
	section: SwitcherSectionComponent;
	fields: KeyValueMap;
	tooltips: KeyValueMap;
	shared: KeyValueMap;
}

export interface FieldInitData {
	key: string;
	value: string | number | KeyValueMap | boolean;
	name: string;
	tooltip?: string;
	label?: string;
	placeholder?: string | number | KeyValueMap | boolean;
}

export interface FieldRenderData extends Omit<FieldInitData, 'key'> {
	id: string;
}
