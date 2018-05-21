/*!
 *	Standard.modal
 * 	Copyright (c) 2018 by Marc Anton Dahmen - http://marcdahmen.de - MIT license
 */

+function(Standard, $, UIkit) {
	
	Standard.modal = {
		
		toggle: function(e) {
			
			var	target = $(this).data('modalToggle'),
				$modal = UIkit.modal(target);
				
			e.preventDefault();
				
			if ($modal.isActive()) {
				$modal.hide();
				$(this).removeClass('uk-active');
			} else {
				$modal.show();
				$(this).addClass('uk-active');
			}
				
		}
		
	};
	
	$(document).on('click', 'a[data-modal-toggle]', Standard.modal.toggle);

}(window.Standard = window.Standard || {}, jQuery, UIkit);