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
 *	Copyright (c) 2014-2016 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Get system status items. 
 *	
 *	To get the status of any config setting (constant),
 * 	the container has to have a 'data-automad-status' attribute.
 *  	The requested item for each container is then passed via 
 *  	'data-automad-status="item"'. 
 */

+function(Automad, $) {
	
	Automad.status = {
		
		dataAttr: 'data-automad-status',
		
		get: function() {
		
			var	s = Automad.status;
			
			$('[' + s.dataAttr + ']').each(function() {
		
				var 	$container = $(this),
					item = $container.data(Automad.util.dataCamelCase(s.dataAttr));
				
				$.post('?ajax=status', {'item': item}, function(data) {
					$container.html(data.status);		
				}, 'json');
				
			});
			
		},
		
		init: function() {
			
			var	$doc = $(document),
				s = Automad.status;
				
			$doc.ready(function() {
				s.get();
			});

			$doc.ajaxComplete(function(e, xhr, settings) {
				// Make sure the status doesn't get triggert by itself in an infinite loop.
				if (settings.url != '?ajax=status') {
					s.get();
				}
			});
			
		}
	
	};
	
	Automad.status.init();
	
}(window.Automad = window.Automad || {}, jQuery);	