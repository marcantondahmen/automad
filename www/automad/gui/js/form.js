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
 *	Handling forms. 
 */

+function(Automad, $) {

	Automad.form = {
		
		unsavedClassPrefix: 'automad-unsaved-',
		
		dataAttr: {
			
			/*
			 *	Important attributes to handle forms.
			 *
			 *
			 * 	FORM ATTRIBUTES:
			 *	
			 * 	data-automad-handler="handler"		Generally, all forms with a [data-automad-handler] attribute will be sumbitted to their AJAX handler, 
			 *						specified in "data-automad-handler".
			 *						For example: 
			 * 						"<form data-automad-handler="page_data"></form>"
			 *						will submit the form to "?ajax=page_data"
			 *
			 *      					Note that a page can only (!) have once a handler with the same name.
			 *      					Having multiple forms with the same handler confuses button and watch states.
			 *
			 * 	data-automad-url="page"			To notify the AJAX handler, that the request belongs to a certain page, the URL has to be 
			 *						included in the request.
			 *						Therefore the data attribute "data-automad-url" must be added to the form tag. 
			 *						
			 *	data-automad-init			Automatically submit form when a page gets loaded.
			 *
			 * 	data-automad-auto-submit		Automatically submit form on changes. Must be added to a button.
			 *
			 * 	data-automad-close-on-success="#form"	Closes a modal window with the given ID on success.
			 *
			 * 	data-automad-confirm="Text..."		Confirm submission
			 *
			 * 
			 * 	INPUT ATTRIBUTES:
			 *
			 *	data-automad-enter="#button"		Trigger click event on pressing the enter key. Must be added to an input field.
			 *
			 * 	data-automad-default="..."		Set a default value for an input to be used if the field gets cleared by the user.
			 *
			 *
			 * 	BUTTON ATTRIBUTES:
			 *
			 *	data-automad-submit="handler"		A button or link with that attribute will be used as submit button for a form having a
			 * 						"data-automad-handler" attribute set to the given handler value.
			 * 						Note that those buttons automatically get disabled on load and re-enable on changes.					
			 */
			
			handler: 	'data-automad-handler',
			url:		'data-automad-url',
			submit:		'data-automad-submit',
			init:		'data-automad-init',
			autoSubmit:	'data-automad-auto-submit',
			close:		'data-automad-close-on-success',
			confirm:	'data-automad-confirm',
			enter:		'data-automad-enter',
			defaultValue:	'data-automad-default'
			
		},
		
		// Post form.
		ajaxPost: function(e) {
			
			/*
			 *	Generally, all forms with a [data-automad-handler] attribute will be sumbitted to their AJAX handler,
			 * 	specified in "data-automad-handler".
			 *
			 * 	For example: 
			 *  	"<form data-automad-handler="page_data"></form>"
			 *   	will submit the form to "?ajax=page_data"
			 *
			 * 	Server Data:
			 *  	The function expects the data from the server to be in JSON format.
			 *  	
			 *   	1.	data.redirect
			 *   		will redirect the page to the given URL.
			 *
			 * 	2.	data.html
			 * 		if any string in data.html gets returned from the server, 
			 * 		the form's (inner) HTML will be replaced.
			 * 		
			 *   	3.	data.error
			 *   		will alert the error message in a notification box.
			 *
			 * 	4.	data.success
			 * 		will alert the success message in a notification box.
			 *
			 *      5.	data.debug
			 *      	Outputs debug info to the console.
			 */
			
			var	f = 		Automad.form,
				da = 		f.dataAttr,
				$form = 	$(e.target),
				
				// Action
				handler = 	$form.data(Automad.util.dataCamelCase(f.dataAttr.handler)),
				
				// Optional URL parameter.
				// Only needed, to identify a page, in case the form relates to a certain page (edit_page.php).
				// Can be omitted for general form actions.
				url =		$form.data(Automad.util.dataCamelCase(f.dataAttr.url));
					
			// Handle default values.
			$form.find('[' + da.defaultValue + ']').each(function(){
				
				var	$input = $(this);
				
				if (!$input.val()) {
					$input.val($input.data(Automad.util.dataCamelCase(da.defaultValue)));
				}
				
			});
				
			// Get parameters.	
			var	param =		$form.serializeArray();
			
			// Add URL to parameters, if existing.	
			if (url) {
				param.push({name: 'url', value: url});
			}	
			
			// Post form data to the handler.
			$.post('?ajax=' + handler, param, function(data) {
			
				// Debug info.
				if (data.debug) {
					console.log(data.debug);
				}
				
				// In case the returned JSON contains a redirect URL, simply redirect the page.
				// A redirect might be needed, in case other elements on the page, like the navigation, have to be updated as well.
				if (data.redirect) {
					window.location.href = data.redirect;
				}

				// If HTML gets returned within the JSON data, replace the form's (inner) HTML.
				if (data.html) {
					$form.html(data.html);
				}
				
				// Display error, if existing.
				if (data.error) {
					Automad.notify.error(data.error);
				}
				
				// Display success, if existing.
				if (data.success) {
					Automad.notify.success(data.success);
				}
				
				// If the request returns no error, optionally close wrapping modal.
				// Note that this can be done with adding 'data-automad-close-on-success="#modal"' attribute to a form.
				if (!data.error) {
							
					// Close wrapping modal if form has 'data-automad-close-on-success="#modal"' attribute. 
					var modalSelector = $form.data(Automad.util.dataCamelCase(f.dataAttr.close));
					
					if (modalSelector) {
						UIkit.modal(modalSelector).hide();
					}
					
				}
				
			}, 'json');
				
		},
		
		// Confirm submission.
		confirm: function(e) {
			
			e.preventDefault();
				
			var	f = 			Automad.form,
				$form = 		$(e.target),
				confirmMessage =	$form.data(Automad.util.dataCamelCase(f.dataAttr.confirm));
			
			// If confirmation is required (confirmMessage is not empty) 
			// and the form is not empty (avoid confirm box for data-automad-init forms).
			if (confirmMessage && $form.find('input').length > 0) {
				
				// Wait for confirmation.	
				UIkit.modal.confirm(confirmMessage, function(){
					f.ajaxPost(e);
				});
				
			} else {
				
				// No confirmation required.
				f.ajaxPost(e);
				
			}
				
		},
		
		// Init form events.
		init: function() {
			
			
			var	$doc = $(document),
				f = Automad.form,
				da = f.dataAttr;
			
			
			// Submitting forms.
			
			// Handle AJAX post on submit event.
		 	$doc.on('submit', '[' + da.handler + ']', f.confirm);
			
			// Submit forms when clicking buttons (possibly outside the form) having "data-automad-submit" attribute.
			$doc.on('click', '[' + da.submit + ']', function(e) {
				
				var	handler = $(this).data(Automad.util.dataCamelCase(da.submit));
				
				e.preventDefault();
				$('form[' + da.handler + '="' + handler + '"]').submit();
				
			});
			
			// Submit forms with a data-automad-init attribute automatically on load.
			$doc.ready(function() {

				// All forms with attribute [data-automad-init] get submitted when page is ready to get initial content via AJAX.
			 	$('[' + Automad.form.dataAttr.init + ']').trigger('submit');

			});
			
			
			// Handle modal events.
			$doc.on('ready ajaxComplete', f.modalEvents);
			
			
			// Handle enter key.
			
			// Disable form submission on pressing enter in input field in general, if form has 'data-automad-handler' attribute.
			// Prevent accidental submission of parent form when pressing enter in nested and injected input fields 
			// such as the file rename dialog.
			$doc.on('keydown', '[' + da.handler + '] input', function(e) {
				
				if (e.which == 13) {
					e.preventDefault();
				}
				
			});
			
			// Explictly enable submission of form when pressing enter key in input with 'data-automad-enter' attribute
			// by triggering a click event on the given button id.
			$doc.on('keydown', '[' + da.enter + ']', function(e) {
				
				if (e.which == 13) {
					
					var	button = $(e.target).data(Automad.util.dataCamelCase(da.enter));
					
					$(button).click();
					
				}
				
			});
			
			
			// Watch changes - handle button status and prevent leaving page with unsaved changes.
			
			// Automatically submit forms with attribute data-automad-auto-submit on changes.
			$doc.on('change drop', '[' + da.autoSubmit + '] input, [' + da.autoSubmit + '] textarea', function(e) {
				$(e.target).closest('form').submit();
			});
			
			// Disable all submit buttons with a data-automad-submit attribute initially when ajax completes or the document is ready.
			// Note that only buttons get disabled. If in some cases it is not wanted to disable a <button> (for example "Delete Page" button),
			// a normal <a> tag can be used instead to submt the form. The <a> link will never be disabled.
			$doc.on('ready', function() {
				$('button[' + da.submit + ']').prop('disabled', true);
			});
			$doc.on('ajaxComplete', function(e, xhr, settings) {
				
				// On an 'ajaxComplete' event it is important to actually only disabled the related submit button and
				// not just all on the current page. Otherwise a file upload would disable the save button of the page
				// data section as well. Therefore the actual ajax request has to be analysed.
				var	handler = settings.url.replace('?ajax=', '');
				
				$('button[' + da.submit + '="' + handler + '"]').prop('disabled', true);
				
			});
			
			// Add 'automad-unsaved-{handler}' class to the <html> element on form changes
			// and re-enable related (only!) submit buttons after touching any form element.
			$doc.on('change drop', '[' + da.handler + ']:not([' + da.autoSubmit + ']) input, [' + da.handler + ']:not([' + da.autoSubmit + ']) textarea', function() {
				
				var 	$form = $(this).closest('[' + da.handler + ']'),
					handler = $form.data(Automad.util.dataCamelCase(da.handler));
				
				$('html').addClass(f.unsavedClassPrefix + handler);
				$('button[' + da.submit + '="' + handler + '"]').prop('disabled', false);
				
			});
			
			// Remove 'automad-unsaved-{handler}' class from <html> element on saving a form with a matching {handler}.
			$doc.on('submit', '[' + da.handler + ']', function(){
				
				var 	handler = $(this).data(Automad.util.dataCamelCase(da.handler));
				
				$('html').removeClass(f.unsavedClassPrefix + handler);
				
			});
			
			// Define onbeforeunload function.
			window.onbeforeunload = function() {
				
				if ($('html[class*="automad-unsaved-"]').length) {
					return 'You have unsaved chages!';
				}
				
			}
			
						
		},
		
		// Modal actions. (hide & show)
		// Reset a form when closing the wrapping modal window and refresh a form[data-automad-init] when opening.
		modalEvents: function() {
		
			var 	$modal = $('.uk-modal');
			
			// Remove all events before adding agian, since the 'ajaxComplete' event will be trigger multiple times.
			$modal.off('show.uk.modal.automad.form');
			$modal.off('hide.uk.modal.automad.form');
			
			// On.
			$modal.on({
				
				'show.uk.modal.automad.form': function(){
				
					// Update [data-automad-init] forms inside (!) a modal window.
				
					// Content can have changed after closing a modal before. When re-opening the modal,
					// the form might have outdated values inside. To update that information, 
					// forms with a 'data-automad-init' attribute must be cleared and re-submitted to
					// pull updates. 
					// Clearing the form is important to avoid auto-submitting unwanted changes 
					// before updateing the form.
					$(this).find('[' + Automad.form.dataAttr.init + ']').each(function() {
						$(this).empty().submit();
					});
					
					// Focus first input (not disabled).
					$(this).find('input:not(:disabled)').first().focus();
					
				},
				
				'hide.uk.modal.automad.form': function(){
					
					// Clear registered changes class from html and reset the form.
					$(this).find('form').each(function() {
						
						var 	handler = $(this).data(Automad.util.dataCamelCase(Automad.form.dataAttr.handler));
						
						// Remove unsaved class from html element.
						$('html').removeClass(Automad.form.unsavedClassPrefix + handler);
						
						// Reset form.
						this.reset();
						
					});
					
				}
				
			});
			
		} 
		
	};

	Automad.form.init();
		
}(window.Automad = window.Automad || {}, jQuery);