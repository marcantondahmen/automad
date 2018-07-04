/*!
 *	Standard.modal
 * 	Copyright (c) 2018 by Marc Anton Dahmen - http://marcdahmen.de - MIT license
 */

+function(Standard, $, UIkit) {
	
	Standard.modal = {
		
		init: function(e) {
			
			$('.uk-modal').on({
				'hide.uk.modal': function(){
					$('[data-modal-toggle]').removeClass('uk-active');
				}
			})
			
			// Prepend a hidden <a> tag to catch the focus on show.uk.modal event
			// and avoid focusing the search field.
			$('.uk-modal-dialog').prepend($('<a href="#"></a>').hide());
			
		},
		
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
	
	$(document).on('ready', Standard.modal.init);
	$(document).on('click', 'a[data-modal-toggle]', Standard.modal.toggle);

}(window.Standard = window.Standard || {}, jQuery, UIkit);