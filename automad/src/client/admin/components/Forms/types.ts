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
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import type { KeyValueMap, ThemeOptions } from '@/admin/types';
import type { SwitcherSectionComponent } from '@/admin/components/Switcher/SwitcherSection';

export interface DeduplicationSettings {
	getFormData: (element: HTMLElement) => KeyValueMap;
	enabled: boolean;
}

export type FieldSectionName = 'settings' | 'text' | 'customizations';

export type FieldSectionCollection = {
	[name in FieldSectionName]: SwitcherSectionComponent;
};

export type FieldGroups = {
	[name in FieldSectionName]: KeyValueMap;
};

export interface FieldGroupData {
	section: SwitcherSectionComponent;
	fields: KeyValueMap;
	unused: KeyValueMap;
	tooltips: KeyValueMap;
	themeOptions: ThemeOptions;
	labels: KeyValueMap;
	renderEmptyAlert: boolean;
	shared?: KeyValueMap;
}
