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
import { SwitcherSectionComponent } from '../components/SwitcherSection';

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
	prefix: string;
	slug: string;
	fields: KeyValueMap;
	template: string;
}

export interface PageMetaData {
	url: string;
	path: string;
	title: string;
	private: boolean;
}

export interface Pages {
	[key: string]: PageMetaData;
}
