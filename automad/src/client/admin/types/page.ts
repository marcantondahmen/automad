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

import { KeyValueMap } from '.';
import { SwitcherSectionComponent } from '@/admin/components/Switcher/SwitcherSection';
import { Binding } from '@/admin/core';

export interface PageRecentlyEditedCardData {
	title: string;
	url: string;
	lastModified: string;
	private: boolean;
	thumbnail: string;
	fileCount: number;
}

export interface PageMainSettingsData {
	section: SwitcherSectionComponent;
	url: string;
	fields: KeyValueMap;
	template: string;
	readme: string;
	shared: KeyValueMap;
}

export interface PageMetaData {
	title: string;
	index: string;
	url: string;
	path: string;
	parentUrl: string;
	private: boolean;
	lastModified: string;
	publicationState: 'published' | 'draft';
}

export interface Pages {
	[key: string]: PageMetaData;
}

export interface PageBindings {
	pageDataFetchTimeBinding: Binding;
	slugBinding: Binding;
}

export interface InPageBindings {
	inPageReturnUrlBinding: Binding;
	inPageTitleBinding: Binding;
	inPageContextUrlBinding: Binding;
	inPageFieldBinding: Binding;
}

export interface DeletedPageMetaData {
	title: string;
	path: string;
	lastModified: string;
}
