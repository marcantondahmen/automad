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


// ScrollBars

$(document).ready(function() {
	
	$('.scroll').mCustomScrollbar({
		scrollbarPosition: 'outside',
		theme: 'minimal-dark',
		autoHideScrollbar: true,
		scrollInertia: 300,
		scrollButtons: { 
			enable: false 
		}
	});
	
	setTimeout(function() {
		
		var activePage = $('.column.nav li.active');
		
		if ((activePage.offset().top + 80) > $(window).height()) {
			
			$('.column.nav .scroll').mCustomScrollbar('scrollTo', function() {
				return activePage.offset().top - $(window).height() + 80;
			}, { scrollInertia: 150 });
			
		}
		
	}, 100);
		
});