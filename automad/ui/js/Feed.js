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
 * Sortable.js wrapper.
 */

+(function (Automad, $) {
	Automad.Feed = {
		dataAttr: 'data-am-feed-fields',

		init: function () {
			const containers = Array.from(
				document.querySelectorAll(`[${this.dataAttr}]`)
			);

			containers.forEach((container) => {
				const form = container.closest('form');

				Sortable.create(container, {
					group: 'feed',
					animation: 200,
					draggable: '.am-feed-field',
					handle: '.am-feed-field',
					forceFallback: true,
					ghostClass: 'sortable-ghost',
					chosenClass: 'sortable-chosen',
					dragClass: 'sortable-drag',
					onSort: (event) => {
						if (form) {
							// Select the first input found in the form.
							// Note that the event.item can not be used here
							// in order to be abled to trigger change events
							// when moving an element outside.
							const input = form.querySelector('input');
							console.log(container.childElementCount);

							$(input).trigger('change');
						}
					},
				});

				container.removeAttribute(this.dataAttr);
			});
		},
	};

	$(document).on('ajaxComplete load', () => {
		Automad.Feed.init();
	});
})((window.Automad = window.Automad || {}), jQuery);
