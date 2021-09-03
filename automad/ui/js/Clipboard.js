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
 * Copyright (c) 2018-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

/*
 * Copy string to clipboard.
 */

+(function (Automad, $) {
	Automad.Clipboard = {
		dataAttr: 'data-am-clipboard',

		copy: function (e) {
			e.preventDefault();

			var text = $(this).data(
					Automad.Util.dataCamelCase(Automad.Clipboard.dataAttr)
				),
				$input = $('<input type="text" />')
					.css({
						position: 'fixed',
						top: -1000,
					})
					.attr('contenteditable', 'true')
					.appendTo($('body'))
					.val(text),
				success = false;

			// Fix iOS clipboard issue as described here:
			// https://stackoverflow.com/questions/34045777/copy-to-clipboard-using-javascript-in-ios
			if (navigator.userAgent.match(/ipad|ipod|iphone/i)) {
				var element = $input.get(0),
					range = document.createRange(),
					selection = window.getSelection();

				range.selectNodeContents(element);
				selection.removeAllRanges();
				selection.addRange(range);
				element.setSelectionRange(0, 999999);
			} else {
				$input.select();
			}

			success = document.execCommand('copy');
			$input.remove();

			if (success) {
				Automad.Notify.success(text);
			}
		},
	};

	$(document).on(
		'click',
		'[' + Automad.Clipboard.dataAttr + ']',
		Automad.Clipboard.copy
	);
})((window.Automad = window.Automad || {}), jQuery);
