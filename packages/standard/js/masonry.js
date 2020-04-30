/*!
 *	Standard.masonry
 *	Copyright (c) 2020 by Marc Anton Dahmen - http://marcdahmen.de - MIT license
 */

+function(Standard, $) {
	
	Standard.masonry = {
		
		layout: function() {

			var selectors = {
					grid: '.masonry',
					item: '.masonry-item',
					content: '.masonry-content'
				},
				$masonrys = $(selectors.grid);

			$masonrys.each(function() {

				var masonry = this,
					$items = $(masonry).find(selectors.item);

				$items.each(function() {

					var item = this,
						content = item.querySelector(selectors.content);

					rowHeight = parseInt(window.getComputedStyle(masonry).getPropertyValue('grid-auto-rows'));
					rowGap = parseInt(window.getComputedStyle(masonry).getPropertyValue('grid-row-gap'));
					paddingTop = parseInt(window.getComputedStyle(item).getPropertyValue('padding-top'));
					paddingBottom = parseInt(window.getComputedStyle(item).getPropertyValue('padding-bottom'));
					rowSpan = Math.ceil((content.getBoundingClientRect().height + rowGap + paddingTop + paddingBottom) / (rowHeight + rowGap));
					item.style.gridRowEnd = 'span ' + rowSpan;
					
				});

			});

		}
		
	};
	
	$(window).on('load resize', Standard.masonry.layout);
	$(document).on('ready', function() {
		Standard.masonry.layout();
		$('.masonry').imagesLoaded().progress(Standard.masonry.layout);
	});
	
}(window.Standard = window.Standard || {}, jQuery);