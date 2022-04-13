/*
 *	Baker
 *
 *	Copyright (c) 2017-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *  MIT license
 */


+function(baker, $, UIkit) {
		
	baker.autocomplete = {

		selectors: {
			submit: '[data-baker-autocomplete-submit]'
		},

		submitForm: function (e) {

			e.preventDefault();

			var $form = $(e.target).closest('form');

			// Set timeout to make sure that the selected dropdown item is passed as value.
			setTimeout(function () {
				$form.submit();
			}, 50);

		}


	}

	// Submit autocomplete form on hitting the return key.
	$(document).on('keydown', baker.autocomplete.selectors.submit + ' .uk-autocomplete input[type="search"]', function (e) {

		if (e.which == 13) {
			baker.autocomplete.submitForm(e);
		}

	});

	// Submit form when selecting an autocomplete value (navbar only).
	$(document).on('click', baker.autocomplete.selectors.submit + ' .uk-dropdown a', baker.autocomplete.submitForm);

	// UIkit options.
	UIkit.on('beforeready.uk.dom', function(){
		$.extend(UIkit.components.autocomplete.prototype.defaults, {
			minLength: 2,
			template: '<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">{{~items}}<li data-value="{{$item.url}}"><a>{{$item.value}}<span class="uk-text-muted"><br class="uk-visible-small" /><span class="uk-hidden-small"> &mdash; </span>{{$item.parent}}</span></a></li>{{/items}}</ul>'
		});
	});

}(window.baker = window.baker || {}, jQuery, UIkit);