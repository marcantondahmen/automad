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


class AutomadButtons {

	constructor({data, api}) {

		var create = Automad.util.create;

		this.api = api;

		this.data = {
			primaryText: data.primaryText || '',
			primaryLink: data.primaryLink || '',
			secondaryText: data.secondaryText || '',
			secondaryLink: data.secondaryLink || '',
			alignment: data.alignment || 'left'
		};

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.innerHTML = `
			<div class="am-block-icon">${AutomadButtons.toolbox.icon}</div>
			<div class="am-block-title">${AutomadButtons.toolbox.title}</div>
			<hr>
			<ul class="uk-grid uk-grid-width-medium-1-2">
				<li>
					${create.label('Primary Button Text').outerHTML}
					${create.editable(['cdx-input', 'am-block-primary-text'], '', this.data.primaryText).outerHTML}
					${create.label('Primary Button Link').outerHTML}
					<div class="am-form-icon-button-input uk-flex">
						<button type="button" class="uk-button uk-button-large">
							<i class="uk-icon-link"></i>
						</button>
						<input type="text" class="am-block-primary-link uk-form-controls uk-width-1-1" value="${this.data.primaryLink}" />
					</div>
				</li>
				<li>
					${create.label('Secondary Button Text').outerHTML}
					${create.editable(['cdx-input', 'am-block-secondary-text'], '', this.data.secondaryText).outerHTML}
					${create.label('Secondary Button Link').outerHTML}
					<div class="am-form-icon-button-input uk-flex">
						<button type="button" class="uk-button uk-button-large">
							<i class="uk-icon-link"></i>
						</button>
						<input type="text" class="am-block-secondary-link uk-form-controls uk-width-1-1" value="${this.data.secondaryLink}" />
					</div>
				</li>
			</ul>`;

		var linkButtons = this.wrapper.querySelectorAll('button');

		for (let i = 0; i < linkButtons.length; ++i) {
			api.listeners.on(linkButtons[i], 'click', function() {
				Automad.link.click(linkButtons[i]);
			});
		}
		
		this.inputs = {
			primaryText: this.wrapper.querySelector('.am-block-primary-text'),
			primaryLink: this.wrapper.querySelector('.am-block-primary-link'),
			secondaryText: this.wrapper.querySelector('.am-block-secondary-text'),
			secondaryLink: this.wrapper.querySelector('.am-block-secondary-link')
		}

		this.settings = [
			{
				name: 'left',
				icon: `<svg width="16" height="11" viewBox="0 0 16 11"><path d="M1.069 0H13.33a1.069 1.069 0 0 1 0 2.138H1.07a1.069 1.069 0 1 1 0-2.138zm0 4.275H9.03a1.069 1.069 0 1 1 0 2.137H1.07a1.069 1.069 0 1 1 0-2.137zm0 4.275h9.812a1.069 1.069 0 0 1 0 2.137H1.07a1.069 1.069 0 0 1 0-2.137z" /></svg>`
			},
			{
				name: 'center',
				icon: `<svg width="16" height="11" viewBox="0 0 16 11"><path d="M1.069 0H13.33a1.069 1.069 0 0 1 0 2.138H1.07a1.069 1.069 0 1 1 0-2.138zm3.15 4.275h5.962a1.069 1.069 0 0 1 0 2.137H4.22a1.069 1.069 0 1 1 0-2.137zM1.069 8.55H13.33a1.069 1.069 0 0 1 0 2.137H1.07a1.069 1.069 0 0 1 0-2.137z"/></svg>`
			}
		]
		
	}

	static get sanitize() {

		return {
			primaryText: {},
			primaryLink: false,
			secondaryText: {},
			secondaryLink: false
		}

	}

	static get toolbox() {

		return {
			title: 'Buttons',
			icon: '<svg xmlns="http://www.w3.org/2000/svg" width="18px" height="15px" viewBox="0 0 18 15"><path d="M16,2.359c0,0,0,0.001,0,0.002C15,0.972,13.623,0,12,0H4C1.791,0,0,1.791,0,4v5c0,1.624,0.972,3,2.362,4 c-0.001,0-0.001,0-0.002,0C2.987,14,4.377,15,6,15h8c2.209,0,4-1.791,4-4V6C18,4.377,17,2.987,16,2.359z M2,4c0-1.103,0.897-2,2-2h8 c1.103,0,2,0.897,2,2v5c0,1.103-0.897,2-2,2H4c-1.103,0-2-0.897-2-2V4z"/><path d="M6,8H5C4.171,8,3.5,7.329,3.5,6.5S4.171,5,5,5h1c0.828,0,1.5,0.671,1.5,1.5S6.828,8,6,8z"/><path d="M11,8h-1C9.172,8,8.5,7.329,8.5,6.5S9.172,5,10,5h1c0.828,0,1.5,0.671,1.5,1.5S11.828,8,11,8z"/></svg>'
		};

	}

	render() {

		return this.wrapper;

	}

	save() {

		return Object.assign(this.data, {
			primaryText: this.inputs.primaryText.innerHTML,
			primaryLink: this.inputs.primaryLink.value.trim(),
			secondaryText: this.inputs.secondaryText.innerHTML,
			secondaryLink: this.inputs.secondaryLink.value.trim()
		});

	}

	renderSettings() {

		var wrapper = document.createElement('div'),
			block = this;

		wrapper.classList.add('cdx-settings-1-2');

		this.settings.map(function(tune) {

			var el = document.createElement('div');

			el.innerHTML = tune.icon;
			el.classList.add(block.api.styles.settingsButton);
			el.classList.toggle(block.api.styles.settingsButtonActive, tune.name === block.data.alignment);

			wrapper.appendChild(el);

			return el;

		}).forEach(function(element, index, elements) {

			element.addEventListener('click', function() {

				block.toggleTune(block.settings[index].name);

				elements.forEach((el, i) => {

					var name = block.settings[i].name;

					el.classList.toggle(block.api.styles.settingsButtonActive, name === block.data.alignment);
				
				});
			});

		});

		return wrapper;

	}

	toggleTune(tune) {
		this.data.alignment = tune;
	}

}