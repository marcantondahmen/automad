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
					innerWrapperSelector: '.sidebar-inner',
					resizeSensor: true,
					topSpacing: 30,
					bottomSpacing: 70
				});
				
			}

		}

	}

	$(document).on('ready', Standard.sidebar.init);

}(window.Standard = window.Standard || {}, jQuery);