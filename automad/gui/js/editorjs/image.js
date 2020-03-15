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


class AutomadImage {

	constructor({data}) {

		this.si = Automad.selectImage;
		this.modal = UIkit.modal(this.si.modalSelector);

		this.data = {
			url: data.url || '',
			caption: data.caption || ''
		};
		
		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('cdx-block');
		this.img = document.createElement('img');
		this.caption = document.createElement('div');
		this.caption.classList.add('cdx-input');
		this.caption.contentEditable = true;
		this.caption.dataset.placeholder = 'Enter a caption';
		this.caption.innerHTML = this.data.caption;
		this.wrapper.appendChild(this.img);
		this.wrapper.appendChild(this.caption);

		this.button = document.createElement('div');
		this.button.innerHTML = '<i class="uk-icon-image uk-icon-small"></i>&nbsp;&nbsp;Select Image';
		this.button.classList.add('uk-panel', 'uk-panel-box', 'uk-text-muted', 'uk-text-center');

		var block = this;

		this.button.addEventListener('click', function () {
			block.select();
		});

	}

	static get toolbox() {

		return {
			title: 'Select Image',
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

		this.si.dialog(this.modal, false, true, function(url) {
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

		return {
			url: this.data.url,
			caption: this.caption.innerHTML
		};

	}

	onPaste(event) {

		if (event.type == 'pattern') {
			this.insertImage(event.detail.data);
		}

	}

}