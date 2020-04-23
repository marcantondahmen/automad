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

				var svg = '<svg width="1.75em" height="1.75em" viewBox="0 0 16 16" fill="%23EC7262" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm0 1A8 8 0 108 0a8 8 0 000 16z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M11.854 4.146a.5.5 0 010 .708l-7 7a.5.5 0 01-.708-.708l7-7a.5.5 0 01.708 0z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M4.146 4.146a.5.5 0 000 .708l7 7a.5.5 0 00.708-.708l-7-7a.5.5 0 00-.708 0z" clip-rule="evenodd"/></svg>';
		
				if (settings.url.includes('page_data') || settings.url.includes('shared_data') || settings.url.includes('inpage_edit')) {
					
					setTimeout(function () {

						$('.uk-form-row img').each(function () {

							var $img = $(this),
								origSrc = $img.attr('src');

							if (this.complete) {

								$('<img>', {
									src: origSrc,
									error: function () {
										$img.attr('src', 'data:image/svg+xml;utf8,' + svg);
										$('<span>')
										.text('Missing: ' + origSrc.split(/[\\/]/).pop())
										.insertAfter($img)
										.addClass('uk-text-danger')
										.attr('style', 'padding-left: 0.5em;');
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