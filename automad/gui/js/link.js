/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Add link dialog. 
 */

+function (Automad, $, UIkit) {

	Automad.link = {

		dataAttr: {
			'form': 'data-am-link',
			'field': 'data-am-link-field'
		},
		
		modalSelector: '#am-link-modal',

		init: function() {

			var link = Automad.link,
				da = link.dataAttr;

			$(document).on('click', '[' + da.field + '] button', function() {

				link.click(this);

			});

		},

		click: function(button) {

			var $input = $(button).parent().find('input');

			Automad.link.dialog($input, function (url) {

				$input.val(url).trigger('keydown');

			});

		},

		dialog: function(elementFocusOnHide, callback) {

			var modal = UIkit.modal(Automad.link.modalSelector),
				onClick = function(url) {

					if (modal.isActive()) {

						if (typeof callback == 'function') {
							callback(url);
						}

						modal.hide();

					}

				};

			modal.show().find('input').val('');

			modal.on('click.automad.link', '.uk-form button', function() {
				onClick($(this).parent().find('input').val());
			});

			modal.on('hide.uk.modal.automad.link', function() {
				elementFocusOnHide.focus();
				modal.off('.automad.link')
			});

		},

		autocomplete: function() {

			var dataAttrForm = Automad.link.dataAttr.form,
				$form = $('[' + dataAttrForm + ']').first(),
				handler = $form.data(Automad.util.dataCamelCase(dataAttrForm));

			if ($form.length > 0) {

				$.post(handler, function(data) {

					var $element = $form.find('.uk-autocomplete').first(),
						options = { source: data, minLength: 1 },
						$autocomplete = UIkit.autocomplete($element, options);

					$autocomplete.on('selectitem.uk.autocomplete', function(data, element) {
						$form.find('input').val(element.value);
						$form.find('button').click();
					});

				}, 'json');

			}

		}

	};

	Automad.link.init();

	$(document).on('ready', Automad.link.autocomplete);

}(window.Automad = window.Automad || {}, jQuery, UIkit);