/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

/*
 * Init jump bar and autocomplete data.
 */

+(function (Automad, $, UIkit) {
	Automad.Jumpbar = {
		selector: '[data-am-jumpbar]',

		init: function () {
			const jumpBarForms = Array.from(
				document.querySelectorAll(`${this.selector}`)
			);

			// Get autocomplete data.
			// Note that to prevent getting data when being in page editing context,
			// the AJAX request is only submitted in case there is an actual autocomplete
			// element on the page, meaning the current context has a jumpbar.
			if (jumpBarForms.length > 0) {
				// Initially disable the input field in order to prevent normal search request before
				// autocomplete has been initialized.
				jumpBarForms.forEach((form) => {
					form.querySelector('input').setAttribute('disabled', true);
				});

				$.post(
					'?controller=UI::autocompleteJump',
					function (data) {
						const searchDataIndex = 0;
						const searchData = Object.assign(
							{},
							data.autocomplete[searchDataIndex]
						);
						const initJumpBar = (form) => {
							const input = form.querySelector('input');

							input.removeAttribute('disabled');

							const autocomplete =
								form.querySelector('.uk-autocomplete');
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
								`,
							};

							const UIkitAutocomplete = UIkit.autocomplete(
								$(autocomplete),
								options
							);
							const UIkitSearchData =
								UIkitAutocomplete.options.source[
									searchDataIndex
								];

							input.addEventListener('focus', () => {
								UIkitAutocomplete.show();
								input.dispatchEvent(new Event('keyup'));
								setTimeout(() => {
									UIkit.tooltip('.uk-tooltip').hide();
								}, 5);
							});

							input.addEventListener('keyup', () => {
								UIkitSearchData.url = `${
									searchData.url
								}&search=${encodeURIComponent(input.value)}`;
								UIkitSearchData.value = `${searchData.value} ${input.value}`;
								UIkitSearchData.subtitle = input.value;
							});

							UIkitAutocomplete.on('show.uk.autocomplete', () => {
								UIkitAutocomplete.pick('next', true);
							});

							UIkitAutocomplete.on(
								'selectitem.uk.autocomplete',
								() => {
									input.value =
										UIkitAutocomplete.selected[0].dataset.value;
									$(form).submit();
									setTimeout(() => {
										input.value = '';
									}, 20);
								}
							);
						};

						jumpBarForms.forEach((form) => {
							initJumpBar(form);
						});
					},
					'json'
				);
			}
		},
	};

	$(document).on('ready', () => {
		Automad.Jumpbar.init();
	});
})((window.Automad = window.Automad || {}), jQuery, UIkit);
