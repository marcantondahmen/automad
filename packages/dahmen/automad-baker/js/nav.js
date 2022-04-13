/*
 *	Baker
 *
 *	Copyright (c) 2017-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *  MIT license
 */


+function(baker, $) {
	
	baker.nav = {
		
		init: function() {
			
			var $doc = $(document),
				selectorNav = '#baker-nav > ul';
			
			// Clone nav into sidebars.
			$doc.ready(function(){
				$(selectorNav)
				// First move the nav to the sidebar modal before cloning, 
				// since the modal exists on every page, also on the home page,
				// where the sidebar doesn't exist.
				.appendTo('#baker-sidebar-modal [data-baker-nav]')
				.clone()
				.appendTo('#baker-sidebar [data-baker-nav]');
				// Clean up.
				$('#baker-nav').remove();
			});
			
			// Get navigation via AJAX.
			$doc.on('click', '[data-baker-nav-target]', function(e) {
				
				var	$arrow = $(this),
					$container = $arrow.closest('[data-baker-nav]'),
					current = $container.data('bakerNav'),
					regex = new RegExp(current + "(#[^#]*)?$", 'i'),
					baseUrl = window.location.pathname.replace(regex, ''),
					target = $(this).data('bakerNavTarget');
					
				e.preventDefault();
				
				// Replace arrow button with loading animation on slow connections.
				setTimeout(function() {
					$arrow.replaceWith(
						$(
							'<span></span>', 
							{ 'class': 'baker-nav-loading' }
						)
						.append(
							$(
								'<i></i>', 
								{ 'class': 'uk-icon-circle-o-notch uk-icon-spin' }
							)
						)
					);
				}, 200);
				
				$container.load(baseUrl + target + " " + selectorNav, function() {
					// Set class for active element.
					$('.baker-nav .uk-active').removeClass('uk-active');
					$('[href="' + window.location.pathname + '"]').addClass('uk-active');
					// Set class for active top level element (:currentPath).
					$('.baker-nav-top .baker-nav-top-active')
					.removeClass('baker-nav-top-active');
					$('.baker-nav-top [href="' + baseUrl + target.match(/^\/[\w\-]+/)[0] + '"]')
					.not('.uk-active')
					.addClass('baker-nav-top-active');
				});
				
			});
			
		}
		
	};
	
	baker.nav.init();

}(window.baker = window.baker || {}, jQuery);