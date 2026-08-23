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

export interface Package {
	name: string;
	title: string;
	description: string;
	thumbnail: string;
	repository: string;
	issues: string;
	documentation: string;
	outdated?: boolean;
	installed?: boolean;
	isDependency?: boolean;
	latest?: string;
	version?: string;
	authors: { name: string; homepage: string }[];
}

export type RepositoryPlatform = 'github' | 'gitlab';

export interface Repository {
	platform: RepositoryPlatform;
	name: string;
	description: string;
	repositoryUrl: string;
	branch: string;
	commit: {
		hash: string;
		message: string;
		timestamp: string;
		url: string;
	} | null;
}

export interface RepositoryCreationData {
	name: string;
	repositoryUrl: string;
	branch: string;
	platform: RepositoryPlatform;
}
