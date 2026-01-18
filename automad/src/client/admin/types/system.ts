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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { transportOptions } from '@/admin/components/Forms/MailConfigForm';
import { Section } from '@/common';

type Enabled = boolean | 0 | 1;

export interface SystemSectionData {
	section: Section;
	icon: string;
	title: string;
	info: string;
	state: string;
	render: () => void;
	narrowIcon?: boolean;
}

interface CacheSettings {
	enabled: Enabled;
	lifetime: number;
	monitorDelay: number;
}

interface DebugSettings {
	enabled: Enabled;
	browser: Enabled;
}

interface FeedSettings {
	enabled: Enabled;
	fields: string;
}

export interface MailConfig {
	transport: (typeof transportOptions)[number];
	from: string;
	fromDefault: string;
	smtpServer: string;
	smtpUsername: string;
	smtpPort: number;
	smtpPasswordIsSet: boolean;
}

interface UserSettings {
	name: string;
	email: string;
}

export interface SystemSettings {
	cache: CacheSettings;
	debug: DebugSettings;
	feed: FeedSettings;
	i18n: Enabled;
	mail: MailConfig;
	translation: string;
	users: UserSettings[];
	tempDirectory: string;
}

export interface SystemUpdateResponse {
	state: string;
	current: string;
	latest: string;
	items: string[];
}

export interface User {
	name: string;
	email: string;
}
