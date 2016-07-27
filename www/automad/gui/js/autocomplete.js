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
 *	Copyright (c) 2016 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Submit form when clicking on an autocomplete item or pressing enter when an autocomplete item is focused.      
 */

+function(Automad, $) {

	Automad.autocomplete = {
		
		submitForm: function(e) {
			
			e.preventDefault();
			
			var $form = $(e.target).closest('form');
			
			// Set timeout to make sure that the selected dropdown item is passed as value.
			setTimeout(function() {	
				$form.submit();
			}, 50);
			
		}
		
	};
	
	// Submit autocomplete form on hitting the return key.
	$(document).on('keydown', '.uk-autocomplete > input', function(e) {
		
		if (e.which == 13) {
			Automad.autocomplete.submitForm(e);	
		}
		
	});
	
	// Submit form when selecting an autocomplete value.
	$(document).on('click', '.uk-form .uk-dropdown a', Automad.autocomplete.submitForm);
	
	
}(window.Automad = window.Automad || {}, jQuery);