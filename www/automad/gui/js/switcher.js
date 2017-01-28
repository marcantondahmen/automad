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
 *	Copyright (c) 2017 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 * 	Override the UIkit switcher plugin to get/set the current tab from/to the window.location.hash.
 */

+function(Automad, $) {
		
	Automad.switcher = {
		
		getActiveTab: function() {
			
			var tab = 0;
			
			if (window.location.hash) {
				tab = parseInt(window.location.hash.substring(1));
			}
			
			return tab;
			
		}
		
	};
	
	// Override UIkit defaults to show the tab defined in the hash.
	UIkit.on('beforeready.uk.dom', function(){
		$.extend(UIkit.components.switcher.prototype.defaults, {
	        	active: Automad.switcher.getActiveTab()
		});
	});
	
	// Check if the hash value matches the active tab and update the switcher if needed.
	// That will be the case, when a link outside the actual switcher tries to change the active tab.
	$(window).on('hashchange', function() {
		
		var 	$switcher = $('[data-uk-switcher]'),
			$active = $switcher.children('.uk-active'),
			hash = Automad.switcher.getActiveTab();
		
		// Only update if the hash doesn't match the active tab.	
		if ($active.index() != hash) {
			$switcher.children('button').eq(hash).click();
		}
			
	});
	
	// Update the hash on change event.
	$(document).on('ready', function() {
		$('[data-uk-switcher]').on('show.uk.switcher', function(event, tab) {
			window.location.hash = tab.index();
		});
	});
	
}(window.Automad = window.Automad || {}, jQuery);