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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

/*
 * Handle form when creating first user.
 */

+(function (Automad, $) {
	Automad.CreateUser = {
		dataAttr: 'data-am-create-user',

		init: function () {
			const container = document.querySelector(`[${this.dataAttr}]`);

			if (container) {
				const form = container.querySelector('form');

				form.addEventListener('submit', () => {
					const inputs = Array.from(
						container.querySelectorAll('input')
					);
					const panels = Array.from(
						container.querySelectorAll('.uk-panel')
					);

					inputs.forEach((input) => {
						input.removeAttribute('required');
						input.setAttribute('type', 'hidden');
					});

					panels.forEach((panel) => {
						panel.classList.toggle('uk-hidden');
					});
				});
			}
		},
	};

	$(document).on('ready', () => {
		Automad.CreateUser.init();
	});
})((window.Automad = window.Automad || {}), jQuery);
