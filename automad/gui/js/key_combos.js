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
 *	Key combos. 
 */
	
+function(Automad, $) {
	
	Automad.keyCombos = {
		
		init: function() {
			
			$(window).bind('keydown', function(e) {

				if (e.ctrlKey || e.metaKey) {
					switch (String.fromCharCode(e.which).toLowerCase()) {
						case 's':
							e.preventDefault();
							$('.am-navbar [data-am-submit], .am-inpage .uk-open [type="submit"]').click();
							break;				
					}
				}

				if ((e.ctrlKey || e.metaKey) && e.shiftKey) {
					switch (String.fromCharCode(e.which).toLowerCase()) {
						case ' ':
							e.preventDefault();
							$('.am-navbar-search [name="query"]').focus();
							break;
					}
				}

			});
			
		}
		
	}
	
	Automad.keyCombos.init();
	
}(window.Automad = window.Automad || {}, jQuery);