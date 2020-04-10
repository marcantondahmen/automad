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


+function (Automad, UIkit) {

	Automad.imageBlock = {
		
		UIkit: UIkit

	}

}(window.Automad = window.Automad || {}, UIkit);

class AutomadImage {

	constructor({data}) {

		this.data = {
			url: data.url || '',
			caption: data.caption || '',
			stretched: data.stretched !== undefined ? data.stretched : false
		};
		
		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('cdx-block');
		this.img = document.createElement('img');
		this.caption = Automad.util.create.editable(['cdx-input'], 'Enter a caption', this.data.caption);

		this.wrapper.appendChild(this.img);
		this.wrapper.appendChild(this.caption);

		this.button = document.createElement('div');
		this.button.innerHTML = '<i class="uk-icon-image uk-icon-small"></i>&nbsp;&nbsp;Select Image';
		this.button.classList.add('uk-panel', 'uk-panel-box', 'uk-text-muted', 'uk-text-center');

		var block = this;

		this.button.addEventListener('click', function () {
			block.select();
		});

		this.settings = [{
			name: 'stretched',
			icon: '<svg width="17" height="10" viewBox="0 0 17 10" xmlns="http://www.w3.org/2000/svg"><path d="M13.568 5.925H4.056l1.703 1.703a1.125 1.125 0 0 1-1.59 1.591L.962 6.014A1.069 1.069 0 0 1 .588 4.26L4.38.469a1.069 1.069 0 0 1 1.512 1.511L4.084 3.787h9.606l-1.85-1.85a1.069 1.069 0 1 1 1.512-1.51l3.792 3.791a1.069 1.069 0 0 1-.475 1.788L13.514 9.16a1.125 1.125 0 0 1-1.59-1.591l1.644-1.644z"/></svg>'
		}];

	}

	static get toolbox() {

		return {
			title: 'Image',
			icon: '<svg width="17" height="15" viewBox="0 0 336 276" xmlns="http://www.w3.org/2000/svg"><path d="M291 150V79c0-19-15-34-34-34H79c-19 0-34 15-34 34v42l67-44 81 72 56-29 42 30zm0 52l-43-30-56 30-81-67-66 39v23c0 19 15 34 34 34h178c17 0 31-13 34-29zM79 0h178c44 0 79 35 79 79v118c0 44-35 79-79 79H79c-44 0-79-35-79-79V79C0 35 35 0 79 0z"/></svg>'
		};

	}

	static get pasteConfig() {

		return {
			patterns: {
				image: /(https?:\/\/)?\S+\.(gif|jpe?g|tiff|png)$/i
			}
		}

	}

	insertImage(url) {

		if (url) {
			this.img.src = Automad.util.resolvePath(url);
			this.data.url = url;
			this.button.parentNode.replaceChild(this.wrapper, this.button);
		}

	}

	select() {

		var block = this;

		Automad.selectImage.dialog(false, true, function(url) {
			block.insertImage(url);
		});

	}

	appendCallback() {

		this.select();

	}

	render() {

		if (this.data && this.data.url) {
			this.img.src = Automad.util.resolvePath(this.data.url);
			return this.wrapper;
		} else {
			return this.button;
		}

	}

	save() {

		return Object.assign(this.data, {
			url: this.data.url,
			caption: this.caption.innerHTML
		});

	}

	onPaste(event) {

		if (event.type == 'pattern') {
			this.insertImage(event.detail.data);
		}

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
			});

		});

		return wrapper;

	}

	toggleTune(tune) {

		this.data[tune] = !this.data[tune];
		Automad.util.triggerBlockChange(this.wrapper);

	}

}