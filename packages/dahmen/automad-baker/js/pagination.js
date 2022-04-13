/*
 *	Baker
 *
 *	Copyright (c) 2017-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *  MIT license
 */


+function(baker, $) {
	
	baker.pagination = {
		
		init: function() {
			
			$('[data-uk-pagination]').on('select.uk.pagination', function(e, index) {
				e.preventDefault();
				baker.pagination.update(index);
			});	
			
		},
		
		update: function(index) {
			
			var url = window.location.href;
			
			index++;
			
			if (/[?&]page\s*=/.test(url)) {
				window.location.href = url.replace(/(?:([?&])page\s*=[^?&]*)/, "$1page=" + index);
			} else if (/\?/.test(url)) {
				window.location.href = url + "&page=" + index;
			} else {
				window.location.href = url + "?page=" + index;
			}

		}
		
	};
	
	$(document).on('ready', baker.pagination.init);

}(window.baker = window.baker || {}, jQuery);