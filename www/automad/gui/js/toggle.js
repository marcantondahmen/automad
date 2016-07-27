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
 *	Handle custom checkboxes/radios (label-input combinations). 
 *	An active class gets added/removed to the label when a checkbox/radio gets checked/unchecked.
 *
 * 	It is also possible to set data-automad-toggle to an ID of another element 
 * 	to also toggle visibility of that element according to the checkbox status.
 * 
 *	To toggle the active class on a label, it must have a "data-automad-toggle"
 *	attribute and the input must be placed inside. 
 *	The full markup must look like this:
 *
 *	<label data-automad-toggle>
 *		<input type="checkbox" name="..." />
 *	</label>
 */

+function(Automad, $) {

	Automad.toggle = {
		
		labelDataAttr: 'data-automad-toggle',
		activeClass: 'uk-active',

		// Toggle checkboxes.
		checkbox: function(e) {
				
			Automad.toggle.update($(e.target));
	
		},

		// Initially update all inputs with a data-automad-toggle attribute
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
		
			var 	active = Automad.toggle.activeClass,
				$label = $input.parent(),
				toggleContainer = $label.data(Automad.util.dataCamelCase(Automad.toggle.labelDataAttr));
		
			// Update label.
			if ($input.is(':checked')) {
				$label.addClass(active);
			} else {
				$label.removeClass(active);
			}
			
			// Update optional container.
			if (toggleContainer) {
				
				var 	$toggleContainer = $(toggleContainer);
				
				if ($input.is(':checked')) {
					$toggleContainer.show();
				} else {
					$toggleContainer.hide();
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