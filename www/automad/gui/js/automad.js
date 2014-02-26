/*!
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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


// ===================================================
// Button: Add Custom Variable (page & shared)
// ===================================================

$(document).on('click', '#automad-add-variable-button', function() {	

	var  	idPrefix = 'input-data-',
		name = $('#automad-add-variable-name').val().replace(/[^\w]/g, '_').toLowerCase();

	if ($('#' + idPrefix + name).length == 0){
	
		if (name) {
			
			var	newFormGroup = 	$('<div class="form-group col-md-12"><label for="' + idPrefix + name + '" class="text-muted">' 
						+ name.charAt(0).toUpperCase() + name.slice(1) 
						+ '</label><button type="button" class="close automad-remove-parent">&times;</button>' 
						+ '<textarea id="' + idPrefix + name + '" class="form-control input-sm" name="data[' + name + ']" rows="10"></textarea></div>')
						.appendTo($('#automad-custom-variables'));
						
			$('#automad-add-variable-modal').modal('hide');
			$('#automad-add-variable-name').val('');
			
			// Trigger key up event once to resize textarea
			newFormGroup.find('textarea').trigger('keyup');
			
		} else {
			
			$('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>You can not add a variable without a name!</div>')
			.prependTo($('#automad-add-variable-modal .modal-body'));
			
		}
	
	} else {
		
		$('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>There is already a variable with that name!</div>')
		.prependTo($('#automad-add-variable-modal .modal-body'));
		
	}
	
});




// ===================================================
// Button: Move Page (Select Destination from Tree) 
// ===================================================

$(document).on('click', '#automad-move-page-modal a', function() {
	
	var	modal =		$('#automad-move-page-modal'),
		modalBody =	modal.find('.modal-body'),
		// Get the URL and title from the current page.
		page =		modal.data('automadMovePage'),
		// Get the destination from the clicked link within the tree.
		destination =	decodeURIComponent($(this).attr('href').split('=')[1]);
		
	// Post request.
	$.post('?ajax=move_page', {url: page.url, title: page.title, destination: destination}, function(data) {
		
		// Redirect on success to updated page.
		if (data.redirect) {
			window.location.href = data.redirect;
		}
		
		// Error message.
		if (data.error) {
			$('<div class="alert alert-danger alert-dismissable fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + data.error + '</div>').prependTo(modalBody);
		}
		
	}, 'json');
	
	return false;
	
});




// ===================================================
// Button: Remove parent container
// ===================================================

$(document).on('click', '.automad-remove-parent', function() {
	$(this).parent().remove();
});




// ===================================================
// Automad Forms
// ===================================================

$(document).on('submit', '.automad-form', function(e) {
	
	e.preventDefault();
	
	var	form =		$(this),
		btn =		form.find('button[type="submit"]'),
		handler = 	form.data('automadAjaxHandler');	
		
	// Set loading state for the submit button.	
	btn.button('loading');
	
	// Post form data to the handler.
	$.post('?ajax=' + handler, form.serialize(), function(data) {
		
		// In case the returned JSON contains a redirect URL, simply redirect the page.
		// A redirect might be needed, in case other elements on the page, like the navigation, have to be updated as well.
		if (data.redirect) {
			window.location.href = data.redirect;
		}
		
		// Display error, if existing.
		if (data.error) {
			
			// Check if form is wrapped in a modal window, to determine the insertion point for the alert box.
			if (form.parents('.modal-dialog').length !== 0) {
				$('<div class="alert alert-danger alert-dismissable fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + data.error + '</div>').prependTo(form.find('.modal-body'));
			} else {
				$('<div class="alert alert-danger alert-dismissable fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + data.error + '</div>').insertBefore(form);
			}
			
		}
		
		// If HTML gets returned within the JSON data, replace the form's (inner) HTML.
		if (data.html) {
			form.html(data.html);
		}
		
		// Reset the button.
		btn.button('reset');
		
	}, 'json');
	
});




// ===================================================
// Textareas: Auto Resize
// ===================================================

$(document).on('keyup', 'textarea', function() {
	
	var	ta = 		$(this),
		content =	ta.val().replace(/\n/g, '<br />'),
		
		// The hidden clone will be used to determine the actual height.
		clone =		$('<div></div>')
				.appendTo('body')
				.hide()
				.width(ta.width())
				.css({
					'white-space': 'pre-wrap',
					'word-wrap': 'break-word',
					'overflow-wrap': 'break-word',
					'font-family': ta.css('font-family'),
					'font-size': ta.css('font-size'),
					'line-height': ta.css('line-height'),
					'letter-spacing': ta.css('letter-spacing')
				});
			
	ta.css({
		'resize': 'none',
		'overflow': 'hidden'
	});

	clone.html(content + ' ');
	ta.height(clone.height() + 20);
		
	clone.remove();
	
});

// Update also when AJAX completes.
$(document).ajaxComplete(function() {
	$('textarea').trigger('keyup');
});




// ---------------------------------
// DOCUMENT READY
// ---------------------------------
	
$(document).ready(function() {

	// Make main container visible.
	$('#noscript').remove();
	$('#script').show();

	// Submit automad forms to get initial AJAX content.
	$('#data .automad-form').trigger('submit');
	
});


