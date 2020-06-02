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
 *	Import file from URL dialog. 
 */

+function (Automad, $, UIkit) {

	Automad.import = {

		selectors: {
			modal: '#am-import-modal',
			button: '#am-import-modal .uk-form button',
			input: '#am-import-modal [name="importUrl"]'
		},

		dataAttr: {
			url: 'data-am-url'
		},

		init: function() {

			var ai = Automad.import;

			$(document).on('click', ai.selectors.button, function() {

				var $modal = $(ai.selectors.modal),
					$input = $(ai.selectors.input),
					importUrl = $input.val(),
					$form = $modal.closest('form'),
					url = $modal.data(Automad.util.dataCamelCase(ai.dataAttr.url));

				$.post('?ajax=import', { url: url, importUrl: importUrl}, function(data) {
					
					if (data.error) {

						Automad.notify.error(data.error);
						
					} else {

						$modal.on('hide.uk.modal.automad.import', function() {
							$modal.off('automad.import');
							$form.empty().submit();
						});

						UIkit.modal(ai.selectors.modal).hide();
						
					}
					
				}, 'json');

			});

		}

	};

	Automad.import.init();

}(window.Automad = window.Automad || {}, jQuery, UIkit);