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
 * Image select dialog.
 */

+(function (Automad, $, UIkit) {
	Automad.SelectImage = {
		dataAttr: {
			field: 'data-am-select-image-field',
		},

		modalSelector: '#am-select-image-modal',

		preview: function (input) {
			if (!input.value || /[\*,]/.test(input.value)) {
				return;
			}

			const src = Automad.Util.resolvePath(input.value);

			fetch(src, {
				method: 'HEAD',
			}).then((res) => {
				try {
					const da = Automad.SelectImage.dataAttr,
						wrapper = input.closest(`[${da.field}]`),
						figure = wrapper.querySelector('figure');

					if (res.ok) {
						let img = figure.querySelector('img');

						if (!img) {
							img = document.createElement('img');
							figure.appendChild(img);
							img.src = src;
						} else {
							if (src != img.src) {
								img.src = src;
							}
						}
					} else {
						figure.innerHTML = '';
					}
				} catch (event) {}
			});
		},

		init: function () {
			var si = Automad.SelectImage,
				da = si.dataAttr;

			$(document).on('click', `[${da.field}] button`, function () {
				var $input = $(this).parent().find('input');

				si.dialog($input, false, function (url) {
					$input.val(url);
					$input[0].dispatchEvent(
						new Event('keydown', { bubbles: true })
					);
				});
			});

			$(document).on(
				'change keyup keydown',
				`[${da.field}] input`,
				(event) => {
					si.preview(event.target);
				}
			);

			$(document).on('ajaxComplete', () => {
				const inputs = document.querySelectorAll(`[${da.field}] input`);

				Array.from(inputs).forEach((input) => si.preview(input));
			});
		},

		dialog: function (elementFocusOnHide, resize, callback) {
			var modalSelector = Automad.SelectImage.modalSelector,
				modal = UIkit.modal(modalSelector),
				onClick = function (url, modalElementClicked) {
					if (modal.isActive()) {
						if (resize) {
							// Add size options in case a label was clicked.
							if (
								modalElementClicked.tagName.toLowerCase() ==
								'label'
							) {
								var width = modal.find('[name="width"]').val(),
									height = modal
										.find('[name="height"]')
										.val();

								if (width && height) {
									url = url + '?' + width + 'x' + height;
								}
							}
						}

						if (typeof callback == 'function') {
							callback(url);
						}

						modal.hide();
					}
				};

			// Hide resize options if not needed.
			$(modalSelector).removeClass('am-select-image-resize-hide');

			if (!resize) {
				$(modalSelector).addClass('am-select-image-resize-hide');
			}

			// Check for context in in-Page edit mode before opening modal
			// and initializing the included form.
			var $inPageEdit = $('.am-inpage');

			if ($inPageEdit.length > 0) {
				var context = $inPageEdit.find('[name="context"]').val();

				modal.find('form').data('amUrl', context);
			}

			modal.show();

			modal.on('click.automad.selectImage', 'form button', function () {
				onClick($(this).parent().find('input').val(), this);
			});

			modal.on('click.automad.selectImage', 'form label', function () {
				onClick($(this).find('input').val(), this);
			});

			modal.on('hide.uk.modal.automad.selectImage', function () {
				if (elementFocusOnHide) {
					elementFocusOnHide.focus();
				}

				modal.off('.automad.selectImage');
			});
		},
	};

	Automad.SelectImage.init();
})((window.Automad = window.Automad || {}), jQuery, UIkit);
