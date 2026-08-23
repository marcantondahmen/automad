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
 * Copyright (c) 2022-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import type { MailConfig } from '@/admin/components/Forms/MailConfigForm';
import type { AiProvider } from './AiProviderSetup';

type Enabled = boolean | 0 | 1;

interface AiSettings {
	enabled: boolean;
	instructions: string;
	activeProviderId: string;
	providers: AiProvider[];
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

export interface User {
	name: string;
	email: string;
	totpIsConfigured: boolean;
}

export interface SystemSettings {
	ai: AiSettings;
	cache: CacheSettings;
	debug: DebugSettings;
	feed: FeedSettings;
	i18n: Enabled;
	mail: MailConfig;
	translation: string;
	users: User[];
	tempDirectory: string;
}

export interface SystemUpdateResponse {
	state: string;
	current: string;
	latest: string;
	items: string[];
}
