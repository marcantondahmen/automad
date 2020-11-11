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
 *	Image select dialog. 
 */

+function (Automad, $, UIkit) {

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

				si.dialog($input, false, function(url) {
					$input.val(url).trigger('keydown');
				});

			});

		},

		dialog: function(elementFocusOnHide, resize, callback) {

			var modalSelector = Automad.selectImage.modalSelector,
				modal = UIkit.modal(modalSelector),
				onClick = function(url, modalElementClicked) {

					if (modal.isActive()) {

						if (resize) {

							// Add size options in case a label was clicked.
							if (modalElementClicked.tagName.toLowerCase() == 'label') {

								var width = modal.find('[name="width"]').val(),
									height = modal.find('[name="height"]').val();

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

			modal.on('click.automad.selectImage', 'form button', function() {
				onClick($(this).parent().find('input').val(), this);
			});

			modal.on('click.automad.selectImage', 'form label', function() {
				onClick($(this).find('input').val(), this);
			});

			modal.on('hide.uk.modal.automad.selectImage', function () {
				
				if (elementFocusOnHide) {
					elementFocusOnHide.focus();
				}
				
				modal.off('.automad.selectImage');

			});
			
		}

	};

	Automad.selectImage.init();

}(window.Automad = window.Automad || {}, jQuery, UIkit);