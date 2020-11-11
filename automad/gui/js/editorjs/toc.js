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
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


class AutomadToc {

	static get isReadOnlySupported() {
		return true;
	}

	static get contentless() {
		return true;
	}

	constructor({ data, config, api }) {

		this.data = {
			key: config.key,
			style: data.style || 'ordered'
		}

		this.api = api;

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.innerHTML = `
			<div class="am-block-icon">${AutomadToc.toolbox.icon}</div>
			<div class="am-block-title">${AutomadToc.toolbox.title}</div>
		`;

		this.settings = [
			{
				name: 'ordered',
				title: 'Ordered',
				icon: '<svg width="17" height="13" viewBox="0 0 17 13"><path d="M5.819 4.607h9.362a1.069 1.069 0 0 1 0 2.138H5.82a1.069 1.069 0 1 1 0-2.138zm0-4.607h9.362a1.069 1.069 0 0 1 0 2.138H5.82a1.069 1.069 0 1 1 0-2.138zm0 9.357h9.362a1.069 1.069 0 0 1 0 2.138H5.82a1.069 1.069 0 0 1 0-2.137zM1.468 4.155V1.33c-.554.404-.926.606-1.118.606a.338.338 0 0 1-.244-.104A.327.327 0 0 1 0 1.59c0-.107.035-.184.105-.234.07-.05.192-.114.369-.192.264-.118.475-.243.633-.373.158-.13.298-.276.42-.438a3.94 3.94 0 0 1 .238-.298C1.802.019 1.872 0 1.975 0c.115 0 .208.042.277.127.07.085.105.202.105.351v3.556c0 .416-.15.624-.448.624a.421.421 0 0 1-.32-.127c-.08-.085-.121-.21-.121-.376zm-.283 6.664h1.572c.156 0 .275.03.358.091a.294.294 0 0 1 .123.25.323.323 0 0 1-.098.238c-.065.065-.164.097-.296.097H.629a.494.494 0 0 1-.353-.119.372.372 0 0 1-.126-.28c0-.068.027-.16.081-.273a.977.977 0 0 1 .178-.268c.267-.264.507-.49.722-.678.215-.188.368-.312.46-.371.165-.11.302-.222.412-.334.109-.112.192-.226.25-.344a.786.786 0 0 0 .085-.345.6.6 0 0 0-.341-.553.75.75 0 0 0-.345-.08c-.263 0-.47.11-.62.329-.02.029-.054.107-.101.235a.966.966 0 0 1-.16.295c-.059.069-.145.103-.26.103a.348.348 0 0 1-.25-.094.34.34 0 0 1-.099-.258c0-.132.031-.27.093-.413.063-.143.155-.273.279-.39.123-.116.28-.21.47-.282.189-.072.411-.107.666-.107.307 0 .569.045.786.137a1.182 1.182 0 0 1 .618.623 1.18 1.18 0 0 1-.096 1.083 2.03 2.03 0 0 1-.378.457c-.128.11-.344.282-.646.517-.302.235-.509.417-.621.547a1.637 1.637 0 0 0-.148.187z"/></svg>'
			},
			{
				name: 'unordered',
				title: 'Unordered',
				icon: '<svg width="17" height="13" viewBox="0 0 17 13"><path d="M5.625 4.85h9.25a1.125 1.125 0 0 1 0 2.25h-9.25a1.125 1.125 0 0 1 0-2.25zm0-4.85h9.25a1.125 1.125 0 0 1 0 2.25h-9.25a1.125 1.125 0 0 1 0-2.25zm0 9.85h9.25a1.125 1.125 0 0 1 0 2.25h-9.25a1.125 1.125 0 0 1 0-2.25zm-4.5-5a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25zm0-4.85a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25zm0 9.85a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25z"/></svg>'
			}
		];

	}

	render() {
		return this.wrapper;
	}

	renderSettings() {

		var wrapper = document.createElement('div'),
			block = this;

		wrapper.classList.add(this.CSS.settingsWrapper);

		this.settings.forEach(function (tune) {

			var button = document.createElement('div');

			button.classList.add(block.CSS.settingsButton);
			button.classList.toggle(block.CSS.settingsButtonActive, (block.data.style == tune.name));
			button.innerHTML = tune.icon;
			wrapper.appendChild(button);

			button.addEventListener('click', function () {
				
				block.toggleTune(tune);
				
				// Clear all buttons.
				const buttons = wrapper.parentNode.querySelectorAll('.' + block.CSS.settingsButton);

				Array.from(buttons).forEach((_button) =>
					_button.classList.remove(block.CSS.settingsButtonActive)
				);

				// Make current active.
				button.classList.toggle(block.CSS.settingsButtonActive);

			});

			block.api.tooltip.onHover(button, tune.title, { placement: 'top' });

		});

		return wrapper;

	}

	save() {
		return this.data;
	}

	toggleTune(tune) {
		this.data.style = tune.name;
	}

	static get toolbox() {
		return {
			icon: '<svg width="17px" height="13px" x="0px" y="0px" viewBox="0 0 17 13"><path d="M3.38,5.3h10.25c0.62,0,1.12,0.5,1.12,1.12s-0.5,1.12-1.12,1.12H3.38c-0.62,0-1.12-0.5-1.12-1.12S2.75,5.3,3.38,5.3z"/><path d="M1.38,0.45h10.25c0.62,0,1.12,0.5,1.12,1.12s-0.5,1.12-1.12,1.12H1.38c-0.62,0-1.12-0.5-1.12-1.12S0.75,0.45,1.38,0.45z"/><path d="M5.38,10.3h10.25c0.62,0,1.12,0.5,1.12,1.12s-0.5,1.12-1.12,1.12H5.38c-0.62,0-1.12-0.5-1.12-1.12S4.75,10.3,5.38,10.3z"/></svg>',
			title: 'Table of Contents'
		};
	}

	get CSS() {
		return {
			settingsWrapper: 'cdx-settings-1-2',
			settingsButton: 'cdx-settings-button',
			settingsButtonActive: 'cdx-settings-button--active'
		};
	}

}