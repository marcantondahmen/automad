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
 * Import file from URL dialog.
 */

+(function (Automad, $, UIkit) {
	Automad.Import = {
		selectors: {
			modal: '#am-import-modal',
			button: '#am-import-modal .uk-form button',
			input: '#am-import-modal [name="importUrl"]',
		},

		dataAttr: {
			url: 'data-am-url',
		},

		init: function () {
			var ai = Automad.Import;

			$(document).on('click', ai.selectors.button, function () {
				var $modal = $(ai.selectors.modal),
					$input = $(ai.selectors.input),
					importUrl = $input.val(),
					$form = $modal.closest('form'),
					url = $modal.data(
						Automad.Util.dataCamelCase(ai.dataAttr.url)
					);

				$.post(
					'?controller=File::import',
					{ url: url, importUrl: importUrl },
					function (data) {
						if (data.error) {
							Automad.Notify.error(data.error);
						} else {
							$modal.on(
								'hide.uk.modal.automad.import',
								function () {
									$modal.off('automad.import');
									$form.empty().submit();
								}
							);

							UIkit.modal(ai.selectors.modal).hide();
						}
					},
					'json'
				);
			});
		},
	};

	Automad.Import.init();
})((window.Automad = window.Automad || {}), jQuery, UIkit);
