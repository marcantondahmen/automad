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
 *	Image select dialog for CodeMirror and input fields. 
 */

+function (Automad, $, UIkit, CodeMirror) {

	Automad.selectImage = {

		dataAttr: {
			'field': 'data-am-select-image-field'
		},

		modalSelector: '#am-select-image-modal',

		init: function() {

			var si = Automad.selectImage,
				da = si.dataAttr;

			$(document).on('click', '[' + da.field + '] button', function() {

				var $input = $(this).parent().find('input');

				si.select(UIkit.modal(si.modalSelector), $input, false, function(url, modalElementClicked) {

					$input.val(url).trigger('change');

				});

			});

		},

		select: function(modal, elementFocusOnHide, resize, callback) {

			var modalSelector = Automad.selectImage.modalSelector,
				onClick = function (url, modalElementClicked) {

					if (modal.isActive()) {

						if (typeof callback == 'function') {
							callback(url, modalElementClicked);
						}

						modal.hide();

					}
				
				};

			// Hide resize options if not needed.
			$(modalSelector).removeClass('am-select-image-resize-hide');

			if (!resize) {
				$(modalSelector).addClass('am-select-image-resize-hide');
			} 

			modal.show();

			modal.on('click.automad.selectImage', 'form button', function() {
				onClick($(this).parent().find('input').val(), this);
			});

			modal.on('click.automad.selectImage', 'form label', function() {
				onClick($(this).find('input').val(), this);
			});

			modal.on('hide.uk.modal.automad.selectImage', function () {
				elementFocusOnHide.focus();
				modal.off('.automad.selectImage')
			});
			
		}

	};

	Automad.selectImage.init();

	CodeMirror.defineExtension('AutomadSelectImage', function () {

		var modalSelector = Automad.selectImage.modalSelector,
			modal = UIkit.modal(modalSelector),
			cm = this;

		Automad.selectImage.select(modal, cm, true, function(url, modalElementClicked) {

			// Add size options in case a label was clicked.
			if (modalElementClicked.tagName.toLowerCase() == 'label') {

				var width = modal.find('[name="width"]').val(),
					height = modal.find('[name="height"]').val();

				if (width && height) {
					url = url + '?' + width + 'x' + height;
				}
				
			}

			cm.replaceRange('![](' + url + ')\n', cm.getCursor());

		});

	});

}(window.Automad = window.Automad || {}, jQuery, UIkit, CodeMirror);