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
 */


+function(AutomadBlocks) {

	AutomadBlocks.Gallery = {

		Lightbox: {

			create: function (items) {

				var container = document.createElement('div'),
					controls = '';

				if (items.length > 1) {
					controls = [
						'<a class="am-gallery-lightbox-prev"></a>',
						'<a class="am-gallery-lightbox-next"></a>'
					].join('');
				}

				container.classList.add('am-gallery-lightbox');
				container.innerHTML = [
					'<img src="" class="am-fade">',
					'<div class="am-gallery-lightbox-caption"></div>',
					'<a class="am-gallery-lightbox-close" href="#"></a>',
					controls
				].join('');

				document.body.appendChild(container);

				var img = container.querySelector('img'),
					caption = container.querySelector('.am-gallery-lightbox-caption'),
					prev = container.querySelector('.am-gallery-lightbox-prev'),
					next = container.querySelector('.am-gallery-lightbox-next'),
					close = container.querySelector('.am-gallery-lightbox-close'),
					activeItem = 0,

					loaded = function (img, callback) {

						if (img.complete) {
							callback();
						} else {
							img.addEventListener('load', callback);
						}

					},

					fadeIn = function () {

						loaded(img, function () {
							img.classList.remove('am-fade');
						});

					},

					fade = function () {

						img.classList.add('am-fade');

						setTimeout(function () {

							img.src = '';
							img.src = items[activeItem].href;
							caption.textContent = items[activeItem].dataset.caption;
							fadeIn();

						}, 200);

					};

				items.forEach(function (item, index) {

					item.addEventListener('click', function (event) {

						event.preventDefault();
						img.src = '';
						img.src = this.href;
						caption.textContent = this.dataset.caption;
						container.classList.add('am-active');
						activeItem = index;
						fadeIn();

					});

				});

				close.addEventListener('click', function (event) {

					event.preventDefault();
					container.classList.remove('am-active');
					img.classList.add('am-fade');

				});

				if (prev) {

					prev.addEventListener('click', function (event) {

						event.preventDefault();
						activeItem--;

						if (activeItem < 0) {
							activeItem = items.length - 1;
						}

						fade();

					});

				}
				
				if (next) {

					next.addEventListener('click', function (event) {

						event.preventDefault();
						activeItem++;

						if (activeItem >= items.length) {
							activeItem = 0
						}

						fade();

					});

				}
				
			},

			init: function () {

				var dataAttr = 'data-am-block-lightbox',
					items = document.body.querySelectorAll('[' + dataAttr + ']');

				if (items.length) {

					items.forEach(function (item) {
						item.removeAttribute(dataAttr);
					});

					AutomadBlocks.Gallery.Lightbox.create(items);

				}

			}

		},

		Masonry: {

			getItemRowSpan: function (item) {

				return parseInt(window.getComputedStyle(item).getPropertyValue('--am-gallery-masonry-rows'));

			},

			cleanBottomEdge: function(masonry, items, rowHeight, rowGap) {

				var columns = {},
					rowCount = Math.ceil((masonry.getBoundingClientRect().height + rowGap) / (rowHeight + rowGap)),
					columnNumber = 0;

				// Create a columns object with the x coordinate as key.
				// All items sharing the same x value get stored in the same "column".

				for (var i = 0; i < items.length; ++i) {

					var item = items[i];

					item.style.gridColumnStart = '';
					item.style.gridRowStart = '';

					// Add 1000 to index to always be positive with negative margins.
					// This is needed to keep the object sorted and get the right column number.
					var x = Math.ceil(item.getBoundingClientRect().x) + 1000;

					columns[x] = columns[x] || [];
					columns[x].push(item);

				}

				for (var x in columns) {

					var column = columns[x],
						columnRows = 0,
						rowsFromTop = 1,
						rest = rowCount;

					columnNumber++;

					// Set column start for each element and collect number of rows used of the column.
					column.forEach(function (item) {
						item.style.gridColumnStart = columnNumber;
						columnRows += AutomadBlocks.Gallery.Masonry.getItemRowSpan(item);
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

							var rowSpan = diffRows + AutomadBlocks.Gallery.Masonry.getItemRowSpan(item);

							item.style.gridRowStart = rowsFromTop;
							item.style.gridRowEnd = 'span ' + rowSpan;
							rest -= rowSpan;
							rowsFromTop += rowSpan;

						}

					});

				}

			},

			init: function() {

				var galleryCleanBottom = function () {

					var galleries = document.querySelectorAll('.am-gallery-masonry-clean-bottom');

					for (var i = 0; i < galleries.length; ++i) {

						var gallery = galleries[i],
							items = gallery.querySelectorAll('.am-gallery-masonry-item');

						// Reset inline styles.
						for (var j = 0; j < items.length; ++j) {
							items[j].style.gridRowStart = '';
							items[j].style.gridRowEnd = '';
							items[j].style.gridColumnStart = '';
						}

						AutomadBlocks.Gallery.Masonry.cleanBottomEdge(gallery, items, 50, 0);

					}

				}

				galleryCleanBottom();
				window.addEventListener('load', galleryCleanBottom);
				window.addEventListener('resize', galleryCleanBottom);

			}

		}

	}

	document.addEventListener('DOMContentLoaded', AutomadBlocks.Gallery.Lightbox.init);
	document.addEventListener('DOMContentLoaded', AutomadBlocks.Gallery.Masonry.init);

}(window.AutomadBlocks = window.AutomadBlocks || {});