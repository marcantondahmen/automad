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
 * Add link dialog.
 */

+(function (Automad, $, UIkit) {
	Automad.Link = {
		dataAttr: {
			form: 'data-am-link',
			field: 'data-am-link-field',
		},

		modalSelector: '#am-link-modal',

		init: function () {
			var link = Automad.Link,
				da = link.dataAttr;

			$(document).on('click', '[' + da.field + '] button', function () {
				link.click(this);
			});
		},

		click: function (button) {
			var $input = $(button).parent().find('input');

			Automad.Link.dialog($input, function (url) {
				$input.val(url).trigger('keydown').trigger('change');
			});
		},

		dialog: function (elementFocusOnHide, callback) {
			var modal = UIkit.modal(Automad.Link.modalSelector),
				onClick = function (url) {
					if (modal.isActive()) {
						if (typeof callback == 'function') {
							callback(url);
						}

						modal.hide();
					}
				};

			modal.show().find('input').val('');

			modal.on('click.automad.link', '.uk-form button', function () {
				onClick($(this).parent().find('input').val());
			});

			modal.on('hide.uk.modal.automad.link', function () {
				elementFocusOnHide.focus();
				modal.off('.automad.link');
			});
		},

		autocomplete: function () {
			var dataAttrForm = Automad.Link.dataAttr.form,
				$form = $('[' + dataAttrForm + ']').first(),
				controller = $form.data(
					Automad.Util.dataCamelCase(dataAttrForm)
				);

			if ($form.length > 0) {
				$.post(
					controller,
					function (data) {
						var $element = $form.find('.uk-autocomplete').first(),
							options = {
								source: data.autocomplete,
								minLength: 1,
							},
							$autocomplete = UIkit.autocomplete(
								$element,
								options
							);

						$autocomplete.on(
							'selectitem.uk.autocomplete',
							function (data, element) {
								$form.find('input').val(element.value);
								$form.find('button').trigger('click');
							}
						);
					},
					'json'
				);
			}
		},
	};

	Automad.Link.init();

	$(document).on('ready', Automad.Link.autocomplete);
})((window.Automad = window.Automad || {}), jQuery, UIkit);
