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
 *	Handle missing images in editors. 
 */
	
+function(Automad, $) {
	
	Automad.missingImg = {
		
		init: function() {

			$(document).ajaxComplete(function (e, xhr, settings) {

				if (settings.url.includes('page_data') || settings.url.includes('shared_data') || settings.url.includes('inpage_edit')) {
					
					setTimeout(function () {

						$('.uk-form-row img').each(function () {

							var $img = $(this),
								origSrc = $img.attr('src');

							if (this.complete) {

								$('<img>', {
									src: origSrc,
									error: function () {
										$img.addClass('uk-hidden');
										$('<span>')
										.text(' Missing: ' + origSrc.split(/[\\/]/).pop())
										.insertAfter($img)
										.addClass('uk-text-danger');
									}
								});

							}

						});

					}, 1500);

				}

			});

		}

	};
	
	Automad.missingImg.init();
	
}(window.Automad = window.Automad || {}, jQuery);