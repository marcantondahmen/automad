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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

// @ts-ignore
import Draggabilly from 'draggabilly';

const SCROLL_KEY = 'am-inpage-scroll-y';
const DOCK_POSITION_KEY = 'am-inpage-dock-position';

export const saveScrollPosition = () => {
	sessionStorage.setItem(SCROLL_KEY, `${window.scrollY}`);
};

export const restoreScrollPosition = () => {
	const _scrollY = sessionStorage.getItem(SCROLL_KEY);

	if (_scrollY) {
		window.scrollTo(window.scrollX, parseInt(_scrollY));
		sessionStorage.removeItem(SCROLL_KEY);
	}
};

export const saveDockPosition = (draggable: Draggabilly) => {
	sessionStorage.setItem(
		DOCK_POSITION_KEY,
		JSON.stringify(draggable.position)
	);
};

export const restoreDockPosition = (draggable: Draggabilly) => {
	const json = sessionStorage.getItem(DOCK_POSITION_KEY);

	if (!json) {
		return;
	}

	const position = JSON.parse(json);

	draggable.setPosition(position.x, position.y);
};
