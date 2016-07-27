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


/*
 * 	Upload modal.
 */

+function(Automad, $) {
	
	Automad.upload = {
		
		selectors: {
			modal: '#automad-upload-modal',
			container: '#automad-upload-container'
		},
	
		// Get defined in init function.
		$modal: 	'',
		$container:	'',
		$close:		'',
		page: 		'',
		
		// Dropzone.
		$dropzone: 	$('<div></div>', { 'class': 'automad-files-dropzone' }),
		$input:		$('<input type="file" multiple />'),
		$browse:	$('<button></button>', { 'class': 'uk-button uk-button-primary uk-width-1-1 uk-margin-top' })
				.click(function() {
					// Make a button click trigger the file input for browsing.
					Automad.upload.$input.click();
					return false;
				}),
				
		// Init the upload modal.
		init: function(e) {
			
			var	u = Automad.upload;
			
			u.$modal = $(u.selectors.modal);
			
			// Get Upload-Container and reset the HTML from previuos calls!
			u.$container = $(u.selectors.container).empty();
			
			// If an URL exists as data-attribute for the modal window, it will be used as additional form data.
			u.page = u.$modal.data('automadUrl');
			
			// Dropzone.
			u.$dropzone.text(u.$modal.data('automadDropzoneText')).appendTo(u.$container);
			u.$input.appendTo(u.$dropzone).hide();
			u.$browse.text(u.$modal.data('automadBrowseText')).insertAfter(u.$dropzone);
			
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
			
					var info = '<hr>';
				
					// As fallback when using IframeTransport and the files are uploaded in one go, the text will include all filenames and sizes.
					// In the normal case that always will be only one elements, since the all files from a selection are sent in a single request each.
					$.each(data.files, function(i) {
						info += '<div class="uk-margin-small-bottom uk-text-truncate"><span class="uk-badge uk-badge-notification">' + 
							Automad.util.formatBytes(data.files[i].size) + '</span>&nbsp;&nbsp;' + data.files[i].name + 
							'</div>';
					});
			
					data.context = $('<div></div>', { 'class': 'uk-margin-large-top uk-margin-large-bottom'}).appendTo(u.$container);
			
					$(info).appendTo(data.context);	
					$('<div class="uk-progress uk-progress-striped uk-active"><div class="uk-progress-bar"></div></div>').appendTo(data.context);
				
					data.context.find('.uk-progress-bar').width('0px');
				
					data.submit();
						
				    	// Disable the close button for every added file.
					// When all uploads are done, the button gets enabled again (always callback).
				    	u.$close.prop('disabled', true);
					u.$close.find('i').removeClass('uk-icon-close').addClass('uk-icon-circle-o-notch uk-icon-spin');
				
				},
				
				progress: function(e, data){

					var 	progress = parseInt(data.loaded / data.total * 100, 10);

					data.context.find('.uk-progress-bar').text(progress + ' %').width(progress + '%');
				 
				},
				
				stop: function (e) {
					
					// When all uploads have finished enable the close button again.
					u.$close.prop('disabled', false);
					u.$close.find('i').addClass('uk-icon-close').removeClass('uk-icon-circle-o-notch uk-icon-spin');
					
				},
				
				done: function (e, data) {
					
					// Deactivate progress bar.
					data.context.find('.uk-progress').removeClass('uk-progress-striped uk-active');
						
					// In case of an server-side error, add alert message.		
					if (data.result.error) {	
						data.context.find('.uk-progress').addClass('uk-progress-warning');
						data.context.find('.uk-progress').after('<div class="uk-alert uk-alert-warning">' + data.result.error + '</div>');
					} else {
						data.context.find('.uk-progress').addClass('uk-progress-success');
					}
								
				},
				
				fail: function (e, data) {
				
					data.context.find('.uk-progress').addClass('uk-progress-danger');
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