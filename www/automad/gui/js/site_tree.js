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


// Site Tree: Collapse/Expand

$(document).ready(function() {
	
	var tree = $('.column.nav .pages');
	
	tree.find('li li').has('ul').each(function() {
	
		var 	node = 		$(this),
			children = 	node.children('ul'),
			button = 	$('<a href="#" class="expand"><span class="glyphicon"></span></a>').prependTo(node);
			
		// Collapse the tree only for the non-active pages/path.
		if (node.hasClass('active') || node.find('.active').length > 0) {
			button.children('.glyphicon').addClass('glyphicon-minus-sign');
		} else {
			button.children('.glyphicon').addClass('glyphicon-plus-sign');
			children.hide();
		}
		
		// Toggle class and visibility.
		button.click(function(e) {
			e.preventDefault();
			children.toggle();
			button.children('.glyphicon').toggleClass('glyphicon-plus-sign glyphicon-minus-sign');
		});
		
	});
	
});
