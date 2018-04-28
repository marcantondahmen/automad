/*!
 *	am.grid
 *	Copyright (c) 2017-2018 by Marc Anton Dahmen - http://marcdahmen.de - MIT license
 */

+function(am, $) {
	
	am.grid = {
		
		init: function() {
			
			// Masonry
			var 	$masonry = $('.masonry');
			
			// Init masonry
			$masonry.masonry({
				columnWidth: $('.masonry-item').not('.masonry-item-large').get(0),
				itemSelector: '.masonry-item',
				transitionDuration: 0
			});
			
			// Trigger layout during loading progress to avoid all little overlaps and gaps.
			$masonry.imagesLoaded().progress(function(instance, image) {
				$masonry.masonry();
			});
					
			// Relayout when page is fully loaded (fonts, slideshows etc.).
			$(window).on('load show.uk.slideshow', function() {
				$masonry.masonry();
				// Repeat layout in case other elements 
				// like a slideshow was initialized after all images have been loaded.
				setTimeout(function() {
					$masonry.masonry();
				}, 100);
			});
			
		}
		
	};
	
	$(document).on('ready', am.grid.init);
	
}(window.am = window.am || {}, jQuery);