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


/*
 *	Textarea auto-resizing and tab-handling. 
 */

+function(Automad, $) {
	
	Automad.textarea = {
		
		handleTabs: function(e) {
		
			// Insert \t at caret when TAB is pressed, instead of jumping to the next textarea or button.
			if (e.keyCode === 9) { 
				
				e.preventDefault();
				
				var 	start = this.selectionStart,
				 	end = this.selectionEnd,
					$ta = $(e.target),
					value = $ta.val();

				// Set textarea value to text before caret + tab + text after caret.
				$ta.val(value.substring(0, start) + "\t" + value.substring(end));

				// Put caret at right position again (add one for the tab).
				this.selectionStart = this.selectionEnd = start + 1;
			
			}
			
		},
		
		init: function() {
		
			var	t = Automad.textarea,
				$doc = $(document);
				
			// On keyup.
			$doc.on('keyup focus focusout update.automad.textarea', 'textarea', t.resize);
			
			// Update also on drop, but with timeout.
			// The timeout is needed to make sure the dropped text gets recognized.
			$doc.on('drop cut paste', 'textarea', function(e) {
				
				var	$ta = $(e.target);
				
				setTimeout(function(e) {
					$ta.trigger('update.automad.textarea');
				}, 50);
				
			})
			
			// Update also when AJAX completes.
			$doc.ajaxComplete(function() {
				$('textarea').trigger('update.automad.textarea');
			});
				
			// Update also on resize.
			$(window).resize(function() {
				$('textarea').trigger('update.automad.textarea');
			});
			
			// Tabs
			$doc.on('keydown', 'textarea', t.handleTabs);	
				
		},
		
		resize: function(e) {
		
			var	$ta = 		$(e.target),
				content =	$ta.val().replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br />'),
				
				// The spaceBottom is needed when textarea is focused to handle 'jumping' text caused by height transitions.
				// That extra space buffers the resize and the text doesn't jump when creating a new line.
				spaceBottom =	30,
				
				// The hidden clone will be used to determine the actual height.
				$clone =	$('<div></div>')
						.appendTo('body')
						.hide()
						.width($ta.width())
						.html(content + ' ')
						.css({
							'white-space': 'pre-wrap',
							'word-wrap': 'break-word',
							'overflow-wrap': 'break-word',
							'font-family': $ta.css('font-family'),
							'font-size': $ta.css('font-size'),
							'line-height': $ta.css('line-height'),
							'letter-spacing': $ta.css('letter-spacing')
						});
							
			
			// Remove bottom space on 'focusout' and the namespaced 'update' event.
			if (e.type == 'focusout' || e.type == 'update') {
				spaceBottom = 0;
			}
							
			$ta.height($clone.height() + spaceBottom);
				
			$clone.remove();
			
		}
		
	};
	
	Automad.textarea.init();
	
}(window.Automad = window.Automad || {}, jQuery);