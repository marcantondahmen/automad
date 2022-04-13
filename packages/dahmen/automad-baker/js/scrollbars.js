/*
 *	Baker
 *
 *	Copyright (c) 2018-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *  MIT license
 */


+function(baker, $) {
	
	baker.scrollbars = {
		
		init: function() {
			
			var $sidebar = $('#baker-sidebar .baker-sidebar-scroll');
			
			// Apply scrollbar plugin.
			$sidebar.mCustomScrollbar({
				scrollbarPosition: 'inside',
				theme: 'minimal-dark',
				autoHideScrollbar: true,
				scrollInertia: 50,
				scrollButtons: { 
					enable: false 
				}
			});
			
			// Scroll to active item initially.
			setTimeout(function() {
				
				var $active = $sidebar.find('.uk-active');
				
				if ($active.length) {
					if (($active.offset().top + 180) > $(window).height()) {
						$sidebar.mCustomScrollbar('scrollTo', function() {
							return $active.offset().top - $(window).height() + 180;
						}, { scrollInertia: 200 });
					}
				} 
				
			}, 150);
			
		}
		
	}
		
	$(document).on('ready', baker.scrollbars.init);

}(window.baker = window.baker || {}, jQuery);