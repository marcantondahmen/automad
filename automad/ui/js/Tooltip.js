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
 * 	Remove tooltip component on touch screens.
 */

+(function ($, UIkit) {
	$(document).on('ready ajaxComplete', function () {
		setTimeout(function () {
			$('html.uk-touch [data-uk-tooltip]').removeAttr('data-uk-tooltip');
		}, 200);
	});

	// Override UIkit default src function to display keyboard shortcuts correctly and OS-dependent.
	UIkit.on('beforeready.uk.dom', function () {
		$.extend(UIkit.components.tooltip.prototype.defaults, {
			src: function (element) {
				var title = element.attr('title');

				if (title !== undefined) {
					if (/mac/i.test(window.navigator.userAgent)) {
						title = title.replace('Ctrl', '⌘');
					}

					title = title.replace(
						/\[([^\]]+)\]/,
						'<div><small>$1</small></div>'
					);
					element.data('cached-title', title).removeAttr('title');
				}

				return element.data('cached-title');
			},
		});
	});
})(jQuery, UIkit);
