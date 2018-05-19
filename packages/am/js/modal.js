/*!
 *	am.modal
 * 	Copyright (c) 2018 by Marc Anton Dahmen - http://marcdahmen.de - MIT license
 */

+function(am, $, UIkit) {
	
	am.modal = {
		
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
	
	$(document).on('click', 'a[data-modal-toggle]', am.modal.toggle);

}(window.am = window.am || {}, jQuery, UIkit);