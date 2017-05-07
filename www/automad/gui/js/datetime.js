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
 *	Copyright (c) 2017 by Marc Anton Dahmen
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
		},
		
		today: function() {
			
			var 	date = new Date(),
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
				
				$datetime.val(date + ' ' + time + ':00');	
				
			}
			
		}
		
	}
	
	Automad.datetime.init();
	
}(window.Automad = window.Automad || {}, jQuery);