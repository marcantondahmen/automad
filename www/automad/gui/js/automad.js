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
	
	// There must be an existing target container with the ID '#automad-custom-variables' 
	// within the page's markup. The created variable input will be appended to that target container.
	
	var  	idPrefix = 'input-data-',
		name = $('#automad-add-variable-name').val().replace(/[^\w]/g, '_').toLowerCase();

	if ($('#' + idPrefix + name).length == 0){
	
		if (name) {
			
			var	newFormGroup = 	$('<div class="form-group"><label for="' + idPrefix + name + '" class="text-muted">' 
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
		url =		modal.data('automadUrl'),
		title =		modal.data('automadTitle'),
		
		// Get the destination from the clicked link within the tree.
		destination =	decodeURIComponent($(this).attr('href').split('=')[1]);
		
	// Post request.
	$.post('?ajax=move_page', {url: url, title: title, destination: destination}, function(data) {
		
		// Debug
		console.log(data);
		
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
	
	// All forms with class '.automad-form' applied 
	// will use 'data-automad-handler' as form action and
	// will prepend 'data-automad-url' (the page's URL) to the request for identification.
	
	e.preventDefault();
	
	var	form =		$(this),
		btn =		form.find('button[type="submit"]'),
		// Action
		handler = 	form.data('automadHandler'),
		// Page "ID"
		url =		form.data('automadUrl');
			
	
	// Build request string
	if (form.serialize()) {
		var 	request = 'url=' + encodeURIComponent(url) + '&' +form.serialize();
	} else {
		var 	request = 'url=' + encodeURIComponent(url);
	}
	
		
	// Set loading state for the submit button.	
	btn.button('loading');
	
	
	// Post form data to the handler.
	$.post('?ajax=' + handler, request, function(data) {
		
		// Debug
		console.log(data);
			
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

$(document).on('keyup', 'textarea:visible', function() {
	
	var	ta = 		$(this),
		content =	ta.val().replace(/\n/g, '<br />'),
		
		// The hidden clone will be used to determine the actual height.
		clone =		$('<div></div>')
				.appendTo('body')
				.hide()
				.width(ta.width())
				.html(content + ' ')
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
	}).height(clone.height() + 20);
		
	clone.remove();
	
});

// Update also when AJAX completes.
$(document).ajaxComplete(function() {
	$('textarea').trigger('keyup');
});




// ---------------------------------
// File Uploader
// ---------------------------------

$(document).on('click', '[data-target="#automad-upload-modal"]', function() {
	
	
	var	modal =		$($(this).data('target')),
	
		// If an URL exists as data-attribute for the modal window, ot will be used as additional form data.
		url = 		modal.data('automadUrl'),
	
		// Find Upload-Container and reset the HTML from previuos calls!
		uploader =	modal.find('#automad-upload').html(''),
		
		// Dropzone
		dropzone =	$('<div class="well well-lg"><h2 class="center-block text-muted" style="text-align: center;">Drop Files here</h2></div>').appendTo(uploader),
		input =		$('<input type="file" multiple />').appendTo(dropzone).hide(),
		browse =	$('<button class="btn btn-lg btn-default center-block">or browse</button>').click(function() {
					// Make a button click trigger the file input for browsing.
					input.click();
					return false;
				}).appendTo(dropzone),
		
		// The modal's close buttons
		close =		modal.find('[data-dismiss="modal"]'); 	
	

	// Prevent the default action when a file is dropped on the window
	$(document).on('drop dragover', function (e) {
		e.preventDefault();
	});
				
	
	// Format file size
	function fileSize(bytes) {
	
		if (typeof bytes !== 'number') {
			return '';
		}

		if (bytes >= 1000000000) {
			return (bytes / 1000000000).toFixed(2) + ' gb';
		}

		if (bytes >= 1000000) {
			return (bytes / 1000000).toFixed(2) + ' mb';
		}

		return (bytes / 1000).toFixed(2) + ' kb';

	};
	

	// Init fileupload plugin.
	input.fileupload({
	
		url: '?ajax=upload',
		dataType: 'json',
		dropZone: dropzone,
		sequentialUploads: true,
		singleFileUploads: true,
		
		// Send also the current page's URL, if existing.
		formData: [{name: 'url', value: url}],
	
		add: function(e, data) {
	
			var	text = '';
		
			// As fallback when using IframeTransport and the files are uploaded in one go, the text will include all filenames and sizes.
			// In the normal case that always will be only one elements, since the all files from a selection are sent in a single request each.
			$.each(data.files, function(i) {
				text = text + '<h5><span class="label label-default">' + fileSize(data.files[i].size) + '</span> ' + data.files[i].name + '</h5>';
			});
	
			data.context = $('<div class="file"></div>').appendTo(uploader);
	
			$(text).appendTo(data.context);	
			$('<div class="progress progress-striped active"><div class="progress-bar"></div></div>').appendTo(data.context);
		
			data.context.find('.progress-bar').width('0px');
		
			data.submit();
				
		    	// Disable the close button for every added file.
			// When all uploads are done, the button gets enabled again (always callback).
		    	close.button('loading');
		
		},
	
		progress: function(e, data){

			var 	progress = parseInt(data.loaded / data.total * 100, 10);

			data.context.find('.progress-bar').text(progress + ' %').animate({width: progress + '%'}, 120);
		 
		},
		
		always: function (e, data) {
			
			// If all uploads are done, or iframe transport is used, enable the close button again.
			// Check first, if iframe-transport is used for upload, otherwise "input.fileupload('active')" is not available.
			if (data.dataType.indexOf('iframe') < 0) {
			
				// Check for running uploads.
				if (input.fileupload('active') <= 1) {
					close.button('reset');
				}
			
			} else {
				
				close.button('reset');
			
			}
			
			// Debug 
			console.log(data.result);
			
	    	},
	
		done: function (e, data) {
				
			// In case of an server-side error, add alert message.		
			if (data.result.error) {	
				data.context.find('.progress-bar').text('Upload completed with errors').addClass('progress-bar-warning');
				data.context.find('.progress').after('<div class="alert alert-warning">' + data.result.error + '</div>');
			} else {
				data.context.find('.progress-bar').addClass('progress-bar-success');
			}
			
			// Deactivate progress bar.
			data.context.find('.progress').removeClass('progress-striped active');
				    	
		},
		
		fail: function (e, data) {
		
			data.context.find('.progress-bar').text('Upload failed').addClass('progress-bar-danger');
		
		}
		
	});
	
	
	// Refresh Files when modal closes.
	modal.on('hidden.bs.modal', function (e) {
	  	modal.closest('form').submit();
	})
	
	
	// Show modal
	modal.modal({
		show: true,
		keyboard: false
        });
	
	
});




// ---------------------------------
// DOCUMENT READY
// ---------------------------------
	
$(document).ready(function() {

	// Make main container visible.
	$('#noscript').remove();
	$('#script').show();

	// Submit automad forms to get initial AJAX content.
	$('#data .automad-form, #files .automad-form').trigger('submit');
	
});


