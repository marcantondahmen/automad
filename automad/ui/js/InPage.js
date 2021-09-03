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
 * In page editing.
 */

+(function (Automad, $) {
	Automad.InPage = {
		selectors: {
			modal: '#am-inpage-edit-modal',
			fields: '#am-inpage-edit-fields',
			menubar: '.am-inpage-menubar',
			dragHandle: '.am-drag-handle',
		},

		dataAttr: {
			content: 'data-am-inpage-content',
			controller: 'data-am-inpage-controller',
		},

		modal: {
			init: function () {
				var $button = $(this),
					ip = Automad.InPage,
					u = Automad.Util,
					param = $button.data(u.dataCamelCase(ip.dataAttr.content)),
					$modal = $(ip.selectors.modal),
					$form = $modal.find('form'),
					controller = $form.data(
						u.dataCamelCase(ip.dataAttr.controller)
					),
					$loader = $('<i></i>', {
						class: 'uk-icon-circle-o-notch uk-icon-spin uk-icon-small',
					}).appendTo($form);

				// Remove inputs from previous call.
				$(ip.selectors.fields).remove();

				// Get form content.
				$.post(
					controller,
					param,
					function (data) {
						if (data.html) {
							var $fields = $(data.html).appendTo($form).hide();

							// Delay resizing to avoid flicker.
							setTimeout(function () {
								$(window).resize();
							}, 400);

							// Delay fade in to avoid flicker.
							setTimeout(function () {
								$loader.remove();
								$fields.fadeIn(300, function () {
									$(window).resize();
									$fields
										.find(
											'.uk-form-controls, textarea, [contenteditable]'
										)
										.first()
										.focus();
								});
							}, 600);
						}
					},
					'json'
				);
			},

			submit: function (e) {
				e.preventDefault();

				var $form = $(e.target),
					controller = $form.data(
						Automad.Util.dataCamelCase(
							Automad.InPage.dataAttr.controller
						)
					),
					param = $form.serializeArray();

				$.post(
					controller,
					param,
					function (data) {
						if (data.redirect) {
							window.location.href = data.redirect;
						}
					},
					'json'
				);
			},
		},

		menubar: {
			init: function () {
				var ips = Automad.InPage.selectors,
					$menubar = $(ips.menubar).draggabilly({
						handle: ips.dragHandle,
					});
			},
		},
	};

	$(document).on(
		'click',
		'[href="' + Automad.InPage.selectors.modal + '"]',
		Automad.InPage.modal.init
	);

	$(document).on(
		'submit',
		'[' + Automad.InPage.dataAttr.controller + ']',
		Automad.InPage.modal.submit
	);

	$(document).on('ready', Automad.InPage.menubar.init);
})((window.Automad = window.Automad || {}), jQuery);
