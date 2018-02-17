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
 *	Copyright (c) 2017-2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	In page editing. 
 */
	
+function(Automad, $) {
	
	Automad.inPage = {
		
		selectors: {
			modal: '#am-inpage-edit-modal',
			title: '#am-inpage-edit-modal-title',
			fields: '#am-inpage-edit-fields',
			menubar: '.am-inpage-menubar',
			dragHandle: '.am-drag-handle'
		},
		
		dataAttr: {
			content: 'data-am-inpage-content',
			handler: 'data-am-inpage-handler',
			url: 'data-am-inpage-url'
		},
		
		modal: {
			
			init: function() {
				
				var	$button = $(this),
					ip = Automad.inPage,
					u = Automad.util,
					param = $button.data(u.dataCamelCase(ip.dataAttr.content)),
					$modal = $(ip.selectors.modal),
					$title = $(ip.selectors.title),
					$form = $modal.find('form'),
					handler = $form.data(u.dataCamelCase(ip.dataAttr.handler)),
					$loader = $('<i></i>', { 'class': 'uk-icon-circle-o-notch uk-icon-spin uk-icon-small' })
						  	  .insertAfter($title.parent()),
					$footer = $form.find('.uk-modal-footer');
					
				// Remove possible title and inputs from previous call.
				$title.empty();
				$(ip.selectors.fields).remove();
				
				// Get form content.
				$.post(handler, param, function(data) {
							
					if (data.html) {
						
						var $fields = $(data.html).insertBefore($footer).hide(),
							context = $fields.find('[name="context"]').val();
						
						// Set context in modal title.
						$title.text(context);
						
						// Delay resizing to avoid flicker.
						setTimeout(function() {
							$(window).resize();
						}, 400);
						
						// Delay fade in to avoid flicker.
						setTimeout(function() {
							$loader.remove();
							$fields.fadeIn(300, function() {
								$(window).resize();
								$fields.find('.uk-form-controls, textarea').focus();	
							});
						}, 600);
							
					}
								
				}, 'json');
					
			},
			
			submit: function(e) {
				
				e.preventDefault();
					
				var	$form = $(e.target),
					handler = $form.data(Automad.util.dataCamelCase(Automad.inPage.dataAttr.handler)),
					param =	$form.serializeArray();
				
				$.post(handler, param, function(data) {
					
					if (data.debug) {
						Automad.debug.log(handler, data.debug);
					}
					
					if (data.redirect) {
						window.location.href = data.redirect;
					}
						
				}, 'json');
					
			}
			
		},
		
		menubar: {
			
			init: function() {
				
				var ips = Automad.inPage.selectors,
					$menubar = $(ips.menubar).draggabilly({
							handle: ips.dragHandle
					});
				
			}
				
		}
		
	}
	
	$(document).on('click', '[href="' + Automad.inPage.selectors.modal + '"]', Automad.inPage.modal.init);
	$(document).on('submit', '[' + Automad.inPage.dataAttr.handler + ']', Automad.inPage.modal.submit);
	$(document).on('ready', Automad.inPage.menubar.init);
	
}(window.Automad = window.Automad || {}, jQuery);