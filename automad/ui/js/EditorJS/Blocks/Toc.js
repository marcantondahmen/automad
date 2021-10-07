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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

class AutomadBlockToc {
	static get contentless() {
		return true;
	}

	static get isReadOnlySupported() {
		return true;
	}

	static get toolbox() {
		return {
			icon: '<svg width="17px" height="13px" x="0px" y="0px" viewBox="0 0 17 13"><path d="M3.38,5.3h10.25c0.62,0,1.12,0.5,1.12,1.12s-0.5,1.12-1.12,1.12H3.38c-0.62,0-1.12-0.5-1.12-1.12S2.75,5.3,3.38,5.3z"/><path d="M1.38,0.45h10.25c0.62,0,1.12,0.5,1.12,1.12s-0.5,1.12-1.12,1.12H1.38c-0.62,0-1.12-0.5-1.12-1.12S0.75,0.45,1.38,0.45z"/><path d="M5.38,10.3h10.25c0.62,0,1.12,0.5,1.12,1.12s-0.5,1.12-1.12,1.12H5.38c-0.62,0-1.12-0.5-1.12-1.12S4.75,10.3,5.38,10.3z"/></svg>',
			title: AutomadEditorTranslation.get('toc'),
		};
	}

	constructor({ data, api }) {
		this.data = {
			style: data.style || 'ordered',
		};

		this.api = api;

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.innerHTML = `
			<div class="am-block-icon">${AutomadBlockToc.toolbox.icon}</div>
			<div class="am-block-title">${AutomadBlockToc.toolbox.title}</div>
		`;

		this.settings = [
			{
				name: 'ordered',
				title: AutomadEditorTranslation.get('list_ordered'),
				icon: AutomadEditorIcons.get.listOrdered,
			},
			{
				name: 'unordered',
				title: AutomadEditorTranslation.get('list_unordered'),
				icon: AutomadEditorIcons.get.listUnordered,
			},
		];
	}

	render() {
		return this.wrapper;
	}

	renderSettings() {
		var wrapper = document.createElement('div'),
			inner = document.createElement('div'),
			block = this;

		inner.classList.add(this.CSS.settingsWrapper);

		this.settings.forEach(function (tune) {
			var button = document.createElement('div');

			button.classList.add(block.CSS.settingsButton);
			button.classList.toggle(
				block.CSS.settingsButtonActive,
				block.data.style == tune.name
			);
			button.innerHTML = tune.icon;
			inner.appendChild(button);

			button.addEventListener('click', function () {
				block.toggleTune(tune);

				// Clear all buttons.
				const buttons = inner.parentNode.querySelectorAll(
					'.' + block.CSS.settingsButton
				);

				Array.from(buttons).forEach((_button) =>
					_button.classList.remove(block.CSS.settingsButtonActive)
				);

				// Make current active.
				button.classList.toggle(block.CSS.settingsButtonActive);
			});

			block.api.tooltip.onHover(button, tune.title, { placement: 'top' });
		});

		wrapper.appendChild(inner);

		return wrapper;
	}

	save() {
		return this.data;
	}

	toggleTune(tune) {
		this.data.style = tune.name;
	}

	get CSS() {
		return {
			settingsWrapper: 'cdx-settings-1-2',
			settingsButton: 'cdx-settings-button',
			settingsButtonActive: 'cdx-settings-button--active',
		};
	}
}
