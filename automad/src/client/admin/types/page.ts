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
import { Binding } from '../core';

export type PageSectionName = 'settings' | 'text' | 'colors';

export type PageSectionCollection = {
	[name in PageSectionName]: SwitcherSectionComponent;
};

export type PageFieldGroups = {
	[name in PageSectionName]: KeyValueMap;
};

export interface PageMainSettingsData {
	section: SwitcherSectionComponent;
	url: string;
	fields: KeyValueMap;
	template: string;
	readme: string;
}

export interface PageMetaData {
	title: string;
	index: string;
	url: string;
	path: string;
	parentPath: string;
	private: boolean;
}

export interface Pages {
	[key: string]: PageMetaData;
}

export interface PageBindings {
	pageUrlBinding: Binding;
	pageUrlWithBaseBinding: Binding;
	pageLinkUIBinding: Binding;
	pageDataFetchTimeBinding: Binding;
	slugBinding: Binding;
}
