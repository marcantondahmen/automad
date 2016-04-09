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
 *	Copyright (c) 2014-2016 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


// System Status

function getStatus() {
	
	$('.automad-status').each(function() {
	
		// To get the status of any config setting (constant),
		// the container has to have the class '.automad-status'.
		// The requested item for each container is passed via 
		// 'data-automad-status="item"'. 
		
		var 	container = $(this),
			item = container.data('automadStatus');
		
		$.post('?ajax=status', {"item": item}, function(data) {
			container.html(data.status);		
		}, 'json');
		
	});
	
}

$(document).ready(function() {
	getStatus();
});

$(document).ajaxComplete(function(e, xhr, settings) {
	// Make sure the status doesn't get triggert by itself in an infinite loop.
	if (settings.url != '?ajax=status') {
		getStatus();
	}
});	