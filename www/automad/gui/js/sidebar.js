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
 *	Copyright (c) 2016-2017 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Sidebar toggle. 
 */
	
+function(Automad, $) {
	
	Automad.sidebar = {
		
		dataAttr: 'data-am-toggle-sidebar',
		
		classActive: 'uk-active',
			
		toggle: function(e) {
			
			e.preventDefault();
			
			var 	sb = Automad.sidebar,
				id = $(this).data(Automad.util.dataCamelCase(sb.dataAttr)),
				$sidebar = $(id);

			if ($sidebar.hasClass(sb.classActive)) {	
				UIkit.offcanvas.hide(false);
			} else {
				UIkit.offcanvas.show(id);
			}
						
		},
		
		init: function() {
			
			var 	sb = Automad.sidebar,
				$sidebarButton = $('[' + sb.dataAttr + ']');
			
			// Toggle sidebar.
			$sidebarButton.on('click', sb.toggle);
			
			// Toggle icon on events to make sure to have the correct icon also when sidebar is closed by any other trigger than the actual button.
			$('.uk-offcanvas').on({
				'hide.uk.offcanvas': function(){
					$sidebarButton.find('i').addClass('uk-icon-navicon').removeClass('uk-icon-close');
				},
				'show.uk.offcanvas': function(){
					$sidebarButton.find('i').removeClass('uk-icon-navicon').addClass('uk-icon-close');
				}
			});
			
		}
		
	};
	
	
	$(document).on('ready', Automad.sidebar.init);
	
	
}(window.Automad = window.Automad || {}, jQuery);