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
 *	Copyright (c) 2016-2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Get autocomplete data from AJAX request and 
 *	submit selected forms when clicking on an autocomplete item or 
 *	pressing enter when an autocomplete item is focused.      
 */

+function(Automad, $) {

	Automad.autocomplete = {
		
		data: {},
		
		selectors: {
			submit: '[data-am-autocomplete-submit]'
		},
		
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
	// Note that to prevent getting data when being in page editing context, the AJAX request is only
	// submitted in case there is an actual autocomplete element on the page, meaning the current context is the dashboard.
	$(document).on('ready', function() {
		
		if ($('.am-dashboard').length > 0) {
			
			$.post('?ajax=autocomplete', function(data) {
				Automad.autocomplete.data = data;
			}, 'json');
			
		}
		
	});
	
	// Submit autocomplete form on hitting the return key.
	$(document).on('keydown', Automad.autocomplete.selectors.submit + ' .uk-autocomplete input[type="search"]', function(e) {
		
		if (e.which == 13) {
			Automad.autocomplete.submitForm(e);	
		}
		
	});
	
	// Submit form when selecting an autocomplete value (navbar only).
	$(document).on('click', Automad.autocomplete.selectors.submit + ' .uk-dropdown a', Automad.autocomplete.submitForm);
	
}(window.Automad = window.Automad || {}, jQuery);