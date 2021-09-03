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
 * Handle missing images in editors.
 */

+(function (Automad, $) {
	Automad.MissingImages = {
		init: function () {
			$(document).ajaxComplete(function (e, xhr, settings) {
				if (
					settings.url.includes('Page::data') ||
					settings.url.includes('Shared::data') ||
					settings.url.includes('InPage::edit')
				) {
					setTimeout(function () {
						$('.uk-form-row img').each(function () {
							var $img = $(this),
								origSrc = $img.attr('src');

							if (this.complete) {
								$('<img>', {
									src: origSrc,
									error: function () {
										$img.addClass('uk-hidden');
										$('<span>')
											.text(
												' Missing: ' +
													origSrc.split(/[\\/]/).pop()
											)
											.insertAfter($img)
											.addClass('uk-text-danger');
									},
								});
							}
						});
					}, 1500);
				}
			});
		},
	};

	Automad.MissingImages.init();
})((window.Automad = window.Automad || {}), jQuery);
