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
 *	AUTOMAD
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


// ===================================================
// Button: Add Custom Variable (page & shared)
// ===================================================

$(document).on('click', '#automad-add-variable-button', function() {	
	
	// There must be an existing target container with the ID '#automad-custom-variables' 
	// within the page's markup. The created variable input will be appended to that target container.
	
	var  	idPrefix = 'input-data-',
		name = $('#automad-add-variable-name').val().replace(/[^\w\.\-]/g, '_').toLowerCase();

	if ($('#' + idPrefix + name).length == 0){
	
		if (name) {
			
			var	newFormGroup = 	$('<div class="form-group"><label for="' + idPrefix + name + '">' 
						+ name.charAt(0).toUpperCase() + name.slice(1) 
						+ '</label><button type="button" class="close automad-remove-parent">&times;</button>' 
						+ '<textarea id="' + idPrefix + name + '" class="form-control" name="data[' + name + ']" rows="10"></textarea></div>')
						.appendTo($('#automad-custom-variables'));
						
			$('#automad-add-variable-modal').modal('hide');
			$('#automad-add-variable-name').val('');
			
			// Trigger key up event once to resize textarea
			newFormGroup.find('textarea').trigger('keyup');
			
		} else {
			
			$('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + $(this).data('automadErrorName') + '</div>')
			.prependTo($('#automad-add-variable-modal .modal-body'));
			
		}
	
	} else {
		
		$('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + $(this).data('automadErrorExists') + '</div>')
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
// File Renaming
// ===================================================

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




// ===================================================
// File Uploader
// ===================================================

$(document).on('click', '[data-target="#automad-upload-modal"]', function() {
	
	
	var	modal =		$($(this).data('target')),
	
		// If an URL exists as data-attribute for the modal window, it will be used as additional form data.
		url = 		modal.data('automadUrl'),
		
		// Text
		dropzoneText =	modal.data('automadDropzoneText'),
		browseText =	modal.data('automadBrowseText'),
	
		// Find Upload-Container and reset the HTML from previuos calls!
		uploader =	modal.find('#automad-upload').html(''),
		
		// Dropzone
		dropzone =	$('<div class="dropzone"><div class="text-muted">' + dropzoneText + '</div></div>').appendTo(uploader),
		input =		$('<input type="file" multiple />').appendTo(dropzone).hide(),
		browse =	$('<button class="btn btn-primary"><span class="glyphicon glyphicon-folder-open"></span> ' + browseText + '</button>').click(function() {
					// Make a button click trigger the file input for browsing.
					input.click();
					return false;
				}).insertAfter(dropzone),
		
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
				text = text + '<h5><span class="badge">' + fileSize(data.files[i].size) + '</span> ' + data.files[i].name + '</h5>';
			});
	
			data.context = $('<div class="box"></div>').appendTo(uploader);
	
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
		
		stop: function (e) {
			// When all uploads have finished enable the close button again.
			close.button('reset');
		},
		
		done: function (e, data) {
				
			// In case of an server-side error, add alert message.		
			if (data.result.error) {	
				data.context.find('.progress-bar').addClass('progress-bar-warning');
				data.context.find('.progress').after('<div class="alert alert-warning">' + data.result.error + '</div>');
			} else {
				data.context.find('.progress-bar').addClass('progress-bar-success');
			}
			
			// Deactivate progress bar.
			data.context.find('.progress').removeClass('progress-striped active');
				    	
		},
		
		fail: function (e, data) {
		
			data.context.find('.progress-bar').text('Error').addClass('progress-bar-danger');
		
		}
		
	});
	
	
	// Refresh filelist and remove uploader when modal closes.
	// To prevent unintended deletion of files, all selected files get de-selected first.
	// Also the Uploader will be removed when by the ajax request, since a clean form will be sent back.
	modal.on('hidden.bs.modal', function (e) {
		
		var	filesForm = modal.closest('form');
		
		// Deselect files (uncheck).
		filesForm.get(0).reset();
		// Submit form to refresh list.
	  	filesForm.submit();
		
	})
	
	
	// Show modal
	modal.modal({
		show: true,
		backdrop: 'static',
		keyboard: false
        });
	
	
});




// ===================================================
// Forms
// ===================================================

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




// ===================================================
// ScrollBars
// ===================================================

$(document).ready(function() {
	
	$('.scroll').mCustomScrollbar({
		scrollbarPosition: 'outside',
		theme: 'minimal-dark',
		autoHideScrollbar: true,
		scrollInertia: 300,
		scrollButtons: { 
			enable: false 
		}
	});
	
	setTimeout(function() {
		
		var activePage = $('.column.nav li.active');
		
		if ((activePage.offset().top + 80) > $(window).height()) {
			
			$('.column.nav .scroll').mCustomScrollbar('scrollTo', function() {
				return activePage.offset().top - $(window).height() + 80;
			}, { scrollInertia: 150 });
			
		}
		
	}, 100);
		
});


	

// ===================================================
// Site Tree: Collapse/Expand
// ===================================================

$(document).ready(function() {
	
	var tree = $('.column.nav .pages');
	
	tree.find('li li').has('ul').each(function() {
	
		var 	node = 		$(this),
			children = 	node.children('ul'),
			button = 	$('<a href="#" class="expand"><span class="glyphicon"></span></a>').prependTo(node);
			
		// Collapse the tree only for the non-active pages/path.
		if (node.hasClass('active') || node.find('.active').length > 0) {
			button.children('.glyphicon').addClass('glyphicon-minus-sign');
		} else {
			button.children('.glyphicon').addClass('glyphicon-plus-sign');
			children.hide();
		}
		
		// Toggle class and visibility.
		button.click(function(e) {
			e.preventDefault();
			children.toggle();
			button.children('.glyphicon').toggleClass('glyphicon-plus-sign glyphicon-minus-sign');
		});
		
	});
	
});




// ===================================================
// System Status
// ===================================================

function getStatus() {
	
	$('.automad-status').each(function() {
	
		// To get the status of any config setting (constant),
		// the container has to have the class '.automad-status'.
		// The requested item for each container is passed via 
		// 'data-automad-status="item"'. 
		
		var 	container = $(this),
			item = container.data('automadStatus');
		
		$.post('?ajax=status', {"item": item}, function(data) {
			container.html(data.status);		
		}, 'json');
		
	});
	
}

$(document).ready(function() {
	getStatus();
});

$(document).ajaxComplete(function(e, xhr, settings) {
	// Make sure the status doesn't get triggert by itself in an infinite loop.
	if (settings.url != '?ajax=status') {
		getStatus();
	}
});	
	
	


// ===================================================
// Textareas: Auto Resize
// ===================================================

$(document).on('keyup', 'textarea:visible', function() {
	
	var	ta = 		$(this),
		content =	ta.val().replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br />'),
		
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
					
	ta.height(clone.height());
		
	clone.remove();
	
});

// Update also on drop, but with timeout.
// The timeout is needed to make sure the dropped text gets recognized.
$(document).on('drop', 'textarea:visible', function() {
	setTimeout(function() {
		$('textarea').trigger('keyup');
	}, 50);
})

// Update also when AJAX completes.
$(document).ajaxComplete(function() {
	$('textarea').trigger('keyup');
});

// Update also on resize.
$(window).resize(function() {
	$('textarea').trigger('keyup');
});




// ===================================================
// Textareas: Tabs
// ===================================================

$(document).on('keydown', 'textarea', function(e) {
	
	// Insert \t at caret when TAB is pressed, instead of jumping to the next textarea or button.
	if (e.keyCode === 9) { 
		
		var 	start = this.selectionStart,
		 	end = this.selectionEnd,
			value = $(this).val();

		// Set textarea value to text before caret + tab + text after caret.
		$(this).val(value.substring(0, start) + "\t" + value.substring(end));

		// Put caret at right position again (add one for the tab).
		this.selectionStart = this.selectionEnd = start + 1;

		// Prevent the focus lose.
		e.preventDefault();
	}
	
});




// ===================================================
// JavaScript check
// ===================================================
	
$(document).ready(function() {

	// Make main container visible.
	$('#noscript').remove();
	$('#script').removeClass('hidden');

});


