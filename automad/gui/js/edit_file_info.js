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
 * 	File information modal.
 */

+function(Automad, $, UIkit) {
	
	Automad.editFileInfo = {
		
		dataAttr: {
			fileInfo: 'data-am-file-info',
			url: 'data-am-url',
			ext: 'data-am-extension'
		},
		
		selectors: {
			modal: '#am-edit-file-info-modal',
			button:	'#am-edit-file-info-submit',
			img: '#am-edit-file-info-img',
			icon: '#am-edit-file-info-icon',
			oldName: '#am-edit-file-info-old-name',
			newName: '#am-edit-file-info-new-name',
			caption: '#am-edit-file-info-caption',
			download: '#am-edit-file-info-download'
		},
		
		// Since the modal window is nested within the actual file list form, it is very important to 
		// reset all input fields of the edit dialog before submitting the files form.
		destroy: function() {
			
			var	efi = Automad.editFileInfo,
				da = efi.dataAttr,
				s = efi.selectors;
			
			$(s.img).attr('src', '')
					.removeAttr('width')
					.removeAttr('height');
			$(s.icon).attr(da.ext, '').html('');
			$(s.download).attr('href', '');
			$(s.oldName).val('');
			$(s.newName).val('');
			$(s.caption).val('').trigger('keyup');	// Trigger keyup to reset textarea resizing.
			
		},
		
		// Initialize the modal window by adding the info for old and new names and also define the required properties.
		init: function() {
			
			var	efi = Automad.editFileInfo,
				u = Automad.util,
				da = efi.dataAttr,
				s = efi.selectors,
				$panel = $(this).closest('[' + da.fileInfo + ']'),
				info = $panel.data(u.dataCamelCase(da.fileInfo)),
				$modal = $(s.modal);
				
			// Only set extension if no image exists. The icon is only used as fallback.
			// It willl be hidden by CSS when the extension attribute will stay empty.
			if (info.img) {
				$(s.img)
				.attr('src', info.img.src)
				.attr('width', info.img.width)
				.attr('height', info.img.height);
			} else {
				$(s.icon)
				.attr(da.ext, info.extension)
				.append($('<i></i>', { class: 'uk-icon-file-o am-files-icon-' + info.extension }));
			}
		
			$(s.oldName).val(info.filename);
			$(s.newName).val(info.filename);
			$(s.caption).val(info.caption).trigger('keyup');	
			$(s.download).attr('href', info.download);
			
			// Events.
			// Remove previously attached events.
			$modal.off('hide.uk.modal.automad.editFileInfo');
			
			// Define event to destroy info on hide (Close button or esc key - without submission).
			$modal.on('hide.uk.modal.automad.editFileInfo', efi.destroy);
			
		},
		
		// The actual AJAX call to edit the current file.
		submit: function(e) {
			
			var	efi = Automad.editFileInfo,
				s = efi.selectors,
				$button = $(e.target),
				param =	{
						'url': $(s.modal).data(Automad.util.dataCamelCase(efi.dataAttr.url)), // If URL is empty, the "/shared" files will be managed.
						'old-name': $(s.oldName).val(),
						'new-name': $(s.newName).val(),
						'caption': $(s.caption).val()
					};
			
			// Temporary disable button to avoid submitting the form twice.
			$button.prop('disabled', true);
			
			// Post form data to the handler.
			$.post('?ajax=edit_file_info', param, function(data) {
				
				// Re-enable button again after AJAX call.
				$button.prop('disabled', false);
				
				if (data.error) {
					Automad.notify.error(data.error);
				} 
				
				// Wait for the modal to be hidden before refreshing the filelist, 
				// otherwise the modal class wouldn't be removed from the <html> element
				// and the UI will stay blocked.
				$(s.modal).on('hide.uk.modal.automad.editFileInfo', function() {
				
					// Remove info before refreshing the file list.
					efi.destroy();
					// Refresh file list (empty & submit).
					$(s.modal).closest('form').empty().submit();
					
				});
				
				// Close modal.
				UIkit.modal(s.modal).hide();
				
			}, 'json');	
			
		}
		
	};
		
				
	// Modal setup.
	$(document).on('click', 'a[href="' + Automad.editFileInfo.selectors.modal + '"]', Automad.editFileInfo.init); 
		
	// Submit AJAX call.
	$(document).on('click', Automad.editFileInfo.selectors.button, Automad.editFileInfo.submit);	
		
	
}(window.Automad = window.Automad || {}, jQuery, UIkit);