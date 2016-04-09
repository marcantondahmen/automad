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


// Forms

$(document).on('submit', '.automad-form', function(e) {
		
	/*
	
	Generally, all forms with class ".automad-form" will be sumbitted to their AJAX handler, 
	specified in "data-automad-handler" (within the <form> tag).
	
	For example: 
	"<form class="automad-form" data-automad-handler="page_data"></form>"
	will submit the form to "?ajax=page_data"
	
	==============================================
	
	Since this function should work with all Automad forms, it has some options to be specified:
	
	1. 	URL
		To notify the AJAX handler, that the request belongs to a certain page, the URL has to be 
	 	included in the request.
		Therefore the data attribute "data-automad-url" must be added to the form tag. 

	2.	CLOSE MODAL	
		To automatically close a wrapping modal window after a successful request, 
		the modal's main DIV must have ".automad-close-on-success" added.
	
	3.	RESET FORM
		To automatically reset a form after a successful request, ".automad-reset" has to be added to the form.
	
	==============================================
	
	Server Data:
	The function expects the data from the server to be in JSON format.
	
	1.	data.REDIRECT
		will redirect the page to the given URL.
	
	2.	data.ERROR
		will alert the error in a boostrap alert box.
	
	3.	data.SUCCESS
		same as ERROR, in a green box.
	
	4.	data.HTML
		if any string in data.html gets returned from the server, the form's (inner) HTML will be replaced.
		
	5.	data.DEBUG
		sends any debug information to the console.
	
	*/
	
	e.preventDefault();
	
	var	form =		$(this),
		btn =		form.find('button[type="submit"]'),
		// Action
		handler = 	form.data('automadHandler'),
		// Optional URL parameter.
		// Only needed, to identify a page, in case the form relates to a certain page (edit_page.php).
		// Can be omitted for general form actions.
		url =		form.data('automadUrl'),
		param =		form.serializeArray();
			
	
	// Add URL to parameters, if existing.	
	if (url) {
		param.push({name: 'url', value: url});
	}

		
	// Set loading state for the submit button.	
	btn.button('loading');
	
	
	// Post form data to the handler.
	$.post('?ajax=' + handler, param, function(data) {
		
		// Remove previous alerts
		form.parent().find('.alert').remove();
			
		// In case the returned JSON contains a redirect URL, simply redirect the page.
		// A redirect might be needed, in case other elements on the page, like the navigation, have to be updated as well.
		if (data.redirect) {
			window.location.href = data.redirect;
		}

		// If HTML gets returned within the JSON data, replace the form's (inner) HTML.
		if (data.html) {
			form.html(data.html);
		}
		
		// Display error, if existing.
		if (data.error) {
			
			// Check if form is wrapped in a modal window, to determine the insertion point for the alert box.
			if (form.parents('.modal-dialog').length !== 0) {
				$('<div class="alert alert-danger alert-dismissable fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + data.error + '</div>').prependTo(form.find('.modal-body'));
			} else {
				$('<div class="alert alert-danger alert-dismissable fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + data.error + '</div>').prependTo(form);
			}
			
		}
		
		// Display success, if existing.
		if (data.success) {
			
			// Check if form is wrapped in a modal window, to determine the insertion point for the alert box.
			if (form.parents('.modal-dialog').length !== 0) {
				$('<div class="alert alert-success alert-dismissable fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + data.success + '</div>').prependTo(form.find('.modal-body'));
			} else {
				$('<div class="alert alert-success alert-dismissable fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + data.success + '</div>').prependTo(form);
			}
			
		}
		
		// On success ...
		if (!data.error) {
			
			// If '.automad-close-on-success' is applied to a modal and the form is actually wrapped in a modal.
			form.closest('.automad-close-on-success').modal('hide');
			
			// If form has '.automad-reset' class added, reset the form's values.			
			form.filter('.automad-reset').each(function() {
				this.reset();
			});
			
		}
		
		// Reset the button.
		btn.button('reset');
		
	}, 'json');
	
});

// Disable Save/Delete buttons for #users, #data and #files when ajax completes.
$(document).ajaxComplete(function() {
	setTimeout(function() {
		$('#data [type="submit"], #files [type="submit"], #users [type="submit"]').prop('disabled', true);
	}, 100);
});

// Re-enable after touching any form element.
$(document).on('change click drop', '#data input, #data textarea, #data select, #data button', function() {
	$('#data [type="submit"]').prop('disabled', false);
});

$(document).on('change', '#files .box input, #users input', function() {
	$('#files [type="submit"], #users [type="submit"]').prop('disabled', false);
});

// Auto Init
$(document).ready(function() {

	// All forms with class ".automad-init" get submitted when page is ready to get initial content via AJAX.
	// If the form is wrapped in a modal, it also gets "refreshed", when opening the modal window.
	$('.automad-init').trigger('submit');
	
	$('.modal').on('show.bs.modal', function (e) {
		$(this).find('.automad-init').submit();
	});

});

