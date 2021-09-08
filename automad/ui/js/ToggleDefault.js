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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

/*
 * Update toggle buttons with an additionl global default option.
 */

+(function (Automad, $) {
	Automad.ToggleDefault = {
		dataAttr: 'data-am-toggle-default',

		update: function ($select) {
			var $toggle = $select.closest(
				'[' + Automad.ToggleDefault.dataAttr + ']'
			);

			$toggle.attr('data-selected', $select.find(':selected').val());
		},

		init: function () {
			$('[' + Automad.ToggleDefault.dataAttr + '] select').each(
				function () {
					Automad.ToggleDefault.update($(this));
				}
			);
		},
	};

	$(document).on(
		'change',
		'[' + Automad.ToggleDefault.dataAttr + '] select',
		function (event) {
			Automad.ToggleDefault.update($(this));
		}
	);

	$(document).on('ready ajaxComplete', Automad.ToggleDefault.init);
})((window.Automad = window.Automad || {}), jQuery);
