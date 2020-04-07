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
 *	http://automad.org/license
 */


/*
 *	Fullscreen toggle for editors. 
 */
	
+function(Automad, $) {
	
	Automad.fullscreen = {
		
		classes: {
			fullscreenWrapper: 'am-fullscreen',
			fullscreenHtml: 'am-fullscreen-page'
		},

		dataAttr: {
			toggle: 'data-am-fullscreen'
		},

		init: function() {

			var fs = Automad.fullscreen,
				fsc = fs.classes,
				$doc = $(document);

			$doc.on('click', '[' + fs.dataAttr.toggle + ']', function() {

				var $html = $('html');

				if ($html.hasClass(fsc.fullscreenHtml)) {
					$html.removeClass(fsc.fullscreenHtml);
					$('.' + fsc.fullscreenWrapper).removeClass(fsc.fullscreenWrapper);
				} else {
					$html.addClass(fsc.fullscreenHtml);
					$(this).parent().addClass(fsc.fullscreenWrapper);
				}

				$(window).trigger('resize');

				setTimeout(function() {
					$(window).trigger('resize');
				}, 500);

			});

		}
		
	}
	
	Automad.fullscreen.init();
	
}(window.Automad = window.Automad || {}, jQuery);