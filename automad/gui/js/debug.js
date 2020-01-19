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
 *	Copyright (c) 2017-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Whenever an AJAX request answer includes a debug field, that content is 
 *	automatically appended to the console when the request has completed. 
 */
	
+function(Automad, $) {
	
	Automad.debug = {
		
		init: function() {

			$(document).ajaxComplete(function(e, xhr, settings) {

				// The debug property in the JSON response will be defined by
				// the jsonOutput() method of the Dashboard class by using the debug 
				// buffer array created with Debug::log() as value for the debug property.
				if (typeof xhr.responseJSON !== 'undefined') {

					if (xhr.responseJSON.hasOwnProperty('debug')) {

						if (!$.isEmptyObject(xhr.responseJSON.debug)) {

							var data = {};

							data["Ajax: " + settings.url] = xhr.responseJSON.debug;
							console.log(data);

						}

					}

				} 

			});

		}
		
	};

	Automad.debug.init();
	
}(window.Automad = window.Automad || {}, jQuery);