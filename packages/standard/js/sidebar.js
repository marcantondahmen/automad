/*!	
 * 	Standard.sidebar
 *	Copyright (c) 2020 by Marc Anton Dahmen - http://marcdahmen.de - MIT license
 */

+function(Standard, $) {

	Standard.sidebar = {

		init: function() {

			if ($('#sidebar').length) {

				new StickySidebar('#sidebar', {
					containerSelector: false,
					innerWrapperSelector: '.sidebar-wrapper',
					resizeSensor: true,
					topSpacing: 0,
					bottomSpacing: 70
				});
				
			}

		}

	}

	$(document).on('ready', Standard.sidebar.init);

}(window.Standard = window.Standard || {}, jQuery);