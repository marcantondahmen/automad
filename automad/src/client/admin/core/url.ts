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
