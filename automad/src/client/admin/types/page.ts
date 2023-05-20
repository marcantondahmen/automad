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

import { KeyValueMap } from '.';
import { SwitcherSectionComponent } from '@/components/Switcher/SwitcherSection';
import { Binding } from '@/core';

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
	parentUrl: string;
	private: boolean;
	lastModified: string;
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

export interface DeletedPageMetaData {
	title: string;
	path: string;
	lastModified: string;
}
