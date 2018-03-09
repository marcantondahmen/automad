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
 *	Copyright (c) 2016-2018 by Marc Anton Dahmen
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
				
		}
		
	}
		
}(window.Automad = window.Automad || {}, jQuery);