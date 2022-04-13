/*
 *	Baker
 *
 *	Copyright (c) 2018-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *  MIT license
 */


+function(baker, $) {
	
	baker.navbar = {
		
		init: function() {
			
			var	$navbar = $('.baker-navbar-nav'),
				$search = $('.baker-navbar-search input');
			
			// Change class of navbar when search field is focused or hovered.	
			$search
			.on('focus mouseenter', function() {
				$navbar.addClass('baker-focus');
			})
			.on('blur', function() {
				$navbar.removeClass('baker-focus');
			})
			.on('mouseleave', function() {
				if (!$search.is(':focus')) {
					$navbar.removeClass('baker-focus');
				}
			});
			
		}
		
	}
		
	$(document).on('ready', baker.navbar.init);

}(window.baker = window.baker || {}, jQuery);