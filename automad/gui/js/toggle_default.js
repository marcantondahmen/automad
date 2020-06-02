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
 *	Update toggle buttons with an additionl global default option. 
 */
	
+function(Automad, $) {
	
	Automad.toggleDefault = {
		
		dataAttr: 'data-am-toggle-default',
		
		update: function($select) {
			
			var $toggle = $select.closest('[' + Automad.toggleDefault.dataAttr + ']');

			$toggle.attr('data-selected', $select.find(':selected').val());

		}, 
		
		init: function() {
			
			$('[' + Automad.toggleDefault.dataAttr + '] select').each(function() {
				Automad.toggleDefault.update($(this));
			});
			
		}
		
	}
	
	$(document).on('change', '[' + Automad.toggleDefault.dataAttr + '] select', function(event) {
		Automad.toggleDefault.update($(this));
	});
		
	$(document).on('ready ajaxComplete', Automad.toggleDefault.init);
	
}(window.Automad = window.Automad || {}, jQuery);