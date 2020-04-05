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

		lightbox: function(items) {

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
				
				loaded = function(img, callback) {

					if (img.complete) {
						callback();
					} else {
						img.addEventListener('load', callback);
					}

				},

				fadeIn = function() {

					loaded(img, function () {
						img.classList.remove('am-fade');
					});

				},

				fade = function() {

					img.classList.add('am-fade');

					setTimeout(function() {
						
						img.src = '';
						img.src = items[activeItem].href;
						caption.textContent = items[activeItem].dataset.caption;
						fadeIn();

					}, 200);

				};

			items.forEach(function(item, index) {

				item.addEventListener('click', function(event) {

					event.preventDefault();
					img.src = '';
					img.src = this.href;
					caption.textContent = this.dataset.caption;
					container.classList.add('am-active');
					activeItem = index;
					fadeIn();
	
				});

			});

			close.addEventListener('click', function(event) {

				event.preventDefault();
				container.classList.remove('am-active');
				img.classList.add('am-fade');

			});

			prev.addEventListener('click', function(event) {
				
				event.preventDefault();
				activeItem--;

				if (activeItem < 0) {
					activeItem = items.length - 1;
				}

				fade();

			});

			next.addEventListener('click', function (event) {

				event.preventDefault();
				activeItem++;

				if (activeItem >= items.length) {
					activeItem = 0
				}

				fade();

			});

		},

		init: function() {

			var dataAttr = 'data-am-block-lightbox',
				items = document.body.querySelectorAll('[' + dataAttr + ']');

			if (items.length) {

				items.forEach(function(item) {
					item.removeAttribute(dataAttr);
				});

				AutomadBlocks.Gallery.lightbox(items);

			}

		}

	}

	document.addEventListener('DOMContentLoaded', AutomadBlocks.Gallery.init);

}(window.AutomadBlocks = window.AutomadBlocks || {});