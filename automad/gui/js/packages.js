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
 *	Copyright (c) 2019-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Handle all requests and events related to the package manager. 
 */

+function(Automad, $, UIkit) {
	
	Automad.packages = {

		dataAttr: {
			'packages': 'data-am-packages',
			'filter': 'data-am-packages-filter'
		},

		composerDone: function() {

			$('html').on('composerDone', function() {
				
				// Refresh packages list.
				Automad.packages.get();

				// Close open modal.
				window.setTimeout(function() {
					UIkit.modal('.uk-modal.uk-open').hide();
				}, 500);
				
			});

		},

		filter: function() {

			var p = Automad.packages,
				$input = $('[' + p.dataAttr.filter + ']'),

				update = function() {

					var filter = $input.val().toLowerCase().split(' ');

					$('[' + p.dataAttr.packages + ']').find('li').filter(function () {
						var text = $(this).text().toLowerCase(),
							display = true;

						for (var i = 0; i < filter.length; i++) {
							if (text.indexOf(filter[i]) == -1) {
								display = false;
								break;
							}
						}

						$(this).toggle(display);
						$(window).trigger('resize');

					});

				};

			// Trigger filter also when packages got reloaded.
			$(document).ajaxComplete(function (e, xhr, settings) {
				
				if (settings.url == '?ajax=get_packages') {
					update();
				}
				
			});

			$input.on('keyup', UIkit.Utils.debounce(update, 250)); 

		},

		get: function() {

			var p = Automad.packages;

			$('[' + p.dataAttr.packages + ']').each(function () {

				var $this = $(this);

				$.get('?ajax=get_packages', function (data) {

					if (data.html) {

						$this.html(data.html);

						$.get('?ajax=outdated_packages', function (data) {

							if (data.buffer) {

								try {

									var json = JSON.parse(data.buffer),
										outdated = json.installed;

									for (let i = 0; i < outdated.length; i++) {
										$('[data-package="' + outdated[i].name + '"] form.uk-hidden')
										.removeClass('uk-hidden');
									}

								} catch (e) {

									return false;

								}

							}
							
						}, 'json');

					}

					if (data.error) {
						$this.html('<div class="uk-alert uk-alert-danger">' + data.error + '</div>');
					}

				}, 'json');

			});

		},

		init: function () {

			Automad.packages.composerDone();
			Automad.packages.get();
			Automad.packages.filter();

		}

	}

	$(document).on('ready', Automad.packages.init);

}(window.Automad = window.Automad || {}, jQuery, UIkit);	