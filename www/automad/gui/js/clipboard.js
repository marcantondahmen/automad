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
 *	Copyright (c) 2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Copy string to clipboard. 
 */
	
+function(Automad, $) {
	
	Automad.clipboard = {
		
		dataAttr: 'data-am-clipboard',
		
		copy: function(e) {
			
			e.preventDefault();
			
			var	text = $(this).data(Automad.util.dataCamelCase(Automad.clipboard.dataAttr)),
				$input = 	$('<input type="text" />')
							.css({
								position: 'fixed',
								top: -1000
							})
							.appendTo($('body'))
							.val(text),
				success = false;
			
			$input.select();
			success = document.execCommand('copy');
			$input.remove();
			
			if (success) {
				Automad.notify.success(text);
			}
			
		}
		
	}
	
	$(document).on('click', '[' + Automad.clipboard.dataAttr + ']', Automad.clipboard.copy);
		
}(window.Automad = window.Automad || {}, jQuery);