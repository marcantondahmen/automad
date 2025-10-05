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

import { App, Attr, CSS, EventName, queryAll } from '.';

/**
 * Initialize all toggles within a given container and toggle the visibily of their targets accordingly.
 *
 * @param container
 */
export const initCheckboxToggles = (container: HTMLElement) => {
	const checkboxes = queryAll<HTMLInputElement>(
		`input[${Attr.toggle}]`,
		container
	);

	checkboxes.forEach((checkbox) => {
		const targets = queryAll(checkbox.getAttribute(Attr.toggle));
		const toggleTargets = () => {
			targets.forEach((target) => {
				target.classList.toggle(CSS.displayNone, !checkbox.checked);
			});
		};

		App.root.listen(
			checkbox,
			`change ${EventName.changeByBinding}`,
			toggleTargets
		);

		toggleTargets();
	});
};
