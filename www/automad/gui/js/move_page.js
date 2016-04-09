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


// Button: Move Page (Select Destination from Tree) 

$(document).on('click', '#automad-move-page-modal a', function() {
	
	var	modal =		$('#automad-move-page-modal'),
		modalBody =	modal.find('.modal-body'),
		
		// Get the URL and title from the current page.
		url =		modal.data('automadUrl'),
		title =		modal.data('automadTitle'),
		
		// Get the destination from the clicked link within the tree.
		destination =	decodeURIComponent($(this).attr('href').split('=')[1]);
		
	// Post request.
	$.post('?ajax=move_page', {url: url, title: title, destination: destination}, function(data) {
		
		// Redirect on success to updated page.
		if (data.redirect) {
			window.location.href = data.redirect;
		}
		
		// Error message.
		if (data.error) {
			$('<div class="alert alert-danger alert-dismissable fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + data.error + '</div>').prependTo(modalBody);
		}
		
	}, 'json');
	
	return false;
	
});