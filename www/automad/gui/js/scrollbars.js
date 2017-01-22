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
 *	Copyright (c) 2014-2017 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Inistialize Scrollbars plugin and watch scrolled elements (including the window). 
 */

+function(Automad, $) {
	
	Automad.scrollbars = {
		
		$scrolledElements: $(),
		
		scrolledClass: 'am-scrolled',
		
		defaults: { 
				'scrollToItem': false					
		},
		
		dataAttr: 'data-am-scroll-box',
		
		init: function() {
			
			var	s = Automad.scrollbars,
				u = Automad.util;
			
			$('[' + s.dataAttr + ']').each(function() {
			
				var	$box = $(this),
					options = $box.data(u.dataCamelCase(s.dataAttr)),
					settings =  $.extend({}, s.defaults, options);
				
				// Apply scrollbar plugin.
				$box.mCustomScrollbar({
					scrollbarPosition: 'inside',
					theme: 'minimal-dark',
					autoHideScrollbar: true,
					scrollInertia: 50,
					scrollButtons: { 
						enable: false 
					},
					callbacks: {
						onScroll: function() {
							$box.trigger('scrolled.automad');
						},
						onUpdate: function() {
							$box.trigger('scrolled.automad');
						}
					}
				});
				
				// If "scrollToItem" is set, initially scroll to that item.
				if (settings.scrollToItem) {
					
					var 	$item = $box.find(settings.scrollToItem);
					
					if ($item.length) {
						
						setTimeout(function() {
							if (($item.offset().top + 180) > $(window).height()) {
								$box.mCustomScrollbar('scrollTo', function() {
									return $item.offset().top - $(window).height() + 180;
								}, { scrollInertia: 200 });
							}
						}, 150);
						
					} 
						
				}
					
			});
			
			// Create event to be triggered only once when scrolling has finished.
			$(window).scroll(function() {
				
				if (this.scrolling) {
					clearTimeout(this.scrolling);
				}
				
				this.scrolling = setTimeout(function() {
					$(this).trigger('scrolled.automad');	
				}, 50);
		
			}); 
			
			// Handle scrolled event.
			$(window).on('load scrolled.automad', s.scrolled);
			
		},
		
		scrolled: function(e) {
			
			// If any element is scrolled more that a given limit (for example 20px), 
			// the 'am-scrolled' class gets added to the <html> element.
			
			var 	s = Automad.scrollbars,
				$elem = $(e.target),
				top = 0;
			
			// Check if element is a custom scrollbars container or $('window').
			if (typeof($elem[0].mcs) != 'undefined') {
				
				// Custom scrollbars.
				// Skip elements with a height smaller 100px, since they are probably within a hidden offcanvas bar.
				if ($elem.height() > 100) {
					top = Math.abs($elem[0].mcs.top);
				}
				
			} else {
				
				// Window.
				
				// When the sidebar (offcanvas bar) is visible and the main <html> element is fixed with a negative margin.
				// That value can be used as default. It will be 0 when the sidebar is hidden.
				// If top is 0 (or the sidebar is hidden), the scrollTop() value will be used instead.
				top = Math.abs(parseInt($('html').css('margin-top')));
				
				if (top == 0) {
					top = $elem.scrollTop();
				}
				
			}
			
			// If the top value of the current element is larger than 20px, it will be added to the list of currently scrolled elements.
			// If not, it will be removed.
			if (top > 20) {
				s.$scrolledElements = s.$scrolledElements.add($elem);
			} else {
				s.$scrolledElements = s.$scrolledElements.not($elem);
			}
			
			// If the list of scrolled elements is not empty, the 'am-scrolled' class will be added to the <html> element.
			if (s.$scrolledElements.length > 0) {
				$('html').addClass(s.scrolledClass);
			} else {
				$('html').removeClass(s.scrolledClass);
			}
			
		}
		
	};
	
	$(document).ready(Automad.scrollbars.init);
		
}(window.Automad = window.Automad || {}, jQuery);