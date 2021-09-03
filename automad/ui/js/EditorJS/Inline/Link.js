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

+(function (Automad, $, UIkit) {
	Automad.inlineLink = {
		$: $,
		UIkit: UIkit,
	};
})((window.Automad = window.Automad || {}), jQuery, UIkit);

class AutomadLink extends AutomadInlineTool {
	get shortcut() {
		return 'CMD+L';
	}

	static get title() {
		return 'Link';
	}

	static get sanitize() {
		return {
			a: true,
		};
	}

	get tag() {
		return 'A';
	}

	get icon() {
		return '<path d="M18.4,12.7l-2.8-2.8c-0.6-0.6-1.3-0.8-2-0.8c-0.7,0-1.5,0.3-2,0.8L11.5,10L10,8.5l0.1-0.1c1.1-1.1,1.1-2.9,0-4L7.3,1.6 C6.7,1.1,6,0.8,5.3,0.8s-1.5,0.3-2,0.8L1.6,3.3c-1.1,1.1-1.1,2.9,0,4l2.8,2.8c0.6,0.6,1.3,0.8,2,0.8s1.5-0.3,2-0.8L8.5,10l1.5,1.5 l-0.1,0.1c-1.1,1.1-1.1,2.9,0,4l2.8,2.8c0.6,0.6,1.3,0.8,2,0.8c0.7,0,1.5-0.3,2-0.8l1.7-1.7C19.5,15.6,19.5,13.8,18.4,12.7z M7,8.7 C6.8,8.9,6.6,8.9,6.4,8.9c-0.1,0-0.4,0-0.6-0.2L3.1,5.9C2.7,5.6,2.7,5,3.1,4.7l1.7-1.7c0.2-0.2,0.4-0.2,0.6-0.2c0.1,0,0.4,0,0.6,0.2 l2.8,2.8C9,6.2,9,6.7,8.7,7L8.6,7.1L8.4,7C8,6.6,7.4,6.6,7,7S6.6,8,7,8.4L7,8.7L7,8.7z M16.9,15.3l-1.7,1.7 c-0.2,0.2-0.4,0.2-0.6,0.2s-0.4,0-0.6-0.2l-2.8-2.8c-0.3-0.3-0.3-0.8,0-1.1l0.1-0.1l0.1,0.1c0.2,0.2,0.5,0.3,0.7,0.3 c0.3,0,0.5-0.1,0.7-0.3c0.4-0.4,0.4-1.1,0-1.5l-0.1-0.1l0.1-0.1c0.2-0.2,0.4-0.2,0.6-0.2s0.4,0,0.6,0.2l2.8,2.8 C17.3,14.4,17.3,15,16.9,15.3z"/>';
	}

	get baseUrl() {
		const inPageForm = document.querySelector(
			'[data-am-inpage-controller]'
		);

		if (inPageForm) {
			const controllerUrl =
				inPageForm.dataset.amInpageController.split('?');
			return controllerUrl[0];
		}

		return '';
	}

	renderActions() {
		const create = Automad.Util.create;
		const label = create.label(AutomadLink.title);
		const script = create.element('script', []);

		script.type = 'text/autocomplete';
		script.textContent = `
			<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">
				{{~items}}
					<li data-value="{{ $item.value }}">
						<a>
							<i class="uk-icon-link"></i>&nbsp;
							{{ $item.title }}
						</a>
					</li>
				{{/items}}
			</ul>
		`;

		const autocomplete = create.element('div', [
			'uk-autocomplete',
			'uk-width-1-1',
			'uk-form',
		]);

		this.input = create.element('input', [this.cls.input]);
		this.input.type = 'text';

		this.wrapper = create.element('span', [this.cls.wrapper, 'link']);
		this.wrapper.appendChild(label);
		this.wrapper.appendChild(autocomplete);

		autocomplete.appendChild(this.input);
		autocomplete.appendChild(script);

		this.wrapper.hidden = true;

		const $ = Automad.inlineLink.$;
		const UIkit = Automad.inlineLink.UIkit;

		fetch(`${this.baseUrl}?controller=UI::autocompleteLink`, {
			method: 'post',
		})
			.then((response) => response.json())
			.then((json) => {
				const options = {
					source: json.autocomplete,
					minLength: 1,
				};

				UIkit.autocomplete($(autocomplete), options);
			});

		return this.wrapper;
	}

	showActions(node) {
		const href = node.getAttribute('href');

		this.input.value = href ? href : '';
		node.href = this.input.value;

		this.input.onchange = () => {
			node.href = this.input.value;
		};

		this.wrapper.hidden = false;
	}

	hideActions() {
		this.input.onchange = null;
		this.wrapper.hidden = true;
	}
}
