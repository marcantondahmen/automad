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
 * Copyright (c) 2017-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

/*
 * Key combos.
 */

+(function (Automad, $) {
	Automad.KeyCombos = {
		init: function () {
			$(window).bind('keydown', function (e) {
				if (e.ctrlKey || e.metaKey) {
					switch (String.fromCharCode(e.which).toLowerCase()) {
						case 's':
							e.preventDefault();
							$(
								'.am-navbar [data-am-submit], .am-inpage .uk-open [type="submit"]'
							).click();
							break;
						case 'j':
							$('.am-navbar-jump [name="target"]').blur().focus();
							return false;
					}
				}
			});
		},
	};

	Automad.KeyCombos.init();
})((window.Automad = window.Automad || {}), jQuery);
