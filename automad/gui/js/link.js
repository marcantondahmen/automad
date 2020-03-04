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
 *	Add link dialog for CodeMirror and input fields. 
 */

+function (Automad, $, UIkit, CodeMirror) {

	Automad.link = {

		dataAttr: {
			'field': 'data-am-link-field'
		},
		
		modalSelector: '#am-link-modal',

		init: function() {

			var link = Automad.link,
				da = link.dataAttr;

			$(document).on('click', '[' + da.field + '] button', function() {

				var $input = $(this).parent().find('input');

				link.select(UIkit.modal(link.modalSelector), $input, function(url) {

					$input.val(url).trigger('change');

				});

			});

		},

		select: function(modal, elementFocusOnHide, callback) {

			var onClick = function(url) {

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

			var $modal = $(Automad.link.modalSelector);

			if ($modal.length > 0) {

				$.post('?ajax=autocomplete_link', function(data) {

					var $element = $modal.find('.uk-autocomplete').first(),
						options = { source: data, minLength: 1 },
						$autocomplete = UIkit.autocomplete($element, options);

					$autocomplete.on('selectitem.uk.autocomplete', function(data, element) {
						$modal.find('input').val(element.value);
						$modal.find('.uk-form button').click();
					});

				}, 'json');

			}

		}

	};

	Automad.link.init();

	$(document).on('ready', Automad.link.autocomplete);

	CodeMirror.defineExtension('AutomadLink', function() {

		var modalSelector = Automad.link.modalSelector,
			modal = UIkit.modal(modalSelector),
			cm = this;

		Automad.link.select(modal, cm, function(url) {

			var selection = cm.getSelection();

			if (!selection) {
				selection = url;
			}

			cm.replaceSelection('[' + selection + '](' + url + ')');

		});

	});

}(window.Automad = window.Automad || {}, jQuery, UIkit, CodeMirror);