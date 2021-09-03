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
 * Color picker.
 */

+(function (Automad, $) {
	Automad.ColorPicker = {
		selector: '[data-am-colorpicker]',

		init: function () {
			var $doc = $(document),
				cp = Automad.ColorPicker;

			$doc.on('change keyup', cp.selector + ' input', cp.update);

			// Add .uk-active class to [data-am-colorpicker] when the text input is focused.
			$doc.on('focus', cp.selector + ' input[type="text"]', function (e) {
				var $input = $(e.target);

				$input.on('blur.automad', function () {
					$input.off('blur.automad');
				});
			});
		},

		update: function (e) {
			var $input = $(e.target),
				cp = Automad.ColorPicker,
				color = $input.val(),
				$combo = $input.closest(cp.selector),
				$picker = $combo.find('[type="color"]'),
				$text = $combo.find('[type="text"]');

			$text.val(color).trigger('keydown');
			$picker.val(color);

			if (!color) {
				color = $text.attr('placeholder');
				$picker.val(color);
			}
		},
	};

	Automad.ColorPicker.init();
})((window.Automad = window.Automad || {}), jQuery);
