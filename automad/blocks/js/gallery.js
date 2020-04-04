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

		lightbox: function(gallery) {

			var images = gallery.querySelectorAll('.am-gallery-img-small'),
				container = gallery.querySelector('.am-gallery-lightbox'),
				img = container.querySelector('img'),
				caption = container.querySelector('.am-gallery-lightbox-caption'),
				prev = container.querySelector('.am-gallery-lightbox-prev'),
				next = container.querySelector('.am-gallery-lightbox-next'),
				close = container.querySelector('.am-gallery-lightbox-close'),
				activeImage = 0,
				
				loaded = function (img, callback) {

					if (img.complete) {
						callback();
					} else {
						img.addEventListener('load', callback);
					}

				},

				fadeIn = function() {

					loaded(img, function () {
						img.classList.remove('fade');
					});

				},

				fade = function() {

					img.classList.add('fade');

					setTimeout(function() {
						
						img.src = '';
						img.src = images[activeImage].href;
						caption.textContent = images[activeImage].dataset.caption;
						fadeIn();

					}, 200);

				};

			images.forEach(function(image, index) {

				image.addEventListener('click', function(event) {

					event.preventDefault();
					img.src = '';
					img.src = this.href;
					caption.textContent = this.dataset.caption;
					container.classList.add('active');
					activeImage = index;
					fadeIn();
	
				});

			});

			close.addEventListener('click', function(event) {

				event.preventDefault();
				container.classList.remove('active');
				img.classList.add('fade');

			});

			prev.addEventListener('click', function(event) {
				
				event.preventDefault();
				activeImage--;

				if (activeImage < 0) {
					activeImage = images.length - 1;
				}

				fade();

			});

			next.addEventListener('click', function (event) {

				event.preventDefault();
				activeImage++;

				if (activeImage >= images.length) {
					activeImage = 0
				}

				fade();

			});

		},

		init: function() {

			var dataAttr = 'data-gallery',
				galleries = document.body.querySelectorAll('[' + dataAttr + ']');

			galleries.forEach(function(gallery) {

				gallery.removeAttribute(dataAttr);
				AutomadBlocks.Gallery.lightbox(gallery);

			});

		}

	}

	document.addEventListener('DOMContentLoaded', AutomadBlocks.Gallery.init);

}(window.AutomadBlocks = window.AutomadBlocks || {});