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
 *	Copyright (c) 2017-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Combine UIkit datepicker and timepicker to one value. 
 */
	
+function(Automad, $) {
	
	Automad.datetime = {
		
		selector: '[data-am-datetime]',
		
		init: function() {
			
			var	$doc = $(document),
				dt = Automad.datetime;
			
			$doc.on('change', dt.selector + ' [data-uk-datepicker]', dt.update.date);
			$doc.on('change', dt.selector + ' [data-uk-timepicker]', dt.update.time);
		
			// Add .uk-active class to [data-am-datetime] when a child input is focused.
			$doc.on('focus', dt.selector + ' input', function(e) {
				
				var $input = $(e.target),
					$datetime = $input.closest(dt.selector);
				
				$datetime.addClass('uk-active');

				$input.on('blur.automad', function() {
					$datetime.removeClass('uk-active');
					$input.off('blur.automad');	
				});
			
			});
			
			// Clear button.
			$doc.on('click', dt.selector + ' [data-am-clear-date]', function() {
				
				var	$datetime = $(this).closest(dt.selector);
						
				$datetime.find('[data-uk-timepicker]').val('');
				$datetime.find('[data-uk-datepicker]').val('').trigger('change');
				
			});
					
		},
		
		today: function() {
			
			var date = new Date(),
				month = date.getMonth() + 1,
				day = date.getDate();

			return date.getFullYear() + '-' + (('' + month).length < 2 ? '0' : '') + month + '-' + (('' + day).length < 2 ? '0' : '') + day;
			
		},
		
		update: {
			
			date: function(e) {
				
				var	$datepicker = $(e.target),
					dt = Automad.datetime,
					$combo = $datepicker.closest(dt.selector),
					$datetime = $combo.find('input[type="hidden"]'),
					date = $datepicker.val(),
					$timepicker = $combo.find('[data-uk-timepicker]')
					time = $timepicker.val();
					
				if (date) {
					
					if (!time) {
						time = '12:00';
						$timepicker.val(time);
					}
					
					$datetime.val(date + ' ' + time + ':00');
					
				} else {

					$datetime.val('');
					$timepicker.val('');
					
				}
				
				$datetime.trigger('change');
					
			},
			
			time: function(e) {
				
				var	$timepicker = $(e.target),
					dt = Automad.datetime,
					$combo = $timepicker.closest(dt.selector),
					$datetime = $combo.find('input[type="hidden"]'),
					time = $timepicker.val(),
					$datepicker = $combo.find('[data-uk-datepicker]')
					date = $datepicker.val();
				
				if (!time) {
					time = '12:00';
					$timepicker.val(time);
				}
					
				if (!date) {
					date = dt.today();
					$datepicker.val(date);
				}
				
				$datetime.val(date + ' ' + time + ':00').trigger('change');
				
			}
			
		}
		
	}
	
	Automad.datetime.init();
	
}(window.Automad = window.Automad || {}, jQuery);