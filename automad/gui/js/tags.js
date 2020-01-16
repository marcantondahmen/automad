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
 *	Copyright (c) 2018-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 * 	Wrapper for Taggle.js.
 */

+function(Automad, $, UIkit) {
		
	Automad.tags = {
		
		containerId: 'am-taggle',
		inputId: 'am-input-data-tags',
		dataAttr: 'data-am-tags',
		
		init: function() {
		
			var t = Automad.tags,
				$container = $('#' + t.containerId);
			
			if ($container.length) {
				
				var	u = Automad.util,
					$input = $('#' + t.inputId),
					options = $container.data(u.dataCamelCase(t.dataAttr)),
					taggle = new Taggle(t.containerId, { 
						tags: options.tags,
						preserveCase: true,
						submitKeys: [188, 9],
						focusInputOnContainerClick: true,
						placeholder: '',
						saveOnBlur: false,
						clearOnBlur: true,
						onTagAdd: function() {
							setValue();
							$input.trigger('change');
						},
						onTagRemove: function() {
							setValue();
							$input.trigger('change');
						}
					}),
					setValue = function() {		
						$input.val(taggle.getTagValues().sort().join(', '));
					};
				
				// Initially set value to hidden input field.
				setValue();
				
				// Remove id to prevent multiple instances being created 
				// when ajaxComplete event gets triggered.
				$container.removeAttr('id');
				
				// Set up autocomplete.
				$inputWrapper = $(taggle.getInput())
								.wrap('<div class="uk-autocomplete"></div>')
								.parent();
				
				$autocomplete = UIkit.autocomplete($inputWrapper, {
					source: options.autocomplete,
					minLength: 1
				});
				
				$autocomplete.on('selectitem.uk.autocomplete', function(e, data, acobject) {
					taggle.add(data.value);
				});
				
				// Clear input on change.
				$(taggle.getInput()).on('change', function() {
					$(this).val('');					
				});
				
			}
			
		}
		
	}
	
	$(document).on('ajaxComplete', Automad.tags.init);
	
}(window.Automad = window.Automad || {}, jQuery, UIkit);