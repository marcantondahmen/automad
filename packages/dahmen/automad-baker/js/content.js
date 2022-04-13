/*
 *	Baker
 *
 *	Copyright (c) 2018-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *  MIT license
 */


+function(baker, $) {
	
	baker.content = {
		
		anchors: function() {
			
			$('.baker-content > h2').each(function() {
				
				var	$h = $(this),
					id = $h.attr('id');
					
				$h.wrapInner('<a href="#' + id + '"></a>');
				
			});
			
		}
		
	}
		
	$(document).on('ready', baker.content.anchors);

}(window.baker = window.baker || {}, jQuery);