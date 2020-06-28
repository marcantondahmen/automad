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
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 * 	Upload modal.
 */

+function(Automad, $) {
	
	Automad.upload = {
		
		dataAttr: {
			url: 'data-am-url',
			dropzoneText: 'data-am-dropzone-text',
			browseText: 'data-am-browse-text'
		},
		
		selectors: {
			modal: '#am-upload-modal',
			container: '#am-upload-container'
		},
	
		// Get defined in init function.
		$modal: 	'',
		$container:	'',
		$close:		'',
		page: 		'',
		
		// Dropzone.
		$dropzone: $('<div></div>', { 'class': 'am-files-dropzone uk-hidden-touch' }),
		$input:	$('<input type="file" multiple />'),
		$browse: $('<button></button>', { 
					'type': 'button',
					'class': 'uk-button uk-button-success uk-button-large uk-width-1-1 uk-margin-small-top' 
				 }),
				
		// Init the upload modal.
		init: function(e) {
			
			var	u = Automad.upload,
				util = Automad.util,
				da = u.dataAttr,
				iconDropzone = '<i class="uk-icon-arrows"></i>&nbsp;&nbsp;',
				iconBrowse = '<i class="uk-icon-folder-open"></i>&nbsp;&nbsp;';
			
			u.$modal = $(u.selectors.modal);
			
			// Get Upload-Container and reset the HTML from previuos calls!
			u.$container = $(u.selectors.container).empty();
			
			// If an URL exists as data-attribute for the modal window, it will be used as additional form data.
			u.page = u.$modal.data(util.dataCamelCase(da.url));
			
			// Dropzone.
			u.$dropzone.html(iconDropzone + u.$modal.data(util.dataCamelCase(da.dropzoneText)))
				   	   .appendTo(u.$container);
			u.$input.appendTo(u.$dropzone).hide();
			u.$browse.html(iconBrowse + u.$modal.data(util.dataCamelCase(da.browseText)))
					 .insertAfter(u.$dropzone)
					 .click(function () {
						// Make a button click trigger the file input for browsing.
						Automad.upload.$input.click();
						return false;
					 });
			
			// The modal's close buttons
			u.$close = u.$modal.find('.uk-modal-close'); 	
			
			// Init the actual plugin.
			u.plugin();
			
			// Refresh file list on close.
			// The event doesn't need to removed, since the file list HTML gets replaced when closing the modal.
			$(u.selectors.modal).on('hide.uk.modal', { $filesForm: u.$modal.closest('form') }, u.refresh);
						
		},
		
		// Init the plugin.
		plugin: function() {
			
			var	u = Automad.upload;
			
			u.$input.fileupload({
				
				url: '?ajax=upload',
				dataType: 'json',
				dropZone: u.$dropzone,
				sequentialUploads: true,
				singleFileUploads: true,
				
				// Send also the current page's URL, if existing.
				formData: [{name: 'url', value: u.page}],
		
				// For testing purpose, forceIframeTransport can be enabled to simulate older browsers (IE).
				// Note that with that option enabled, drag & drop doesn't work.
				// forceIframeTransport: true,
				
				add: function(e, data) {
			
					var info = '';
				
					// As fallback when using IframeTransport and the files are uploaded in one go, the text will include all filenames and sizes.
					// In the normal case that always will be only one elements, since the all files from a selection are sent in a single request each.
					$.each(data.files, function(i) {
						info += '<div class="am-text-white uk-text-truncate">' + 
							data.files[i].name +  
							'</div>' +
							'<div class="uk-text-small uk-text-muted">' + 
							Automad.util.formatBytes(data.files[i].size) + 
							'</div>';
					});
			
					data.context = $('<div></div>', { 'class': 'uk-margin-top' }).appendTo(u.$container);
			
					$(info).appendTo(data.context);	
					$('<div class="uk-progress uk-progress-striped uk-active"><div class="uk-progress-bar"></div></div>').appendTo(data.context);
				
					data.context.find('.uk-progress-bar').width('0px');
				
					data.submit();
						
				    	// Disable the close button for every added file.
						// When all uploads are done, the button gets enabled again (always callback).
				    	u.$close.prop('disabled', true);
					
				},
				
				progress: function(e, data){

					var progress = parseInt(data.loaded / data.total * 100, 10);

				 	data.context.find('.uk-progress-bar').width(progress + '%').text(progress + ' %');
					
				},
				
				stop: function (e) {
					
					// When all uploads have finished enable the close button again.
					u.$close.prop('disabled', false);
					
				},
				
				done: function (e, data) {
					
					// Deactivate progress bar.
					data.context.find('.uk-progress').removeClass('uk-progress-striped uk-active');
						
					// In case of an server-side error, show error message in progress bar.		
					if (data.result.error) {	
						data.context.find('.uk-progress').addClass('uk-progress-danger').find('.uk-progress-bar').text(data.result.error);
					} else {
						data.context.find('.uk-progress').addClass('uk-progress-success');
					}
								
				},
				
				fail: function (e, data) {
				
					data.context.find('.uk-progress').addClass('uk-progress-danger').removeClass('uk-progress-striped uk-active');
					data.context.find('.uk-progress-bar').text('Error');
				
				}
					
			});
					
		},
		
		// Reset (empty!) and submit $filesForm to reflect the latest uploads. 
		refresh: function(e) {
			
			e.data.$filesForm.empty().submit();
		
		}
		
	};
	
	// Init modal.
	$(document).on('click', '[href="' + Automad.upload.selectors.modal + '"]', Automad.upload.init);
	
	// Prevent the default action when a file is dropped on the window.
	$(document).on('drop dragover', Automad.upload.selectors.modal, function (e) {
		e.preventDefault();
	});
	
}(window.Automad = window.Automad || {}, jQuery);