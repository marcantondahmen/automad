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
 *	Search and replace form handling. 
 */
	
+function(Automad, $, UIkit) {
	
	Automad.search = {
		
		form: null,
		
		init: function() {
			
			const container = document.querySelector('[data-am-search]');
			
			if (!container) {
				return false;
			}
			
			const searchField = container.querySelector('[name="searchValue"]');
			const replaceField = container.querySelector('[name="replaceValue"]');
			const replaceButton = container.querySelector('[name="replaceSelected"]');
			const checkAllButton = container.querySelector('[name="checkAll"]');
			const unCheckAllButton = container.querySelector('[name="unCheckAll"]');
			const regexCheckbox = container.querySelector('[name="isRegex"]')
			
			this.form = container.querySelector('form');
			
			searchField.addEventListener('keyup', UIkit.Utils.debounce(() => {
				this.search(searchField.value, regexCheckbox.checked);
			}, 200));

			regexCheckbox.addEventListener('change', () => {
				this.search(searchField.value, regexCheckbox.checked);
			});

			replaceButton.addEventListener('click', () => {
				this.submit([
					{ name: 'searchValue', value: searchField.value },
					{ name: 'replaceValue', value: replaceField.value },
					{ name: 'replaceSelected', value: true },
					{ name: 'isRegex', value: regexCheckbox.checked ? 1 : 0 }
				])
			});

			this.search(searchField.value, regexCheckbox.checked);

			checkAllButton.addEventListener('click', () => { this.toggleAll(container, true)});
			unCheckAllButton.addEventListener('click', () => { this.toggleAll(container, false)});
			
		},

		search: function(value, isRegex) {

			if (history.pushState) {

				const url = window.location.href.replace(
					/(&search=.*)?$/,
					`&search=${encodeURIComponent(value)}`
				);

				window.history.pushState({ path: url }, '', url);

			}

			this.submit([
				{ name: 'searchValue', value: value },
				{ name: 'isRegex', value: isRegex ? 1 : 0 }
			]);

		},

		submit: function(fields) {

			const formData = new FormData(this.form);

			fields.forEach((field) => {
				formData.append(field.name, field.value);
			});

			fetch('?controller=Search::searchAndReplace', {
				method: 'POST',
				body: formData
			})
			.then((response) => response.json())
			.then((json) => {
				this.form.innerHTML = json.html;
			});

		},

		toggleAll: function(container, state) {

			const checkboxes = container.querySelectorAll('[data-am-toggle] > input');

			Array.from(checkboxes).forEach((box) => {
				box.checked = state;
				Automad.toggle.update($(box));
			});

		}
		
	}
	
	$(document).on('ready', () => { Automad.search.init(); });
	
}(window.Automad = window.Automad || {}, jQuery, UIkit);