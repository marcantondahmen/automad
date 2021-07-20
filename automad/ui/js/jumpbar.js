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
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


/*
 *	Init jump bar and autocomplete data.
 */

+function(Automad, $, UIkit) {

	Automad.jumpBar = {
		
		selector: '[data-am-jumpbar]',
		
		init: function() {

			const dashboard = document.querySelector('.am-dashboard');

			// Get autocomplete data.
			// Note that to prevent getting data when being in page editing context, 
			// the AJAX request is only submitted in case there is an actual autocomplete 
			// element on the page, meaning the current context is the dashboard.
			if (dashboard) {

				const jumpBarForms = Array.from(
					document.querySelectorAll(`${Automad.jumpBar.selector}`)
				);
				
				// Initially disable the input field in order to prevent normal search request before
				// autocomplete has been initialized.
				jumpBarForms.forEach((form) => {
					form.querySelector('input').setAttribute('disabled', true);
				});

				$.post('?controller=UI::autocompleteJump', function (data) {

					const initJumpBar = (form) => {

						const input = form.querySelector('input');

						const options = {
								source: data.autocomplete,
								minLength: 0,
								delay: 0,
								template: `
								<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">
									{{~items}}
										<li data-value="{{$item.url}}">
											<a>
												<i class="uk-icon-{{$item.icon}} uk-icon-justify"></i>
												<span>{{$item.title}}</span>
												<span>{{$item.subtitle}}</span>
											</a>
										</li>
									{{/items}}
								</ul>
							`
						};

						input.removeAttribute('disabled');

						const UIkitAutocomplete = UIkit.autocomplete($(form.querySelector('.uk-autocomplete')), options);

						input.addEventListener('focus', () => {

							UIkitAutocomplete.show();
							input.dispatchEvent(new Event('keyup'));

						});

						UIkitAutocomplete.on('selectitem.uk.autocomplete', () => {

							input.value = UIkitAutocomplete.selected[0].dataset.value;
							
							$(form).submit();

							setTimeout(() => {
								input.value = '';
							}, 50);

						});

					}

					jumpBarForms.forEach((form) => { initJumpBar(form); });

				}, 'json');

			}

		}
		
	};
	
	$(document).on('ready', Automad.jumpBar.init);
	
}(window.Automad = window.Automad || {}, jQuery, UIkit);