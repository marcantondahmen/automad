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


// File Renaming

// Modal setup.
$(document).on('click', '[data-target="#automad-rename-file-modal"]', function() {
	
	var	modal =		$('#automad-rename-file-modal'),
		modalBody =	modal.find('.modal-body').empty(),	// Reset the body's HTML
		inputOld =	$('<input>', { "type": "hidden", "name": "old-name" }).val($(this).data('file')).appendTo(modalBody),
		inputNew =	$('<input>', { "type": "text", "name": "new-name", "class": "form-control" }).val($(this).data('file')).appendTo(modalBody);
	
	// Clear modal body's HTML to remove inputs and refresh filelist when modal closes.
	// As good practice, useless data shouldn't be sent when the #files main form (deleting files) gets submitted.
	modal.on('hidden.bs.modal', function() {
		
		var	filesForm = modal.closest('form');
		
		// Clear modal be removing generated input fields.
		modalBody.empty();
		// Deselect files (uncheck).
		filesForm.get(0).reset();
		// Submit form to refresh list and entirely remove the modal and its events to clean up.
	  	filesForm.submit();
		
	});
		
});

// Ajax request.
$(document).on('click', 'button#rename-file', function() {

	var	btn = 		$(this).button('loading'),
		modal = 	$('#automad-rename-file-modal'),
		param =		{
					"url": modal.data('automadUrl'), // If URL is empty, the "/shared" files will me managed.
					"old-name": modal.find('[name="old-name"]').val(),
					"new-name": modal.find('[name="new-name"]').val()
				};
	
	// Post form data to the handler.
	$.post('?ajax=rename_file', param, function(data) {
		
		btn.button('reset');
		
		if (data.error) {
			// Remove previous alerts.
			modal.find('.alert').remove();
			// Prepend alert.
			$('<div class="alert alert-danger alert-dismissable fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + data.error + '</div>').prependTo(modal.find('.modal-body'));
		} else {
			modal.modal('hide');
		}
		
	}, 'json');
	
});
