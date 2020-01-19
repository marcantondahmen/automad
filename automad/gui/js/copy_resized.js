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
 *	Copyright (c) 2018-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 * 	Copy image resized.
 */

+function(Automad, $, UIkit) {
	
	Automad.copyResized = {
		
		dataAttr: {
			fileInfo: 'data-am-file-info',
			url: 'data-am-url'
		},
		
		selectors: {
			modal: '#am-copy-resized-modal',
			button:	'#am-copy-resized-submit',
			filename: '#am-copy-resized-filename',
			width: '#am-copy-resized-width',
			height: '#am-copy-resized-height',
			crop: '#am-copy-resized-crop'
		},
		
		destroy: function() {
			
			var	s = Automad.copyResized.selectors;
			
			$(s.filename).val('');
			$(s.width).val('');
			$(s.height).val('');
			
		},
		
		init: function() {
			
			var	cr = Automad.copyResized,
				da = cr.dataAttr,
				s = cr.selectors,
				$panel = $(this).closest('[' + da.fileInfo + ']'),
				info = $panel.data(Automad.util.dataCamelCase(da.fileInfo)),
				$modal = $(s.modal);
				
			$(s.filename).val(info.filename);
			$(s.width).val(info.img.originalWidth);
			$(s.height).val(info.img.originalHeight);
			
			// Events.
			// Remove previously attached events.
			$modal.off('hide.uk.modal.automad.copyResized');
			
			// Define event to destroy info on hide (Close button or esc key - without submission).
			$modal.on('hide.uk.modal.automad.copyResized', cr.destroy);
			
		},
		
		submit: function(e) {
			
			var	cr = Automad.copyResized,
				s = cr.selectors,
				$button = $(e.target),
				param =	{
						'url': $(s.modal).data(Automad.util.dataCamelCase(cr.dataAttr.url)), // If URL is empty, the "/shared" files will be managed.
						'filename': $(s.filename).val(),
						'width': $(s.width).val(),
						'height': $(s.height).val(),
						'crop': Number($(s.crop).is(':checked'))
					};
			
			// Temporary disable button to avoid submitting the form twice.
			$button.prop('disabled', true);
			
			// Post form data to the handler.
			$.post('?ajax=copy_resized', param, function(data) {
				
				// Re-enable button again after AJAX call.
				$button.prop('disabled', false);
				
				if (data.error) {
					Automad.notify.error(data.error);
				} 
				
				if (data.success) {
					Automad.notify.success(data.success);
				} 
				
				// Wait for the modal to be hidden before refreshing the filelist, 
				// otherwise the modal class wouldn't be removed from the <html> element
				// and the UI will stay blocked.
				$(s.modal).on('hide.uk.modal.automad.copyResized', function() {
				
					// Remove info before refreshing the file list.
					cr.destroy();
					// Refresh file list (empty & submit).
					$(s.modal).closest('form').empty().submit();
					
				});
				
				// Close modal.
				UIkit.modal(s.modal).hide();
				
			}, 'json');	
			
		}
		
	}
	
	// Modal setup.
	$(document).on('click', 'a[href="' + Automad.copyResized.selectors.modal + '"]', Automad.copyResized.init); 
	
	// Submit AJAX call.
	$(document).on('click', Automad.copyResized.selectors.button, Automad.copyResized.submit);
	
}(window.Automad = window.Automad || {}, jQuery, UIkit);