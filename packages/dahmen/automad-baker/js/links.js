/*
 *	Baker
 *
 *	Copyright (c) 2018-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *  MIT license
 */


+function(baker, $) {
	
	baker.links = {
		
		external: function() {
			$('a[href^="http"]').not('a[href$=".zip"]').attr('target', '_blank');
		}
		
	}
		
	$(document).on('ready', baker.links.external);

}(window.baker = window.baker || {}, jQuery);