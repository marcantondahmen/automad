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
 *	Copyright (c) 2016-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*	
 *	Handle custom checkboxes/radios (label-input combinations). 
 *	An active class gets added/removed to the label when a checkbox/radio gets checked/unchecked.
 *
 * 	It is also possible to set data-am-toggle to an ID of another element 
 * 	to also toggle visibility of that element according to the checkbox status.
 * 
 *	To toggle the active class on a label, it must have a "data-am-toggle"
 *	attribute and the input must be placed inside. 
 *	The full markup must look like this:
 *
 *	<label data-am-toggle>
 *		<input type="checkbox" name="..." />
 *	</label>
 */

+function(Automad, $) {

	Automad.toggle = {
		
		labelDataAttr: 'data-am-toggle',
		
		class: {
			active: 'uk-active'
		},

		// Toggle checkboxes.
		checkbox: function(e) {
				
			Automad.toggle.update($(e.target));
	
		},

		// Initially update all inputs with a data-am-toggle attribute
		// to update visibility status of related containers and labels.
		init: function() {
			
			$('[' + Automad.toggle.labelDataAttr + '] input').each(function(){
				Automad.toggle.update($(this));
			});
			
		},
		
		// Toggle radio inputs and its related siblings.
		radio: function(e) {
			
			// Find all radio inputs with the same name as the triggering element and update them all.
			$('input[name="' + $(e.target).attr('name') + '"]').each(function() {
				Automad.toggle.update($(this));
			});
				
		},

		// Update the label of a nested input and the visibility of an optional element.
		update: function($input) {
		
			var t = Automad.toggle,
				$label = $input.parent(),
				toggleContainer = $label.data(Automad.util.dataCamelCase(t.labelDataAttr));
		
			// Update label.
			if ($input.is(':checked')) {
				$label.addClass(t.class.active);
			} else {
				$label.removeClass(t.class.active);
			}
			
			// Update optional container.
			if (toggleContainer) {
				
				var $toggleContainer = $(toggleContainer);
								
				if ($input.is(':checked')) {
					$toggleContainer.addClass(t.class.active);
				} else {
					$toggleContainer.removeClass(t.class.active);
				}
				
			}
			
		}

	}
	
	// Handle events for checkboxes and radio inputs separately.
	$(document).on('change', '[' + Automad.toggle.labelDataAttr + '] [type="checkbox"]', Automad.toggle.checkbox);
	$(document).on('change', '[' + Automad.toggle.labelDataAttr + '] [type="radio"]', Automad.toggle.radio);
	
	// Initially check visibility status of related containers.
	$(document).on('ready ajaxComplete', Automad.toggle.init);
	
}(window.Automad = window.Automad || {}, jQuery);