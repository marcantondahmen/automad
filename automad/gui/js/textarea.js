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
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
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
		
		// Do not select textareas of HTML editors!
		// Since CodeMirror also creates textareas, it is not enough to just filter by ':not([data-uk-markdowneditor])'.
		// Those textareas created by CodeMirror don't have the '.uk-form-controls' class, so that class must be part of the
		// selector as well.
		selector: 'textarea.uk-form-controls:not([data-uk-markdowneditor]), textarea.cdx-input',
		
		handleTabs: function(e) {
		
			// Insert \t at caret when TAB is pressed, instead of jumping to the next textarea or button.
			if (e.keyCode === 9) { 
				
				e.preventDefault();
				e.stopPropagation();

				var start = this.selectionStart,
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
				$doc = $(document),
				triggerResize = function() {
					$(t.selector).trigger('update.automad.textarea');
				};
				
			// On keyup.
			$doc.on('keyup focus focusout update.automad.textarea', t.selector, t.resize);
			
			// Update also on drop, but with timeout.
			// The timeout is needed to make sure the dropped text gets recognized.
			$doc.on('drop cut paste', t.selector, function(e) {
				
				var	$ta = $(e.target);
				
				setTimeout(function(e) {
					$ta.trigger('update.automad.textarea');
				}, 50);
				
			})
			
			// Update also when AJAX completes.
			$doc.ajaxComplete(triggerResize);
			
			// Update also when doc is ready.
			$doc.on('ready', triggerResize);
			
			// Also trigger resizing on toggles to fix issues with hidden textareas.
			$doc.on('click', '.uk-accordion-title', function() {
				setTimeout(triggerResize, 50);
			});
				
			// Update also on resize.
			$(window).resize(triggerResize);	
				
			// Tabs
			$doc.on('keydown', t.selector, t.handleTabs);	
				
		},
		
		resize: function(e) {
		
			var	$ta = $(e.target),
				content = $ta.val().replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br />'),
				
				// The hidden clone will be used to determine the actual height.
				$clone = $('<div></div>')
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
						
			$ta.height($clone.height());
				
			$clone.remove();
			
		}
		
	};
	
	Automad.textarea.init();
			
}(window.Automad = window.Automad || {}, jQuery);