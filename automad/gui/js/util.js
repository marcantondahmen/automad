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
 *	Copyright (c) 2016-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Helper tools. 
 */

+function(Automad, $) {
	
	Automad.util = {
		
		create: {

			element: function(tag, cls) {

				var element = document.createElement(tag);

				for (var i = 0; i < cls.length; i++) {
					element.classList.add(cls[i]);
				}

				return element;

			},

			editable: function(cls, placeholder, value) {

				var div = Automad.util.create.element('div', cls);

				div.contentEditable = true;
				div.dataset.placeholder = placeholder;
				div.innerHTML = value;

				return div;

			},

			label: function(text) {

				var label = Automad.util.create.element('label', ['am-block-label']);

				label.textContent = text;

				return label;

			}

		},

		// Convert data attribute string in dataAPI string. 
		// For example "data-am-handler" gets converted into "amHandler".
		dataCamelCase: function(str) {
			
			str = str.replace(/data-/g, '');
			str = str.replace(/\-[a-z]/g, function(s) {
				return s.charAt(1).toUpperCase();
			});
			
			return str;
			
		},
		
		// Format bytes.
		formatBytes: function(bytes) {
		
			if (typeof bytes !== 'number') {
				return '';
			}

			if (bytes >= 1000000000) {
				return (bytes / 1000000000).toFixed(2) + ' gb';
			}

			if (bytes >= 1000000) {
				return (bytes / 1000000).toFixed(2) + ' mb';
			}

			return (bytes / 1000).toFixed(2) + ' kb';
				
		},

		resolvePath: function(path) {

			var pagePath = $('[data-am-path]').data('amPath');

			if (pagePath === undefined) {
				pagePath = '';
			}

			if (path.includes('://')) {
				return path;
			}

			if (path.startsWith('/')) {
				return '.' + path;
			} else {
				return 'pages' + pagePath + path;
			}

		},

		resolveUrl: function(url) {

			var pageUrl = $('[data-am-url]').data('amUrl');

			if (pageUrl === undefined) {
				pageUrl = '';
			}

			if (url.includes('://')) {
				return url;
			}

			if (url.startsWith('/')) {
				return '.' + url;
			} else {
				return '.' + pageUrl + '/' + url;
			}

		},

		triggerBlockChange(blockContent) {

			var temp = document.createElement('div');

			// Trigger a fake block changed event by adding and removing a temporary div.
			blockContent.appendChild(temp);
			blockContent.removeChild(temp);

		}
		
	}
		
}(window.Automad = window.Automad || {}, jQuery);