/*!
 * 	Standard.sidebar
 * Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de - MIT license
 */

+(function (Standard, $) {
	Standard.sidebar = {
		init: function () {
			if ($('#sidebar').length) {
				new StickySidebar('#sidebar', {
					containerSelector: false,
					innerWrapperSelector: '.sidebar-inner',
					resizeSensor: true,
					topSpacing: 100,
					bottomSpacing: 70,
				});
			}
		},
	};

	$(document).on('ready', Standard.sidebar.init);
})((window.Standard = window.Standard || {}), jQuery);
