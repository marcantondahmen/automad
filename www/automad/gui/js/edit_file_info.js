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
 * 	Rename file modal.
 */

+function(Automad, $) {
	
	Automad.renameFile = {
		
		selectors: {
			modal: '#automad-rename-file-modal',
			button:	'#automad-rename-file-submit'
		},
		
		// Define modal and parentForm within inputs.init() to make sure the DOM is ready, when a user clicks a rename button.
		$modal: '',
		$parentForm: '',
		
		inputs: {
			
			$old: $('<input>', {
				'type': 'text', 
				'name': 'old-name', 
				'class': 'uk-form-controls uk-width-1-1'
			})
			.prop('disabled', true),
			
			$new: $('<input>', {
				'type': 'text', 
				'name': 'new-name', 
				'class': 'uk-form-controls uk-width-1-1 uk-margin-small-top'
			}),
			
			// Initialize the modal window by adding the inputs for old and new names and also define the required properties.
			init: function(e) {
			
				var 	rf = Automad.renameFile;
				
				// Define modal and parentForm after a user action (click) to make sure DOM is ready when looking for the modal and form elements.
				rf.$modal = $(rf.selectors.modal);
				rf.$parentForm = rf.$modal.closest('form');	
				
				// Set local vars.
				var	header = 	rf.$modal.find('.uk-modal-header'),
					file = 		$(e.target).data('automadFile');	
				
				// Insert inputs into the modal.
				rf.inputs.$old.insertAfter(header).val(file);
				rf.inputs.$new.insertAfter(rf.inputs.$old).focus().val(file);
				
				// Add submit on enter attribute.
				rf.inputs.$new.attr(Automad.form.dataAttr.enter, Automad.renameFile.selectors.button);
				
				// Remove previously attached events.
				rf.$modal.off('hide.uk.modal.automad.renameFile');
				
				// Define event to destroy inputs on hide (Close button or esc key - without submission).
				rf.$modal.on('hide.uk.modal.automad.renameFile', rf.inputs.destroy);
						
			},
			
			// Since the modal window is nested within the actual file list form, it is very important to 
			// remove any input fields of the rename dialog before submitting the files form.
			destroy: function() {
				
				Automad.renameFile.inputs.$old.remove();
				Automad.renameFile.inputs.$new.remove();
				
			},
			
			// The actual AJAX call to rename the current file.
			submit: function(e) {
				
				var	rf = 		Automad.renameFile,
					button = 	$(e.target),
					param =		{
								'url': rf.$modal.data('automadUrl'), // If URL is empty, the "/shared" files will be managed.
								'old-name': rf.inputs.$old.val(),
								'new-name': rf.inputs.$new.val()
							};
				
				// Temporary disable button to avoid submitting the form twice.
				button.prop('disabled', true);
				
				// Post form data to the handler.
				$.post('?ajax=rename_file', param, function(data) {
					
					// Re-enable button again after AJAX call.
					button.prop('disabled', false);
					
					if (data.error) {
						
						Automad.notify.error(data.error);
						
					} else {
						
						// Close modal.
						UIkit.modal(rf.selectors.modal).hide();
						
						// Wait for the modal to be hidden before refreshing the filelist, 
						// otherwise the modal class wouldn't be removed from the html element
						// and the UI will stay blocked.
						rf.$modal.on('hide.uk.modal.automad.renameFile', function() {
						
							// Remove inputs before refreshing the file list.
							rf.inputs.destroy();
							// Refresh file list (empty & submit).
							rf.$parentForm.empty().submit();
							
						});
							
					}
					
				}, 'json');	
				
			}
			
		}
			
	};
	
	// Modal setup.
	$(document).on('click', 'a[href="' + Automad.renameFile.selectors.modal + '"]', Automad.renameFile.inputs.init); 
	
	// AJAX call.
	$(document).on('click', Automad.renameFile.selectors.button, Automad.renameFile.inputs.submit);
	
}(window.Automad = window.Automad || {}, jQuery);