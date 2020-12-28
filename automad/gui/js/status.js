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
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Get system status items. 
 *	
 *	To get the status of any config setting (constant),
 * 	the container has to have a 'data-am-status' attribute.
 *  The requested item for each container is then passed via 
 *  'data-am-status="item"'. 
 */

+function(Automad, $) {
	
	Automad.status = {
		
		dataAttr: 'data-am-status',
		
		get: function() {
		
			var	s = Automad.status;
			
			$('[' + s.dataAttr + ']').each(function() {
		
				var $container = $(this),
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
				
				var triggers = [
								'?ajax=users', 
								'?ajax=add_user', 
								'?ajax=edit_headless_template',
								'?ajax=reset_headless_template',
								'?ajax=update_config',
								'?ajax=get_packages',
								'?ajax=update_system'
							];

				if (triggers.includes(settings.url)) {
					s.get();
				}
				
			});
			
		}
	
	};
	
	Automad.status.init();
	
}(window.Automad = window.Automad || {}, jQuery);	