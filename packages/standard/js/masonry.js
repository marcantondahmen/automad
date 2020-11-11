/*!
 *	Standard.masonry
 *	Copyright (c) 2020 by Marc Anton Dahmen - http://marcdahmen.de - MIT license
 */

+function(Standard, $) {
	
	Standard.masonry = {

		selectors: {
			grid: '.masonry',
			item: '.card',
			content: '.card-content'
		},
		
		// Get number of rows a given item spans.
		calcItemRowSpan: function(item, rowHeight, rowGap) {

			// For all other items, the span has to be calculated.
			var content = item.querySelector(Standard.masonry.selectors.content),
				paddingTop = parseInt(window.getComputedStyle(item).getPropertyValue('padding-top')),
				paddingBottom = parseInt(window.getComputedStyle(item).getPropertyValue('padding-bottom'));
			
			return Math.ceil((content.getBoundingClientRect().height + rowGap + paddingTop + paddingBottom) / (rowHeight + rowGap));

		},

		cleanBottomEdge: function($masonry, $items, rowHeight, rowGap) {
	
			var columns = {},
				rowCount = Math.ceil(($masonry.height() + rowGap) / (rowHeight + rowGap)),
				columnNumber = 0;

			// Create a columns object with the x coordinate as key.
			// All items sharing the same x value get stored in the same "column".
			$items.each(function () {

				this.style.gridColumnStart = '';
				this.style.gridRowStart = '';
				
				// Add 1000 to index to always be positive with negative margins.
				// This is needed to keep the object sorted and get the right column number.
				var x = Math.ceil(this.getBoundingClientRect().x) + 1000; 

				columns[x] = columns[x] || [];
				columns[x].push(this);

			});

			for (var x in columns) {

				var column = columns[x],
					columnRows = 0,
					rowsFromTop = 1,
					rest = rowCount;

				columnNumber++;
				
				// Set column start for each element and collect number of rows used of the column.
				column.forEach(function (item) {
					item.style.gridColumnStart = columnNumber;
					columnRows += Standard.masonry.calcItemRowSpan(item, rowHeight, rowGap);
				});

				// Calculate the diff of the used rows with the full number of rows spanned by the container.
				var diff = rowCount - columnRows,
					diffRows = 0;

				if (diff != 0) {
					diffRows = Math.floor(diff / column.length);
				}

				// Distribute the diffRows to each item in a column.
				// The last item simply get the rest, in case there are left over rows due to rounding.
				column.forEach(function (item, index) {

					if (index == column.length - 1) {
						
						item.style.gridRowStart = rowsFromTop;
						item.style.gridRowEnd = 'span ' + rest;
						
					} else {
						
						var rowSpan = diffRows + Standard.masonry.calcItemRowSpan(item, rowHeight, rowGap);
						
						item.style.gridRowStart = rowsFromTop;
						item.style.gridRowEnd = 'span ' + rowSpan;
						rest -= rowSpan;
						rowsFromTop += rowSpan;

					}

				});

			}

		},

		layout: function() {

			var $masonrys = $(Standard.masonry.selectors.grid);

			$masonrys.each(function() {

				var masonry = this,
					$items = $(masonry).find(Standard.masonry.selectors.item),
					rowHeight = parseInt(window.getComputedStyle(masonry).getPropertyValue('grid-auto-rows')),
					rowGap = parseInt(window.getComputedStyle(masonry).getPropertyValue('row-gap'));	

				masonry.classList.remove('masonry-clean-bottom');

				$items.each(function() {
					this.style.gridRowStart = '';
					this.style.gridColumnStart = '';
					this.style.gridRowEnd = 'span ' + Standard.masonry.calcItemRowSpan(this, rowHeight, rowGap);
				});

				Standard.masonry.cleanBottomEdge($(masonry), $items, rowHeight, rowGap);
				
				masonry.classList.add('masonry-clean-bottom');

			});	

		},

		init: function() {

			$(window).on('load resize orientationchange', function() {
				Standard.masonry.layout();
				setTimeout(Standard.masonry.layout, 300);
			});

			$(document).on('ready', function() {
				Standard.masonry.layout();
				$(Standard.masonry.selectors.grid).imagesLoaded().progress(Standard.masonry.layout);
			});

		}
	};
	
	Standard.masonry.init();
	
}(window.Standard = window.Standard || {}, jQuery);