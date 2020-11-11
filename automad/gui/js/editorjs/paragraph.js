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


/**
 *	This block is based on the original paragraph block of editor.js
 *	https://github.com/editor-js/paragraph
 */

class AutomadParagraph {
	
	constructor({ data, api }) {

		var large = data.large !== undefined ? data.large : false;

		this.api = api;

		this._CSS = {
			block: this.api.styles.block,
			wrapper: 'ce-paragraph',
			large: 'am-paragraph-large'
		};

		this._data = {};
		this.onKeyUp = this.onKeyUp.bind(this);
		this.input = this.drawView(large);
		
		this.data = {
			text: data.text || '',
			large: large
		};

		this.settings = [{
			name: 'large',
			title: 'Large',
			icon: '<svg width="24px" height="14px" viewBox="0 0 24 14"><path d="M12.815,11.695L7.609,1.283c-0.387-0.771-1.651-0.771-2.038,0L0.365,11.695c-0.281,0.562-0.054,1.246,0.509,1.527 c0.562,0.287,1.247,0.057,1.528-0.508l0.987-1.975h6.402l0.986,1.975c0.2,0.398,0.602,0.629,1.02,0.629 c0.173,0,0.346-0.038,0.508-0.121C12.869,12.941,13.095,12.258,12.815,11.695z M4.528,8.463l2.061-4.124l2.063,4.124H4.528z"/><path d="M23.416,4.887L19.6,1.11c-0.208-0.271-0.52-0.456-0.889-0.456c-0.368,0-0.68,0.186-0.889,0.456l-3.816,3.777 c-0.447,0.442-0.451,1.165-0.01,1.611c0.445,0.445,1.164,0.453,1.611,0.01l1.965-1.946v7.643c0,0.629,0.51,1.139,1.139,1.139 s1.139-0.51,1.139-1.139V4.562l1.966,1.946c0.221,0.219,0.511,0.328,0.8,0.328c0.295,0,0.588-0.113,0.811-0.338 C23.868,6.052,23.864,5.33,23.416,4.887z"/></svg>'
		}];

	}

	onKeyUp(e) {

		if (e.code !== 'Backspace' && e.code !== 'Delete') {
			return;
		}

		const { textContent } = this.input;

		if (textContent === '') {
			this.input.innerHTML = '';
		}

	}

	drawView(large) {

		var div = Automad.util.create.editable([this._CSS.wrapper, this._CSS.block], '', '');

		if (large) {
			div.classList.add(this._CSS.large);
		}

		div.addEventListener('keyup', this.onKeyUp);

		return div;

	}

	render() {

		return this.input;

	}

	merge(data) {

		let newData = {
			text: this.data.text + data.text
		};

		this.data = newData;

	}

	validate(savedData) {

		if (savedData.text.trim() === '') {
			return false;
		}

		return true;

	}

	save() {

		return Object.assign(this.data, {
			text: this.input.innerHTML.replace(/\<br\>$/, '')
		});

	}

	onPaste(event) {

		const data = {
			text: event.detail.data.innerHTML
		};

		this.data = data;

	}

	get data() {

		let text = this.input.innerHTML;

		this._data.text = text;

		return this._data;

	}

	set data(data) {

		this._data = data || {};
		this.input.innerHTML = this._data.text || '';

	}

	renderSettings() {

		var wrapper = document.createElement('div'),
			block = this;

		wrapper.classList.add('cdx-settings-1-1');

		this.settings.forEach(function (tune) {

			var button = document.createElement('div');

			button.classList.add('cdx-settings-button');
			button.classList.toggle('cdx-settings-button--active', block.data[tune.name]);
			button.innerHTML = tune.icon;
			wrapper.appendChild(button);

			button.addEventListener('click', function () {
				block.toggleTune(tune.name);
				button.classList.toggle('cdx-settings-button--active');
				block.input.classList.toggle(block._CSS.large);
			});

			block.api.tooltip.onHover(button, tune.title, { placement: 'top' });

		});

		return wrapper;

	}

	toggleTune(tune) {

		this.data[tune] = !this.data[tune];

	}

	static get conversionConfig() {
		return {
			export: 'text', 
			import: 'text' 
		};
	}

	static get sanitize() {

		return {
			text: {
				br: true,
			}
		};

	}

	static get pasteConfig() {

		return {
			tags: ['P']
		};

	}

	static get toolbox() {

		return {
			icon: '<svg viewBox="0.2 -0.3 9 11.4" width="12" height="14"><path d="M0 2.77V.92A1 1 0 01.2.28C.35.1.56 0 .83 0h7.66c.28.01.48.1.63.28.14.17.21.38.21.64v1.85c0 .26-.08.48-.23.66-.15.17-.37.26-.66.26-.28 0-.5-.09-.64-.26a1 1 0 01-.21-.66V1.69H5.6v7.58h.5c.25 0 .45.08.6.23.17.16.25.35.25.6s-.08.45-.24.6a.87.87 0 01-.62.22H3.21a.87.87 0 01-.61-.22.78.78 0 01-.24-.6c0-.25.08-.44.24-.6a.85.85 0 01.61-.23h.5V1.7H1.73v1.08c0 .26-.08.48-.23.66-.15.17-.37.26-.66.26-.28 0-.5-.09-.64-.26A1 1 0 010 2.77z"/></svg>',
			title: 'Text'
		};

	}

}
