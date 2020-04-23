/*!
 *	Standard.modal
 * 	Copyright (c) 2018-2020 by Marc Anton Dahmen - http://marcdahmen.de - MIT license
 */

+function(Standard, $, UIkit) {
	
	Standard.modal = {
		
		init: function(e) {
			
			$('.uk-modal').on({
				'hide.uk.modal': function(){
					$('[data-modal-toggle]').removeClass('uk-active');
				}
			})
			
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