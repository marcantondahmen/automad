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
 * 	Track scroll position.
 */

+function(Automad, $) {
		
	Automad.scrollPosition = {
		
		init: function() {
			
			var	$doc = $(document),
				$html = $('html');
			
			// Create event to be triggered only once when scrolling has finished.
			$(window).scroll(function() {
				
				if (this.scrolling) {
					clearTimeout(this.scrolling);
				}
				
				this.scrolling = setTimeout(function() {
					$(this).trigger('scrolled.automad');	
				}, 10);
		
			}); 
			
			// Handle scrolled event.
			$(window).on('load scrolled.automad', function() {
				
				var scrolled = $doc.scrollTop(),
					cls = 'am-scrolled',
					clsSecondary = 'am-scrolled-secondary';
				
				if (scrolled > 30) {
					$html.addClass(cls);
				} else {
					$html.removeClass(cls);
				}

				if (scrolled > 60) {
					$html.addClass(clsSecondary);
				} else {
					$html.removeClass(clsSecondary);
				}
				
			});
			
		}
		
	}
	
	$(document).ready(Automad.scrollPosition.init);
	
}(window.Automad = window.Automad || {}, jQuery);