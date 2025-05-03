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
	url: string;
	repository: string;
	image?: string;
	readme?: string;
	outdated?: boolean;
	installed?: boolean;
	latest?: string;
	version?: string;
}
