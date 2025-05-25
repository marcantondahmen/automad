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

export interface ComposerAuth {
	githubToken: string;
	githubTokenIsSet: boolean;
	gitlabUrl: string;
	gitlabToken: string;
	gitlabTokenIsSet: boolean;
}

export type RepositoryPlatform = 'github' | 'gitlab';

export interface Repository {
	platform: RepositoryPlatform;
	name: string;
	description: string;
	repositoryUrl: string;
	branch: string;
}

export interface RepositoryCreationData {
	name: string;
	repositoryUrl: string;
	branch: string;
	platform: RepositoryPlatform;
}

export interface Package {
	name: string;
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
