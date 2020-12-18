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


+function (AutomadBlocks) {

	AutomadBlocks.Embed = {

		twitterResize: function() {

			var iframes = Array.prototype.slice.apply(
				document.querySelectorAll('iframe[id^="am-embed-twitter-"]')
			);

			iframes.forEach(function(iframe) {

				iframe.onload = function() {
					this.contentWindow.postMessage({
						element: this.id,
						query: 'height'
					}, 'https://twitframe.com');
				};

				// Reload iFrame to trigger resize.
				window.addEventListener('resize', function() {
					iframe.src = iframe.src;	
				});

			});

			window.addEventListener('message', function (event) {
				
				if (event.origin != 'https://twitframe.com') {
					return;
				}

				var height = parseInt(event.data.height),
					element = event.data.element;
				
				if (height && element.match(/am-embed-twitter-/)) {
					document.getElementById(element).style.height = height + 'px';
				}
				
			}, false);

		},

		init: function() {

			AutomadBlocks.Embed.twitterResize();

		}

	}

	document.addEventListener('DOMContentLoaded', AutomadBlocks.Embed.init);

}(window.AutomadBlocks = window.AutomadBlocks || {});