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
 *	Get autocomplete data from AJAX request and 
 *	submit form when clicking on an autocomplete item or 
 *	pressing enter when an autocomplete item is focused.      
 */

+function(Automad, $) {

	Automad.autocomplete = {
		
		data: {},
		
		submitForm: function(e) {
			
			e.preventDefault();
			
			var $form = $(e.target).closest('form');
			
			// Set timeout to make sure that the selected dropdown item is passed as value.
			setTimeout(function() {	
				$form.submit();
			}, 50);
			
		}
		
	};
	
	// Get autocomplete data.
	$.post('?ajax=autocomplete', function(data) {
		Automad.autocomplete.data = data;
	}, 'json');
	
	// Submit autocomplete form on hitting the return key.
	$(document).on('keydown', '.uk-autocomplete input[type="text"]', function(e) {
		
		if (e.which == 13) {
			Automad.autocomplete.submitForm(e);	
		}
		
	});
	
	// Submit form when selecting an autocomplete value.
	$(document).on('click', '.uk-form .uk-dropdown a', Automad.autocomplete.submitForm);
	
}(window.Automad = window.Automad || {}, jQuery);