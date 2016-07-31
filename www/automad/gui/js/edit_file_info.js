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
 * 	File information modal.
 */

+function(Automad, $) {
	
	Automad.editFileInfo = {
		
		selectors: {
			modal: '#automad-edit-file-info-modal',
			button:	'#automad-edit-file-info-submit',
			infoContainer: '#automad-edit-file-info-container'
		},
		
		previewHeight: 320,
		previewWidth: 560,
		
		// Define modal and parentForm within info.init() to make sure the DOM is ready, when a user clicks a edit button.
		$modal: '',
		$parentForm: '',
		$infoContainer: '',
		$preview: '',
		
		$nameOld: $('<input>', {
			'type': 'hidden', 
			'name': 'old-name'
		})
		.prop('disabled', true),
		
		$nameNew: $('<input>', {
			'type': 'text', 
			'name': 'new-name', 
			'class': 'uk-form-controls uk-form-large uk-width-1-1 uk-margin-small-bottom'
		}),
		
		$caption: $('<textarea></textarea>', {
			'class': 'uk-form-controls uk-width-1-1 uk-margin-small-top',
			'rows': 1,
			'placeholder': 'Caption' 
		}),
		
		getPreview: function(file, url) {
			
			var	efi = 	Automad.editFileInfo,
				icon =	'<i class="uk-icon-eye-slash uk-icon-medium"></i>',
				param =	{
						'url': url, // If URL is empty, the "/shared" files will be managed.
						'file': file,
						'height': efi.previewHeight,
						'width': efi.previewWidth
					};
			
			$.post('?ajax=get_file_preview', param, function(data) {
				
				if (data.html) {
					
					efi.$preview.fadeOut(300, function() {
						$preview = $(data.html).hide().fadeIn(300);
						efi.$preview.replaceWith($preview);
					});
					
				} else {
					
					efi.$preview.find('i').fadeOut(300, function() {
						$icon = $(icon).hide().fadeIn(300);
						$(this).replaceWith($icon);
					});
					
				}
				
			}, 'json');		
			
		},
		
		// Initialize the modal window by adding the info for old and new names and also define the required properties.
		init: function(e) {
		
			var 	efi = 		Automad.editFileInfo,
				file = 		$(this).data('automadFile'),
				caption =	$(this).data('automadCaption');
				
			// Define modal and parentForm after a user action (click) to make sure DOM is ready when looking for the modal and form elements.
			efi.$modal = $(efi.selectors.modal);
			efi.$parentForm = efi.$modal.closest('form');
			efi.$infoContainer = $(efi.selectors.infoContainer);	
			
			var	url =		efi.$modal.data('automadUrl'),
				loader =	'<div class="uk-block uk-text-muted uk-text-center uk-panel uk-panel-box uk-panel-box-secondary">' +
						'<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-medium"></i>' +
						'</div>';
			
			// Loading icon.	
			efi.$preview = $(loader);
			
			// Insert info items into the container.
			efi.$nameOld.appendTo(efi.$infoContainer).val(file);
			efi.$nameNew.appendTo(efi.$infoContainer).focus().val(file);
			efi.$preview.appendTo(efi.$infoContainer);
			efi.$caption.appendTo(efi.$infoContainer).val(caption).trigger('focusout');
			
			setTimeout(function(){
				efi.getPreview(file, url);
			}, 400);
			
			// Remove previously attached events.
			efi.$modal.off('hide.uk.modal.automad.editFileInfo');
			
			// Define event to destroy info on hide (Close button or esc key - without submission).
			efi.$modal.on('hide.uk.modal.automad.editFileInfo', efi.destroy);
					
		},
		
		// Since the modal window is nested within the actual file list form, it is very important to 
		// remove any input fields of the edit dialog before submitting the files form.
		destroy: function() {
			
			Automad.editFileInfo.$infoContainer.empty();
			
		},
		
		// The actual AJAX call to edit the current file.
		submit: function(e) {
			
			var	efi = 		Automad.editFileInfo,
				button = 	$(e.target),
				param =		{
							'url': efi.$modal.data('automadUrl'), // If URL is empty, the "/shared" files will be managed.
							'old-name': efi.$nameOld.val(),
							'new-name': efi.$nameNew.val(),
							'caption': efi.$caption.val()
						};
			
			// Temporary disable button to avoid submitting the form twice.
			button.prop('disabled', true);
			
			// Post form data to the handler.
			$.post('?ajax=edit_file_info', param, function(data) {
				
				// Re-enable button again after AJAX call.
				button.prop('disabled', false);
				
				if (data.error) {
					Automad.notify.error(data.error);
				} 
				
				// Wait for the modal to be hidden before refreshing the filelist, 
				// otherwise the modal class wouldn't be removed from the <html> element
				// and the UI will stay blocked.
				efi.$modal.on('hide.uk.modal.automad.editFileInfo', function() {
				
					// Remove info before refreshing the file list.
					efi.destroy();
					// Refresh file list (empty & submit).
					efi.$parentForm.empty().submit();
					
				});
				
				// Close modal.
				UIkit.modal(efi.selectors.modal).hide();
				
				
			}, 'json');	
			
		}
				
	};
	
	// Modal setup.
	$(document).on('click', 'a[href="' + Automad.editFileInfo.selectors.modal + '"]', Automad.editFileInfo.init); 
	
	// AJAX call.
	$(document).on('click', Automad.editFileInfo.selectors.button, Automad.editFileInfo.submit);
	
}(window.Automad = window.Automad || {}, jQuery);