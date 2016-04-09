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


// Textareas: Auto Resize

$(document).on('keyup', 'textarea:visible', function() {
	
	var	ta = 		$(this),
		content =	ta.val().replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br />'),
		
		// The hidden clone will be used to determine the actual height.
		clone =		$('<div></div>')
				.appendTo('body')
				.hide()
				.width(ta.width())
				.html(content + ' ')
				.css({
					'white-space': 'pre-wrap',
					'word-wrap': 'break-word',
					'overflow-wrap': 'break-word',
					'font-family': ta.css('font-family'),
					'font-size': ta.css('font-size'),
					'line-height': ta.css('line-height'),
					'letter-spacing': ta.css('letter-spacing')
				});
					
	ta.height(clone.height());
		
	clone.remove();
	
});

// Update also on drop, but with timeout.
// The timeout is needed to make sure the dropped text gets recognized.
$(document).on('drop', 'textarea:visible', function() {
	setTimeout(function() {
		$('textarea').trigger('keyup');
	}, 50);
})

// Update also when AJAX completes.
$(document).ajaxComplete(function() {
	$('textarea').trigger('keyup');
});

// Update also on resize.
$(window).resize(function() {
	$('textarea').trigger('keyup');
});




// Textareas: Tabs

$(document).on('keydown', 'textarea', function(e) {
	
	// Insert \t at caret when TAB is pressed, instead of jumping to the next textarea or button.
	if (e.keyCode === 9) { 
		
		var 	start = this.selectionStart,
		 	end = this.selectionEnd,
			value = $(this).val();

		// Set textarea value to text before caret + tab + text after caret.
		$(this).val(value.substring(0, start) + "\t" + value.substring(end));

		// Put caret at right position again (add one for the tab).
		this.selectionStart = this.selectionEnd = start + 1;

		// Prevent the focus lose.
		e.preventDefault();
	}
	
});

