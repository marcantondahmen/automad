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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App } from '.';

/**
 * Delete a parameter from the query string.
 *
 * @param param
 */
export const deleteSearchParam = (param: string): void => {
	const url = new URL(window.location.href);

	url.searchParams.delete(param);
	window.history.replaceState(null, null, url);
};

/**
 * Get the current page URL from the query string.
 *
 * @returns a page URL
 */
export const getPageURL = (): string => {
	return getSearchParam('url');
};

/**
 * Get an item from the URL search params.
 *
 * @param param
 * @returns the search param value
 */
export const getSearchParam = (param: string): string => {
	const searchParams = new URLSearchParams(window.location.search);

	return searchParams.get(param) || '';
};

/**
 * Resolve a file URL.
 *
 * @param fileUrl
 * @returns the resolved URL
 */
export const resolveFileUrl = (fileUrl: string): string => {
	if (!fileUrl) {
		return '';
	}

	if (fileUrl.match(/^\//)) {
		return `${App.baseURL}${fileUrl}`;
	}

	if (fileUrl.match(/:\/\//g)) {
		return fileUrl;
	}

	const pageUrl = getPageURL();

	if (pageUrl) {
		const page = App.pages[pageUrl];

		if (page) {
			return `${App.baseURL}/pages${page.path}${fileUrl}`;
		}
	}

	return '';
};

/**
 * Resolve a page URL.
 *
 * @param pageUrl
 * @returns the resolved URL
 */
export const resolvePageUrl = (pageUrl: string): string => {
	if (!pageUrl) {
		return;
	}

	if (pageUrl.match(/^\//)) {
		return `${App.baseURL}${pageUrl}`;
	}

	return pageUrl;
};

/**
 * Set an item from the URL search params.
 *
 * @param param
 * @param key
 */
export const setSearchParam = (param: string, value: string): void => {
	const url = new URL(window.location.href);

	url.searchParams.set(param, value);
	window.history.replaceState(null, null, url);
};
