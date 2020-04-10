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

		slider: function(container, options) {

			var items = container.querySelectorAll('.am-slider-item');

			if (items.length > 1) {

				var prev = document.createElement('a'),
					next = document.createElement('a'),
					dotWrapper = document.createElement('div'),
					dots = [],
					activeItem = 0,
					timer,
					interval = function() {
						
						if (options.autoplay) {

							if (timer) {
								clearInterval(timer);
							}

							timer = setInterval(function () {
								change(activeItem + 1, null);
							}, 4000);

						}
						
					},
					change = function(index, event) {

						if (event) {
							event.preventDefault();
						}
						
						activeItem = index;

						if (activeItem < 0) {
							activeItem = items.length - 1;
						}

						if (activeItem >= items.length) {
							activeItem = 0
						}

						interval();
						fade();

					},
					fade = function() {
						
						var currentItem = container.querySelector('.am-slider-item.am-active');
							
						currentItem.classList.remove('am-active');
						items[activeItem].classList.add('am-active');

						if (options.dots) {

							var currentDot = container.querySelector('.am-slider-dots .am-active');

							currentDot.classList.remove('am-active');
							dots[activeItem].classList.add('am-active');

						}

					};

				prev.classList.add('am-slider-prev');
				next.classList.add('am-slider-next');
				container.appendChild(prev);
				container.appendChild(next);

				if (options.dots) {

					dotWrapper.classList.add('am-slider-dots');
					container.appendChild(dotWrapper);

					for (var i = 0; i < items.length; i++) {

						(function (index) {

							var dot = document.createElement('a');

							dot.addEventListener('click', function (event) {
								change(index, event);
							});

							if (index === 0) {
								dot.classList.add('am-active');
							}

							dots.push(dot);
							dotWrapper.appendChild(dot);

						}(i));

					}

				}

				prev.addEventListener('click', function(event) {
					change(activeItem - 1, event);
				});

				next.addEventListener('click', function(event) {
					change(activeItem + 1, event);
				});

				interval();

			}

		},

		init: function() {

			var dataAttr = 'data-am-block-slider',
				sliders = document.body.querySelectorAll('[' + dataAttr + ']');

			if (sliders.length) {

				sliders.forEach(function(container) {

					var options = JSON.parse(container.dataset.amBlockSlider);

					container.removeAttribute(dataAttr);
					AutomadBlocks.Slider.slider(container, options);

				});

			}

		}

	}

	document.addEventListener('DOMContentLoaded', AutomadBlocks.Slider.init);

}(window.AutomadBlocks = window.AutomadBlocks || {});