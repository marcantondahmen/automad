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

	AutomadBlocks.Slider = {

		slider: function(container) {

			var items = container.querySelectorAll('.am-slider-item');

			if (items.length > 1) {

				var prev = document.createElement('a'),
					next = document.createElement('a'),
					activeItem = 0,
					timer,
					interval = function() {
						
						if (timer) {
							clearInterval(timer);
						}

						timer = setInterval(function () {
							change(1);
						}, 5000);

					},
					change = function(increment) {

						activeItem = activeItem + increment;

						if (activeItem < 0) {
							activeItem = items.length - 1;
						}

						if (activeItem >= items.length) {
							activeItem = 0
						}

						fade();

					},
					fade = function() {
						
						var current = container.querySelector('.am-slider-item.am-active');

						current.classList.remove('am-active');
						items[activeItem].classList.add('am-active');

					};

				prev.classList.add('am-slider-prev');
				next.classList.add('am-slider-next');
				container.appendChild(prev);
				container.appendChild(next);

				prev.addEventListener('click', function(event) {
					event.preventDefault();
					interval();
					change(-1);
				});

				next.addEventListener('click', function(event) {
					event.preventDefault();
					interval();
					change(1);
				});

				interval();

			}

		},

		init: function() {

			var dataAttr = 'data-am-block-slider',
				sliders = document.body.querySelectorAll('[' + dataAttr + ']');

			if (sliders.length) {

				sliders.forEach(function(container) {
					container.removeAttribute(dataAttr);
					AutomadBlocks.Slider.slider(container);
				});

			}

		}

	}

	document.addEventListener('DOMContentLoaded', AutomadBlocks.Slider.init);

}(window.AutomadBlocks = window.AutomadBlocks || {});