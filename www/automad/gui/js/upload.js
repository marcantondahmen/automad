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


// File Uploader

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

