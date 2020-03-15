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


+function (Automad, $, UIkit, CodeMirror) {

	CodeMirror.defineExtension('AutomadSelectImage', function () {

		var modalSelector = Automad.selectImage.modalSelector,
			modal = UIkit.modal(modalSelector),
			cm = this;

		Automad.selectImage.dialog(modal, cm, true, function (url, modalElementClicked) {

			if (url) {

				// Add size options in case a label was clicked.
				if (modalElementClicked.tagName.toLowerCase() == 'label') {

					var width = modal.find('[name="width"]').val(),
						height = modal.find('[name="height"]').val();

					if (width && height) {
						url = url + '?' + width + 'x' + height;
					}

				}

				cm.replaceRange('![](' + url + ')\n', cm.getCursor());

			}

		});

	});

}(window.Automad = window.Automad || {}, jQuery, UIkit, CodeMirror);